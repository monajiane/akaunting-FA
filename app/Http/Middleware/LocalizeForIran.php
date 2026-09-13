<?php

namespace App\Http\Middleware;

use App\Utilities\Persian;
use Closure;
use Illuminate\Http\Request;

class LocalizeForIran
{
    /**
     * When the current locale is fa-IR:
     *   - Convert Latin/Eastern-Arabic digits in HTML responses to Persian digits
     *   - Set the Content-Language header to fa-IR
     *
     * Digits inside <script>, <style>, <pre>, <code> and HTML attributes are
     * protected so that JSON, URLs and form values keep their Latin digits.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (app()->getLocale() !== 'fa-IR') {
            return $response;
        }

        $response->headers->set('Content-Language', 'fa-IR');

        $contentType = $response->headers->get('Content-Type', '');
        if (strpos($contentType, 'text/html') === false && strpos($contentType, 'text/plain') === false) {
            return $response;
        }

        $content = $response->getContent();

        if ($content === false || $content === null || $content === '') {
            return $response;
        }

        $placeholders = [];

        $protect = function ($pattern) use (&$content, &$placeholders) {
            $content = preg_replace_callback($pattern, function ($m) use (&$placeholders) {
                $key = "@@PERSIAN_BLOCK_" . count($placeholders) . "@@";
                $placeholders[$key] = $m[0];
                return $key;
            }, $content);
        };

        $protect('{<script\b[^>]*>.*?</script>}siu');
        $protect('{<style\b[^>]*>.*?</style>}siu');
        $protect('{<pre\b[^>]*>.*?</pre>}siu');
        $protect('{<code\b[^>]*>.*?</code>}siu');
        $protect('{value="[^"]*"}siu');
        $protect('{data-[a-z-]+="[^"]*"}siu');
        $protect('{href="[^"]*"}siu');
        $protect('{src="[^"]*"}siu');
        $protect('{content="[^"]*"}siu');

        $content = Persian::digits($content);

        // Inject <html lang="fa-IR" dir="rtl"> and the iran.css stylesheet
        if (stripos($content, '<html') !== false) {
            $content = preg_replace('/<html\b/i', '<html lang="fa-IR" dir="rtl"', $content, 1);
        }

        $iranCss = '<link rel="stylesheet" href="' . asset('public/css/iran.css?v=1') . '" type="text/css">';
        if (stripos($content, '</head>') !== false) {
            $content = preg_replace('/<\/head>/i', $iranCss . '</head>', $content, 1);
        } elseif (stripos($content, '<link rel="stylesheet" href="') !== false) {
            $content = $content . $iranCss;
        }

        foreach ($placeholders as $key => $original) {
            $content = str_replace($key, $original, $content);
        }

        $response->setContent($content);

        return $response;
    }
}

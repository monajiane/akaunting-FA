## اکانتینگ فارسی (Akaunting Iran)

**نرم افزار حسابداری آنلاین رایگان و متن باز، بومی سازی شده برای ایران**

[![نسخه بومی](https://img.shields.io/badge/version-1.0.0--fa-blue.svg)](https://github.com/monajiane/akaunting-FA/releases)
[![زبان](https://img.shields.io/badge/language-فارسی-green.svg)](https://github.com/monajiane/akaunting-FA)
[![واحد پول](https://img.shields.io/badge/currency-تومان-orange.svg)](https://github.com/monajiane/akaunting-FA)
[![لایسنس](https://img.shields.io/badge/license-GPLv3-yellow.svg)](LICENSE.txt)

این پروژه، فورک رسمی [Akaunting](https://akaunting.com/) (نسخه 3.x) است که به صورت اختصاصی برای کاربران ایرانی بومی سازی شده است. Akaunting یک نرم افزار حسابداری آنلاین آزاد و متن باز است که برای کسب وکارهای کوچک و متوسط طراحی شده است.

---

### ویژگی های نسخه بومی ایران

- زبان فارسی (fa-IR) با جهت راست به چپ کامل
- واحد پول **تومان** به عنوان پیش فرض
- پشتیبانی از **ریال** با نرخ تبدیل ۱۰ ریال = ۱ تومان
- اعداد فارسی خودکار در تمام صفحات
- تقویم **شمسی (جلالی)** در توابع کمکی
- مالیات بر ارزش افزوده ۹٪ پیش فرض
- روش های پرداخت ایرانی (نقدی، شبا، کارت خوان، کارت به کارت، چک)
- فونت فارسی Vazirmatn / IRANSans / Tahoma
- شروع سال مالی از **۱ فروردین**
- منطقه زمانی Asia/Tehran
- کشور پیش فرض ایران (IR)

---

### نصب سریع

پیش نیازها:
- PHP 8.1+ (افزونه های: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML)
- MySQL یا MariaDB
- Composer
- Node.js + NPM

```bash
# ۱. دریافت پروژه
git clone https://github.com/monajiane/akaunting-FA.git
cd akaunting-FA
git checkout feature/iran-localization

# ۲. نصب وابستگی ها
composer install --no-dev
npm install && npm run production

# ۳. تنظیم فایل محیطی
cp .env.example .env
php artisan key:generate

# ۴. نصب خودکار با تنظیمات ایران
php artisan install --locale=fa-IR --company-name="نام شرکت شما" \
    --company-email="info@company.ir" \
    --admin-email="admin@company.ir" \
    --admin-password="رمز-عبور-قوی" \
    --db-host=localhost --db-name=akaunting \
    --db-username=root --db-password="your-db-password" --no-interaction

# ۵. تنظیم دسترسی ها
chmod -R 755 storage bootstrap/cache
```

همچنین می توانید پس از کپی فایل `.env` و تنظیم دیتابیس، با باز کردن آدرس پروژه در مرورگر، مراحل نصب را به صورت ویزارد فارسی طی کنید.

---

### استفاده از تقویم شمسی

**در Blade (قالب ها):**

```blade
{{-- نمایش تاریخ شمسی سند --}}
تاریخ فاکتور: @jdate($invoice->invoiced_at)
{{-- خروجی: ۱۴۰۳/۰۶/۲۳ --}}

{{-- فرمت دلخواه با نام ماه --}}
@jdate('j F Y', $invoice->invoiced_at)
{{-- خروجی: ۲۳ شهریور ۱۴۰۳ --}}
```

**در کد PHP:**

```php
echo jdate('Y/m/d', $invoice->invoiced_at);  // ۱۴۰۳/۰۶/۲۳
echo pnum(1234567);                          // ۱٬۲۳۴٬۵۶۷
list($jy, $jm, $jd) = to_jalali(2024, 9, 13);
```

---

### درگاه های پرداخت (توسعه آتی)

- زرین پال، آی دی پی، نکست پی، پرداخت ملی، سداد
- فاکتور الکترونیک سامانه مودیان
- گزارشات مالی مطابق استاندارد حسابداری ایران
- حقوق و دستمزد
- به روزرسانی نرخ ارز

---

### مجوز (لایسنس)

این پروژه تحت لایسنس **GPLv3** منتشر شده است. هسته اصلی Akaunting نیز تحت GPLv3 منتشر شده است.

**با عشق برای کسب وکارهای ایرانی**

<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Jobs\Setting\CreateCurrency;
use App\Jobs\Setting\CreateTax;
use App\Traits\Jobs;
use Illuminate\Database\Seeder;

class Currencies extends Seeder
{
    use Jobs;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $this->create();

        Model::reguard();
    }

    private function create()
    {
        $company_id = $this->command->argument('company');

        // Default currency for Iran: Iranian Toman (IRT)
        $rows = [
            [
                'company_id' => $company_id,
                'name' => trans('demo.currencies.irt', [], 'fa-IR') ?: 'تومان ایران',
                'code' => 'IRT',
                'rate' => '1.00',
                'enabled' => '1',
                'precision' => 0,
                'symbol' => 'تومان',
                'symbol_first' => false,
                'decimal_mark' => '٫',
                'thousands_separator' => '،',
            ],
            [
                'company_id' => $company_id,
                'name' => trans('demo.currencies.irr', [], 'fa-IR') ?: 'ریال ایران',
                'code' => 'IRR',
                'rate' => '0.10',
                'enabled' => '1',
                'precision' => 0,
                'symbol' => 'ریال',
                'symbol_first' => false,
                'decimal_mark' => '٫',
                'thousands_separator' => '،',
            ],
        ];

        foreach ($rows as $row) {
            $row['created_from'] = 'core::seed';

            $this->dispatch(new CreateCurrency($row));
        }

        // Iran VAT (مالیات بر ارزش افزوده) 9% – default for fa-IR
        if (app()->getLocale() === 'fa-IR' || env('APP_LOCALE') === 'fa-IR') {
            $this->dispatch(new CreateTax([
                'company_id'  => $company_id,
                'name'        => 'مالیات بر ارزش افزوده',
                'rate'        => '9.00',
                'type'        => 'normal',
                'enabled'     => '1',
                'created_from'=> 'core::seed',
            ]));
        }
    }
}

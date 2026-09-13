<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use Illuminate\Database\Seeder;

class Settings extends Seeder
{
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

        $is_fa = (app()->getLocale() === 'fa-IR' || env('APP_LOCALE') === 'fa-IR');

        $offline_payments = [
            [
                'code' => 'offline-payments.cash.1',
                'name' => trans('demo.offline_payments.cash'),
                'customer' => '0',
                'order' => '1',
                'description' => null,
            ],
            [
                'code' => 'offline-payments.bank_transfer.2',
                'name' => trans('demo.offline_payments.bank'),
                'customer' => '0',
                'order' => '2',
                'description' => $is_fa
                    ? 'پرداخت از طریق شماره شبا / شماره حساب و ارائه فیش'
                    : null,
            ],
        ];

        if ($is_fa) {
            $offline_payments[] = [
                'code' => 'offline-payments.pos.3',
                'name' => trans('demo.offline_payments.pos'),
                'customer' => '0',
                'order' => '3',
                'description' => 'پرداخت از طریق کارتخوان فروشگاهی (POS)',
            ];
            $offline_payments[] = [
                'code' => 'offline-payments.card_to_card.4',
                'name' => trans('demo.offline_payments.card_to_card'),
                'customer' => '0',
                'order' => '4',
                'description' => 'انتقال وجه کارت به کارت و ارسال رسید',
            ];
            $offline_payments[] = [
                'code' => 'offline-payments.cheque.5',
                'name' => trans('demo.offline_payments.cheque'),
                'customer' => '0',
                'order' => '5',
                'description' => 'پرداخت با چک',
            ];
        }

        $defaults = [
            'invoice.title'                     => trans_choice('general.invoices', 1),
            'wizard.completed'                  => '0',
            'offline-payments.methods'          => json_encode($offline_payments),
        ];

        if ($is_fa) {
            $defaults = array_merge($defaults, [
                'default.country'               => 'IR',
                'default.currency'              => 'IRT',
                'default.locale'                => 'fa-IR',
                'company.country'               => 'IR',
                'financial.start'               => '01-01', // شروع سال مالی اول فروردین
                'date.format'                   => 'Y/m/d',
                'date.separator'                => '/',
                'number_digit_separator'        => '،',
                'number_mark_separator'         => '٫',
            ]);
        }

        setting()->set($defaults);
    }
}

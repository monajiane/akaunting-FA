<?php

namespace Database\Seeds;

use App\Abstracts\Model;
use App\Jobs\Banking\CreateAccount;
use App\Traits\Jobs;
use Illuminate\Database\Seeder;

class Accounts extends Seeder
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

        $currency_code = (app()->getLocale() === 'fa-IR' || env('APP_LOCALE') === 'fa-IR') ? 'IRT' : 'USD';

        $account = $this->dispatch(new CreateAccount([
            'company_id' => $company_id,
            'name' => trans('demo.accounts.cash'),
            'number' => '1',
            'currency_code' => $currency_code,
            'bank_name' => trans('demo.accounts.cash'),
            'enabled' => '1',
            'created_from' => 'core::seed',
        ]));

        setting()->set('default.account', $account->id);
    }
}

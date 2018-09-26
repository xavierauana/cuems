<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot() {
        Validator::extend("date_gt",
            function ($attribute, $value, $parameters, $validator) {

                if (isset($validator->getData()[$parameters[0]])) {

                    $comparison = $validator->getData()[$parameters[0]];

                    return strtotime($comparison) < strtotime($value);
                }

                return false;

            });

        app()->bind(PaymentServiceInterface::class,
            config('app.payment_service'));
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register() {
        //
    }
}

<?php

namespace App\Providers;

use App\Contracts\PaymentServiceInterface;
use App\Observers\TransactionObserver;
use App\Ticket;
use App\Transaction;
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

        Validator::extend('traineeInfoRequired',
            function ($attribute, $value, $parameters, $validator) {

                $ticketId = $validator->getData()['ticket_id'];
                if ($ticket = Ticket::find($ticketId)) {
                    if (strpos(strtolower($ticket->note), "trainee") > -1) {
                        return !empty($value);
                    };

                    return true;
                }

                return false;
            });

        app()->bind(PaymentServiceInterface::class,
            config('app.payment_service'));

        Transaction::observe(TransactionObserver::class);
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

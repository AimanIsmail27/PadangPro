<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Component\HttpClient\HttpClient;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Register Brevo API transport as a Laravel mail transport called "brevo"
        Mail::extend('brevo', function () {
            $factory = new BrevoApiTransportFactory(HttpClient::create());

            return $factory->create(new Dsn(
                'brevo+api',
                'default',
                config('services.brevo.key')
            ));
        });
    }
}

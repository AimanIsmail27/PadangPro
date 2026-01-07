<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS in production (Railway / Cloud)
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        /**
         * Register Brevo mail transport ONLY if the Symfony Brevo bridge is installed.
         * This prevents "class not found" crashes in production.
         */
        if (class_exists(\Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransportFactory::class)) {
            Mail::extend('brevo', function (array $config) {
                $factory = new \Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransportFactory(
                    \Symfony\Component\HttpClient\HttpClient::create()
                );

                return $factory->create(
                    new \Symfony\Component\Mailer\Transport\Dsn(
                        'brevo+api',
                        'default',
                        config('services.brevo.key')
                    )
                );
            });
        }
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        Log::info('AppServiceProvider boot() loaded. MAIL_MAILER=' . config('mail.default'));

        if (class_exists(\Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoApiTransportFactory::class)) {
            Log::info('BrevoApiTransportFactory exists. Registering brevo mailer...');

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
        } else {
            Log::error('BrevoApiTransportFactory class NOT found on this server.');
        }
    }
}

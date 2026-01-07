<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

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

        // Register Brevo (API) transport as a Laravel mail transport called "brevo"
        if (class_exists(BrevoTransportFactory::class)) {
            Log::info('BrevoTransportFactory exists. Registering brevo mailer...');

            Mail::extend('brevo', function () {
                return (new BrevoTransportFactory())->create(
                    new Dsn(
                        'brevo+api',
                        'default',
                        config('services.brevo.key')
                    )
                );
            });
        } else {
            Log::error('BrevoTransportFactory class NOT found on this server. Did composer install symfony/brevo-mailer?');
        }
    }
}

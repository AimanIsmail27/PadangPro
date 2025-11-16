<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Log;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Test scheduler
Schedule::call(function () {
    Log::info('--- The scheduler is alive and running at ' . now() . ' ---');
})->everyMinute();

// Cancel expired rentals
Schedule::command('rental:cancel-expired')->everyMinute();

// Export training data
Schedule::command('export:training-data')->everyMinute();

// Run Python ML training - USE 'py' instead of 'python'
Schedule::exec('py ' . base_path('train_model.py'))
    ->everyMinute()
    ->appendOutputTo(storage_path('logs/python_output.log'));
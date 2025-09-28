<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rental;
use Carbon\Carbon;

class CancelExpiredRentals extends Command
{
    protected $signature = 'rental:cancel-expired';
    protected $description = 'Cancel unpaid rentals that have expired';

    public function handle()
    {
        $now = Carbon::now();

        // Fetch expired rentals with status Pending
        $expiredRentals = Rental::where('rental_Status', 'Pending')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        $count = $expiredRentals->count();

        foreach ($expiredRentals as $rental) {
            $rental->delete();
        }

        $this->info("Cancelled $count expired rental(s).");
    }
}

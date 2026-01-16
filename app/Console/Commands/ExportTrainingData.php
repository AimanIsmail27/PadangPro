<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;
use Illuminate\Support\Facades\File;

class ExportTrainingData extends Command
{
    protected $signature = 'export:training-data';
    protected $description = 'Export successful booking and slot data to a CSV file for AI model training.';

    public function handle()
    {
        $this->info('Fetching successful bookings (paid & completed)...');

        // Fetch all successful bookings (paid OR completed) with their related slot information
        $bookings = Booking::whereIn('booking_Status', ['paid', 'completed'])
            ->with('slot')
            ->get();

        if ($bookings->isEmpty()) {
            $this->warn('No successful bookings found. CSV file will not be created.');
            return 0;
        }

        $this->info("Found {$bookings->count()} successful bookings. Preparing CSV data...");

        // Define the path for the CSV file
        $filePath = base_path('training_data.csv');

        // Prepare the CSV content
        $csvHeader = ['bookingID', 'slotID', 'slot_Date', 'slot_Time', 'booking_Status'];
        $csvRows = [];

        foreach ($bookings as $booking) {
            // Ensure the booking has a related slot
            if ($booking->slot) {
                $csvRows[] = [
                    $booking->bookingID,
                    $booking->slot->slotID,
                    $booking->slot->slot_Date,
                    $booking->slot->slot_Time,
                    $booking->booking_Status,
                ];
            }
        }

        // Write the data to the CSV file
        $handle = fopen($filePath, 'w');
        fputcsv($handle, $csvHeader);

        foreach ($csvRows as $row) {
            fputcsv($handle, $row);
        }

        fclose($handle);

        $this->info("Successfully exported data to training_data.csv");
        return 0;
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingForecastSeeder extends Seeder
{
    public function run(): void
    {
        // ================= CONFIG =================
        $daysBack = 90; // generate history for last 90 days
        $times = [
            '08:00:00', '10:00:00', '12:00:00',
            '14:00:00', '16:00:00', '18:00:00',
            '20:00:00', '22:00:00'
        ];

        // Booking probability
        $weekdayProb = 0.15;  // weekdays less busy
        $weekendProb = 0.45;  // weekends busier

        // Required slot fields
        $defaultSlotPrice = 80.00; // <-- change to your real price if needed
        // ==========================================

        $this->command->info('Seeding historical slots & bookings for forecasting...');

        // ---------- Get Field IDs ----------
        $fieldIDs = DB::table('field')->pluck('fieldID')->toArray();

        if (empty($fieldIDs)) {
            $this->command->error('No fields found in `field` table. Seeder aborted.');
            return;
        }

        // ---------- Get User IDs (if exists) ----------
        $userIDs = [];
        if (DB::getSchemaBuilder()->hasTable('users')) {
            $userIDs = DB::table('users')->pluck('id')->toArray();
        }

        $today = Carbon::today();
        $createdSlots = 0;
        $createdBookings = 0;

        // ---------- Seed Data ----------
        for ($i = $daysBack; $i >= 1; $i--) {
            $date = $today->copy()->subDays($i);
            $isWeekend = $date->isWeekend();
            $probability = $isWeekend ? $weekendProb : $weekdayProb;

            foreach ($fieldIDs as $fieldID) {
                foreach ($times as $time) {

                    // Stable slotID (prevents duplicates)
                    $slotKey = $fieldID . '_' . $date->format('Ymd') . '_' . str_replace(':', '', $time);
                    $slotID = 'SLOT_' . substr(md5($slotKey), 0, 12);

                    // ---------- Insert Slot ----------
                    if (!DB::table('slot')->where('slotID', $slotID)->exists()) {
                        DB::table('slot')->insert([
                            'slotID' => $slotID,
                            'fieldID' => $fieldID,
                            'slot_Date' => $date->format('Y-m-d'),
                            'slot_Time' => $time,
                            'slot_Status' => 'available', // REQUIRED
                            'slot_Price' => $defaultSlotPrice, // REQUIRED
                        ]);
                        $createdSlots++;
                    }

                    // ---------- Randomly create booking ----------
                    if (mt_rand() / mt_getrandmax() <= $probability) {

                        // Avoid duplicate booking on the same slot
                        $alreadyBooked = DB::table('booking')->where('slotID', $slotID)->exists();
                        if ($alreadyBooked) {
                            continue;
                        }

                        $bookingID = 'BOOK_' . Str::upper(Str::random(10));
                        $userID = !empty($userIDs) ? $userIDs[array_rand($userIDs)] : null;

                        DB::table('booking')->insert([
                            'bookingID' => $bookingID,
                            'booking_Name' => 'Seed User',
                            'booking_Email' => 'seed_' . Str::random(6) . '@example.com',
                            'booking_PhoneNumber' => '01' . mt_rand(100000000, 999999999),
                            'booking_BackupNumber' => '01' . mt_rand(100000000, 999999999),
                            'booking_Status' => 'completed',
                            'fieldID' => $fieldID,
                            'slotID' => $slotID,
                            'userID' => $userID,
                            'booking_CreatedAt' => $date->format('Y-m-d') . ' ' . $time,
                        ]);

                        // Update slot status
                        DB::table('slot')->where('slotID', $slotID)->update([
                            'slot_Status' => 'booked'
                        ]);

                        $createdBookings++;
                    }
                }
            }
        }

        $this->command->info("Seeding complete:");
        $this->command->info("- Slots created: {$createdSlots}");
        $this->command->info("- Bookings created: {$createdBookings}");
    }
}

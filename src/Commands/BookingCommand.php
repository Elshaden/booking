<?php

namespace Elshaden\Booking\Commands;

use Elshaden\Booking\Facades\Booking;
use Illuminate\Console\Command;

class BookingCommand extends Command
{
    public $signature = 'booking:clean-expired';

    public $description = 'Clean up expired bookings';

    public function handle(): int
    {
        $count = Booking::cleanExpiredBookings();

        $this->info("Cleaned up {$count} expired bookings.");

        return self::SUCCESS;
    }
}

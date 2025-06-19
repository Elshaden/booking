<?php

namespace Elshaden\Booking;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Elshaden\Booking\Commands\BookingCommand;

class BookingServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('booking')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_booking_table')
            ->hasCommand(BookingCommand::class);
    }
}

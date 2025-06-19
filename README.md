# Laravel Booking System

[![Latest Version on Packagist](https://img.shields.io/packagist/v/elshaden/booking.svg?style=flat-square)](https://packagist.org/packages/elshaden/booking)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/elshaden/booking/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/elshaden/booking/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/elshaden/booking/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/elshaden/booking/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/elshaden/booking.svg?style=flat-square)](https://packagist.org/packages/elshaden/booking)

The Booking System is a lightweight Laravel package that manages booking schedules for any model in your application. It provides a simple way to add booking functionality to your models, with features like checking availability, making preliminary bookings that expire after a preset period, confirming bookings, and more.

## Features

- Check availability for a given model
- Make preliminary bookings that expire after a preset period
- Confirm bookings
- List bookings
- Cancel bookings
- Change bookings
- Configurable booking range (hours, days, or months)
- Automatic cleanup of expired bookings

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/booking.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/booking)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require elshaden/booking
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="booking-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="booking-config"
```

This is the contents of the published config file:

```php
return [
    /*
    |--------------------------------------------------------------------------
    | Booking Expiration Time
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in minutes) a pending booking will be valid
    | before it expires. After this time, the booking will be automatically
    | marked as expired and the time slot will be available for booking again.
    |
    */
    'pending_expiration_minutes' => 30,

    /*
    |--------------------------------------------------------------------------
    | Default Booking Range
    |--------------------------------------------------------------------------
    |
    | This value determines the default booking range type if a model doesn't
    | specify its own range type. Valid values are 'hours', 'days', or 'months'.
    |
    */
    'default_range_type' => 'hours',

    /*
    |--------------------------------------------------------------------------
    | Booking Status Options
    |--------------------------------------------------------------------------
    |
    | These are the valid status values for bookings.
    |
    */
    'status_options' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'cancelled' => 'Cancelled',
    ],

    /*
    |--------------------------------------------------------------------------
    | Clean Expired Bookings
    |--------------------------------------------------------------------------
    |
    | If true, a scheduled command will run to automatically clean up expired
    | bookings. You can customize the frequency in the console kernel.
    |
    */
    'clean_expired_bookings' => true,
];
```

## Usage

### Available Traits

This package provides two traits:

1. **HasBookingTrait**: For models that can be booked (e.g., a Room, a Resource)
2. **CanMakeBookingTrait**: For models that can make bookings (e.g., a User, a Customer)

### Setting Up a Bookable Model

To make a model bookable, add the `HasBookingTrait` trait to it and implement the `presetRange` method:

```php
use Elshaden\Booking\Traits\HasBookingTrait;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasBookingTrait;

    /**
     * Get the booking range type for this model.
     *
     * @return string
     */
    public static function presetRange(): string
    {
        return 'hours'; // or 'days' or 'months'
    }
}
```

### Setting Up a Model That Can Make Bookings

To allow a model to make bookings, add the `CanMakeBookingTrait` trait to it:

```php
use Elshaden\Booking\Traits\CanMakeBookingTrait;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use CanMakeBookingTrait;
}
```

### Using Both Traits Together

You can use both traits together to create a complete booking system. For example:

```php
// A room that can be booked
$room = Room::find(1);

// A user that can make bookings
$user = User::find(1);

// Check if the room is available
$isAvailable = $room->checkAvailability('2023-01-01 10:00:00', '2023-01-01 12:00:00');

// Make a booking if available
if ($isAvailable) {
    $booking = $room->preBook('2023-01-01 10:00:00', '2023-01-01 12:00:00', $user);
}

// List all bookings made by the user
$userBookings = $user->listMyBookings();
```

### Checking Availability

You can check if a model is available for booking in a given time range:

```php
// Using the model directly
$room = Room::find(1);
$isAvailable = $room->checkAvailability('2023-01-01 10:00:00', '2023-01-01 12:00:00');

// Using the facade
use Elshaden\Booking\Facades\Booking;

$isAvailable = Booking::checkAvailability($room, '2023-01-01 10:00:00', '2023-01-01 12:00:00');
```

### Making a Booking

To make a preliminary booking that will expire after the configured time:

```php
// Using the model directly
$room = Room::find(1);
$booking = $room->preBook('2023-01-01 10:00:00', '2023-01-01 12:00:00');

// Using the facade
use Elshaden\Booking\Facades\Booking;

$booking = Booking::preBook($room, '2023-01-01 10:00:00', '2023-01-01 12:00:00');
```

### Confirming a Booking

To confirm a booking:

```php
// Using the booking model directly
$booking->confirm();

// Using the facade
use Elshaden\Booking\Facades\Booking;

Booking::confirmBooking($booking->id);
```

### Cancelling a Booking

To cancel a booking:

```php
// Using the booking model directly
$booking->cancel();

// Using the facade
use Elshaden\Booking\Facades\Booking;

Booking::cancelBooking($booking->id);
```

### Changing a Booking

To change a booking's dates:

```php
// Using the booking model directly
$booking->change('2023-01-01 14:00:00', '2023-01-01 16:00:00');

// Using the facade
use Elshaden\Booking\Facades\Booking;

Booking::changeBooking($booking->id, '2023-01-01 14:00:00', '2023-01-01 16:00:00');
```

### Listing Bookings

#### Listing Bookings for a Bookable Model

To list all bookings for a bookable model in a given time range:

```php
// Using the model directly
$room = Room::find(1);
$bookings = $room->listBookings('2023-01-01', '2023-01-31');

// Using the facade
use Elshaden\Booking\Facades\Booking;

$bookings = Booking::listBookings($room, '2023-01-01', '2023-01-31');
```

#### Listing Bookings Made by a User

To list all bookings made by a user in a given time range:

```php
// Using the model directly
$user = User::find(1);
$bookings = $user->listMyBookings('2023-01-01', '2023-01-31');

// You can also filter by status
$pendingBookings = $user->listMyBookings('2023-01-01', '2023-01-31', 'pending');

// Using the facade
use Elshaden\Booking\Facades\Booking;

$bookings = Booking::listMyBookings($user, '2023-01-01', '2023-01-31');
```

### Cleaning Expired Bookings

To clean up expired bookings, you can use the provided command:

```bash
php artisan booking:clean-expired
```

Or you can schedule it in your console kernel:

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('booking:clean-expired')->hourly();
}
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Salah Elabbar](https://github.com/Elshaden)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

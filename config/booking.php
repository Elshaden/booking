<?php

// config for Elshaden/Booking
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
    'default_range_type' => 'days',

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

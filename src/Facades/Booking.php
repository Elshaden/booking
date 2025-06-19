<?php

namespace Elshaden\Booking\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Elshaden\Booking\Booking
 */
class Booking extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Elshaden\Booking\Booking::class;
    }
}

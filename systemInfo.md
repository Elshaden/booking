# Laravel Booking System

# Summary
The Booking System is a lightweight Laravel Booking package  that manages booking schedules for a given model.
The Main methods available for applications are :


> Check Availability for a given Model
> Make primary booking that expires after a preset period from config 'booking.php'
> Confirm Booking
> List bookings
> cancel booking
> change booking

The Package can be installed using composer require
and follow the installation and configuratin guide in README.md


# Booking range

> Booking range in hours, days, or months
> Set the range on the model with a presetRange() static method that will be used


# How it works

> add the HasBookingTrait to the model you want to make booking for
> add the presetRange() which will return one of the rage types 'hours, days, months'


# Available  

> checkAvailability(_from,to_):bool;
> preBook(_from,to_)
> confirmBooking($Booking->id)
> cancelBooking($Booking->id)
> changeBooking($Booking->id, _from,to_)
> ListBooking(_from,to_)

 Recommend Models and Data schemas migrations
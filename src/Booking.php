<?php

namespace Elshaden\Booking;

use Elshaden\Booking\Models\Booking as BookingModel;
use Illuminate\Database\Eloquent\Model;

class Booking
{
    /**
     * Check availability for a model in the given time range.
     *
     * @param Model $model
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @return bool
     */
    public function checkAvailability(Model $model, $from, $to): bool
    {
        if (!method_exists($model, 'checkAvailability')) {
            throw new \Exception('Model does not have the HasBookingTrait trait');
        }

        return $model->checkAvailability($from, $to);
    }

    /**
     * Create a preliminary booking for a model.
     *
     * @param Model $model
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @param Model|null $bookedBy The model that is making the booking
     * @return \Elshaden\Booking\Models\Booking|null
     */
    public static function preBook(Model $model, $from, $to, Model $bookedBy = null)
    {
        if (!method_exists($model, 'preBook')) {
            throw new \Exception('Model does not have the HasBookingTrait trait');
        }

        return $model->preBook($from, $to, $bookedBy);
    }

    /**
     * Confirm a booking.
     *
     * @param int $bookingId
     * @return bool
     */
    public static function confirmBooking(int $bookingId): bool
    {
        $booking = BookingModel::find($bookingId);

        if (!$booking) {
            return false;
        }

        return $booking->confirm();
    }

    /**
     * Cancel a booking.
     *
     * @param int $bookingId
     * @return bool
     */
    public function cancelBooking(int $bookingId): bool
    {
        $booking = BookingModel::find($bookingId);

        if (!$booking) {
            return false;
        }

        return $booking->cancel();
    }

    /**
     * Change a booking's dates.
     *
     * @param int $bookingId
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @return bool
     */
    public function changeBooking(int $bookingId, $from, $to): bool
    {
        $booking = BookingModel::find($bookingId);

        if (!$booking) {
            return false;
        }

        return $booking->change($from, $to);
    }

    /**
     * List bookings for a model in the given time range.
     *
     * @param Model $model
     * @param \DateTime|string|null $from
     * @param \DateTime|string|null $to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listBookings(Model $model, $from = null, $to = null)
    {
        if (!method_exists($model, 'listBookings')) {
            throw new \Exception('Model does not have the HasBookingTrait trait');
        }

        return $model->listBookings($from, $to);
    }

    /**
     * List bookings made by a model in the given time range.
     *
     * @param Model $model
     * @param \DateTime|string|null $from
     * @param \DateTime|string|null $to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listMyBookings(Model $model, $from = null, $to = null)
    {
        if (!method_exists($model, 'listMyBookings')) {
            throw new \Exception('Model does not have the CanMakeBookingTrait trait');
        }

        return $model->listMyBookings($from, $to);
    }

    /**
     * Clean up expired bookings.
     *
     * @return int Number of expired bookings that were cleaned up
     */
    public function cleanExpiredBookings(): int
    {
        return BookingModel::expired()->update(['status' => 'cancelled']);
    }
}

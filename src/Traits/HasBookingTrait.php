<?php

namespace Elshaden\Booking\Traits;

use Elshaden\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasBookingTrait
{
    /**
     * Get all bookings for this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'bookable');
    }

    /**
     * Check if the model is available for booking in the given time range.
     *
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @return bool
     */
    public function checkAvailability($from, $to): bool
    {
        // Convert string dates to DateTime if needed
        $fromDate = is_string($from) ? new \DateTime($from) : $from;
        $toDate = is_string($to) ? new \DateTime($to) : $to;

        // Check if there are any overlapping bookings
        $overlappingBookings = $this->bookings()
            ->where(function ($query) use ($fromDate, $toDate) {
                $query->where(function ($q) use ($fromDate, $toDate) {
                    // Booking starts during the requested period
                    $q->where('start_time', '>=', $fromDate)
                      ->where('start_time', '<', $toDate);
                })->orWhere(function ($q) use ($fromDate, $toDate) {
                    // Booking ends during the requested period
                    $q->where('end_time', '>', $fromDate)
                      ->where('end_time', '<=', $toDate);
                })->orWhere(function ($q) use ($fromDate, $toDate) {
                    // Booking spans the entire requested period
                    $q->where('start_time', '<=', $fromDate)
                      ->where('end_time', '>=', $toDate);
                });
            })
            ->whereIn('status', ['confirmed', 'pending'])
            ->count();

        return $overlappingBookings === 0;
    }

    /**
     * Create a preliminary booking that will expire after the configured time.
     *
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @param \Illuminate\Database\Eloquent\Model|null $bookedBy The model that is making the booking
     * @return \Elshaden\Booking\Models\Booking|null
     */
    public function preBook($from, $to, $bookedBy = null)
    {
        if (!$this->checkAvailability($from, $to)) {
            return null;
        }

        // Convert string dates to DateTime if needed
        $fromDate = is_string($from) ? new \DateTime($from) : $from;
        $toDate = is_string($to) ? new \DateTime($to) : $to;

        $attributes = [
            'start_time' => $fromDate,
            'end_time' => $toDate,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(config('booking.pending_expiration_minutes', 30)),
        ];

        // Set the booked_by relationship if provided
        if ($bookedBy) {
            $attributes['booked_by_type'] = get_class($bookedBy);
            $attributes['booked_by_id'] = $bookedBy->getKey();
        }

        return $this->bookings()->create($attributes);
    }

    /**
     * Get the booking range type for this model.
     * Must be implemented by the model using this trait.
     *
     * @return string
     */
    public static function presetRange(): string
    {
        // This should be overridden by the model using this trait
        // Should return one of: 'hours', 'days', 'months'
        return config('booking.Method…', 'days');
    }

    /**
     * List all bookings for this model in the given time range.
     *
     * @param \DateTime|string|null $from
     * @param \DateTime|string|null $to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listBookings($from = null, $to = null, $status = null)
    {
        $query = $this->bookings();

        if ($from) {
            $fromDate = is_string($from) ? new \DateTime($from) : $from;
            $query->where('end_time', '>=', $fromDate);
        }

        if ($to) {
            $toDate = is_string($to) ? new \DateTime($to) : $to;
            $query->where('start_time', '<=', $toDate);
        }
        if ($status) {
            $query->where('status',$status);
        }

        return $query->orderBy('start_time')->get();
    }

}

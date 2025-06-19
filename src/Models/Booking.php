<?php

namespace Elshaden\Booking\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Booking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'start_time',
        'end_time',
        'status',
        'expires_at',
        'booked_by_type',
        'booked_by_id',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Get the bookable model that owns the booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function bookable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the model that booked this booking.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function bookedBy(): MorphTo
    {
        return $this->morphTo('booked_by');
    }

    /**
     * Confirm this booking.
     *
     * @return bool
     */
    public function confirm(): bool
    {
        return $this->update([
            'status' => 'confirmed',
            'expires_at' => null,
        ]);
    }

    /**
     * Cancel this booking.
     *
     * @return bool
     */
    public function cancel(): bool
    {
        return $this->update([
            'status' => 'cancelled',
        ]);
    }

    /**
     * Change the booking dates.
     *
     * @param \DateTime|string $from
     * @param \DateTime|string $to
     * @return bool
     */
    public function change($from, $to): bool
    {
        // Convert string dates to DateTime if needed
        $fromDate = is_string($from) ? new \DateTime($from) : $from;
        $toDate = is_string($to) ? new \DateTime($to) : $to;

        // Check if the new dates are available
        if (!$this->bookable->checkAvailability($fromDate, $toDate)) {
            return false;
        }

        return $this->update([
            'start_time' => $fromDate,
            'end_time' => $toDate,
        ]);
    }

    /**
     * Scope a query to only include pending bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include confirmed bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeConfirmed($query)
    {
        return $query->where('status', 'confirmed');
    }

    /**
     * Scope a query to only include cancelled bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    /**
     * Scope a query to only include expired bookings.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'pending')
            ->where('expires_at', '<', now());
    }
}

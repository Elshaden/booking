<?php

namespace Elshaden\Booking\Traits;

use Elshaden\Booking\Models\Booking;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait CanMakeBookingTrait
{
    /**
     * Get all bookings made by this model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function myBookings(): MorphMany
    {
        return $this->morphMany(Booking::class, 'booked_by');
    }

    /**
     * List all bookings made by this model in the given time range.
     *
     * @param \DateTime|string|null $from
     * @param \DateTime|string|null $to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function listMyBookings($from = null, $to = null,$status = null)
    {
        $query = $this->myBookings();

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

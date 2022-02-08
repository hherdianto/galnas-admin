<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\Event
 *
 * @property int | null parent_event_id
 * @property int $id
 * @property string $event_name
 * @property int $event_type_id
 * @property string|null $icon
 * @property int $sequence
 * @property Carbon $open_booking_at
 * @property Carbon $date_start
 * @property Carbon $date_end
 * @property int|null $location_id
 * @property string|null $notes
 * @property string|null $url
 * @property int $is_active
 * @property int|null $created_by
 * @property Carbon|null $created_at
 * @property int|null $updated_by
 * @property Carbon|null $updated_at
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 * @property-read Collection|Event[] $children
 * @property-read int|null $children_count
 * @property-read EventType $eventType
 * @property-read Collection|EventImage[] $images
 * @property-read int|null $images_count
 * @property-read Location|null $location
 * @property-read Collection|EventSchedule[] $schedules
 * @property-read int|null $schedules_count
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static Builder|Event onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDateEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDateStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLocationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereOpenBookingAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereParentEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUrl($value)
 * @method static Builder|Event withTrashed()
 * @method static Builder|Event withoutTrashed()
 * @mixin Eloquent
 * @property int $include_weekend 0: tidak termasuk weekday
 * 1: termasuk weekday
 * @property-read Collection|DayOfWeekEvent[] $dayOfWeeks
 * @property-read int|null $day_of_weeks_count
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereIncludeWeekend($value)
 */
class Event extends Model
{
    use SoftDeletes;
    protected $fillable = ['event_name', 'event_type_id', 'parent_event_id', 'icon', 'sequence', 'open_booking_at',
        'date_start', 'date_end', 'include_weekend', 'location_id', 'notes', 'url', 'is_active',
        'created_by', 'updated_by', 'deleted_by'];
    protected $dates = ['open_booking_at', 'date_start', 'date_end'];

    public function eventType() {
        return $this->belongsTo(EventType::class);
    }

    public function parent() {
        if ($this->parent_event_id && $this->parent_event_id > 0) {
            return $this->belongsTo(static::class, 'parent_event_id');
        }
        return null;
    }

    public function children() {
        return $this->hasMany(static::class, 'parent_event_id');
    }

    public function location() {
        return $this->belongsTo(Location::class);
    }

    public function images() {
        return $this->hasMany(EventImage::class);
    }

    public function schedules() {
        return $this->hasMany(EventSchedule::class);
    }

    public function dayOfWeeks() {
        return $this->hasMany(DayOfWeekEvent::class);
    }
}

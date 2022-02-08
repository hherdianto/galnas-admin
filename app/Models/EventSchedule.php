<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\EventSchedule
 *
 * @property int $id
 * @property int $event_id
 * @property string $schedule_name
 * @property Carbon $start_time
 * @property Carbon $end_time
 * @property int $slot
 * @property int $is_active
 * @property string|null $notes
 * @property int $created_by
 * @property Carbon $created_at
 * @property int|null $updated_by
 * @property Carbon|null $updated_at
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 * @property-read Event $event
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule newQuery()
 * @method static Builder|EventSchedule onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereEndTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereScheduleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereSlot($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereStartTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereUpdatedBy($value)
 * @method static Builder|EventSchedule withTrashed()
 * @method static Builder|EventSchedule withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|Visit[] $visits
 * @property-read int|null $visits_count
 * @property int $day_of_week data hari,
 * 0: minggu,
 * 1: senin,
 * 2: selasa,
 * ...
 * 6: sabtu
 * @property-read Collection|\App\Models\Visit[] $confirmedVisits
 * @property-read int|null $confirmed_visits_count
 * @method static \Illuminate\Database\Eloquent\Builder|EventSchedule whereDayOfWeek($value)
 */
class EventSchedule extends Model
{
    use SoftDeletes;

    protected $fillable = ['event_id', 'schedule_name', 'day_of_week', 'start_time', 'end_time', 'slot', 'is_active', 'notes',
        'created_by', 'updated_by', 'deleted_by'];

    protected $dates = ['start_time', 'end_time'];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function confirmedVisits() {
        return $this->hasMany(Visit::class)->whereNotNull(['confirmed_at']);
    }
}

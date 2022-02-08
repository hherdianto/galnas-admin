<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\DayOfWeekEvent
 *
 * @property int $id
 * @property int $event_id
 * @property int $day_of_week
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property-read Event $event
 * @method static Builder|DayOfWeekEvent newModelQuery()
 * @method static Builder|DayOfWeekEvent newQuery()
 * @method static Builder|DayOfWeekEvent query()
 * @method static Builder|DayOfWeekEvent whereActive($value)
 * @method static Builder|DayOfWeekEvent whereCreatedAt($value)
 * @method static Builder|DayOfWeekEvent whereDayOfWeek($value)
 * @method static Builder|DayOfWeekEvent whereEventId($value)
 * @method static Builder|DayOfWeekEvent whereId($value)
 * @method static Builder|DayOfWeekEvent whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DayOfWeekEvent extends Model {
    protected $fillable = [
        'event_id',
        'day_of_week',
        'active',
    ];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}

<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Visit
 *
 * @property int $id
 * @property int $visitor_id
 * @property int $event_schedule_id
 * @property string $code
 * @property int|null $confirmed_by
 * @property Carbon|null $confirmed_at
 * @property int|null $canceled_by 0: auto canceled by system
 * @property Carbon|null $canceled_at
 * @property string|null $notes
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property-read EventSchedule $eventSchedule
 * @property-read Visitor $visitor
 * @method static Builder|Visit newModelQuery()
 * @method static Builder|Visit newQuery()
 * @method static Builder|Visit query()
 * @method static Builder|Visit whereCanceledAt($value)
 * @method static Builder|Visit whereCanceledBy($value)
 * @method static Builder|Visit whereCode($value)
 * @method static Builder|Visit whereConfirmedAt($value)
 * @method static Builder|Visit whereConfirmedBy($value)
 * @method static Builder|Visit whereCreatedAt($value)
 * @method static Builder|Visit whereEventScheduleId($value)
 * @method static Builder|Visit whereId($value)
 * @method static Builder|Visit whereNotes($value)
 * @method static Builder|Visit whereUpdatedAt($value)
 * @method static Builder|Visit whereVisitorId($value)
 * @mixin Eloquent
 */
class Visit extends Model
{
    protected $fillable = ['visitor_id', 'event_schedule_id', 'code', 'member_count', 'read_at',
        'confirmed_by', 'confirmed_at', 'canceled_by', 'canceled_at', 'notes'];
    protected $dates = ['read_at', 'confirmed_at', 'canceled_at'];

    public function visitor() {
        return $this->belongsTo(Visitor::class);
    }

    public function eventSchedule() {
        return $this->belongsTo(EventSchedule::class);
    }

    public function eventScheduleWithTrash() {
        return $this->belongsTo(EventSchedule::class)->withTrashed();
    }
}

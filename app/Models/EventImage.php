<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Models\EventImage
 *
 * @property int $id
 * @property int $event_id
 * @property string $image
 * @property int $sequence
 * @property int $created_by
 * @property Carbon $created_at
 * @property int|null $updated_by
 * @property Carbon|null $updated_at
 * @property int|null $deleted_by
 * @property Carbon|null $deleted_at
 * @property-read Event $event
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage newQuery()
 * @method static Builder|EventImage onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage query()
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventImage whereUpdatedBy($value)
 * @method static Builder|EventImage withTrashed()
 * @method static Builder|EventImage withoutTrashed()
 * @mixin Eloquent
 */
class EventImage extends Model
{
    Use SoftDeletes;

    protected $fillable = ['event_id', 'image', 'sequence', 'created_by', 'updated_by', 'deleted_by'];

    public function event() {
        return $this->belongsTo(Event::class);
    }
}

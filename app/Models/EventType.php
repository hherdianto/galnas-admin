<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EventType
 *
 * @property int $id
 * @property string $type
 * @property string|null $icon
 * @property int $is_showing
 * @property string|null $notes
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @method static Builder|EventType newModelQuery()
 * @method static Builder|EventType newQuery()
 * @method static Builder|EventType query()
 * @method static Builder|EventType whereIcon($value)
 * @method static Builder|EventType whereId($value)
 * @method static Builder|EventType whereIsShowing($value)
 * @method static Builder|EventType whereNotes($value)
 * @method static Builder|EventType whereType($value)
 * @mixin Eloquent
 */
class EventType extends Model
{
    protected $fillable = ['type', 'icon', 'is_showing', 'notes'];

    public function events() {
        return $this->hasMany(Event::class);
    }
}

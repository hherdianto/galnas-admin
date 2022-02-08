<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Location
 *
 * @property int $id
 * @property string $location_name
 * @property string|null $icon
 * @property int $is_showing
 * @property int|null $parent_location_id
 * @property string|null $notes
 * @property string|null $url
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @property-read Collection|Location[] $children
 * @property-read int|null $children_count
 * @property-read Collection|Event[] $events
 * @property-read int|null $events_count
 * @method static Builder|Location newModelQuery()
 * @method static Builder|Location newQuery()
 * @method static Builder|Location query()
 * @method static Builder|Location whereCreatedAt($value)
 * @method static Builder|Location whereCreatedBy($value)
 * @method static Builder|Location whereDeletedAt($value)
 * @method static Builder|Location whereDeletedBy($value)
 * @method static Builder|Location whereIcon($value)
 * @method static Builder|Location whereId($value)
 * @method static Builder|Location whereIsShowing($value)
 * @method static Builder|Location whereLocationName($value)
 * @method static Builder|Location whereNotes($value)
 * @method static Builder|Location whereParentLocationId($value)
 * @method static Builder|Location whereUpdatedAt($value)
 * @method static Builder|Location whereUpdatedBy($value)
 * @method static Builder|Location whereUrl($value)
 * @mixin Eloquent
 */
class Location extends Model
{
    public $timestamps = false;
    protected $fillable = ['location_name', 'icon', 'is_showing', 'parent_location_id', 'notes', 'url', 'created_by', 'updated_by', 'deleted_by'];

    /**
     * @return BelongsTo|null
     */
    public function parent() {
        if ($this->parent_location_id && $this->parent_location_id > 0) {
            return $this->belongsTo(static::class, 'parent_location_id');
        }
        return null;
    }

    public function children() {
        return $this->hasMany(static::class, 'parent_location_id');
    }

    public function events() {
        return $this->hasMany(Event::class);
    }
}

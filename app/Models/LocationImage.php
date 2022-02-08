<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\LocationImage
 *
 * @property int $id
 * @property string $image
 * @property string|null $alt_name
 * @property int $sequence
 * @property int $created_by
 * @property Carbon $created_at
 * @property int|null $updated_by
 * @property Carbon|null $updated_at
 * @property int|null $deleted_by
 * @property string|null $deleted_at
 * @method static Builder|LocationImage newModelQuery()
 * @method static Builder|LocationImage newQuery()
 * @method static Builder|LocationImage query()
 * @method static Builder|LocationImage whereAltName($value)
 * @method static Builder|LocationImage whereCreatedAt($value)
 * @method static Builder|LocationImage whereCreatedBy($value)
 * @method static Builder|LocationImage whereDeletedAt($value)
 * @method static Builder|LocationImage whereDeletedBy($value)
 * @method static Builder|LocationImage whereId($value)
 * @method static Builder|LocationImage whereImage($value)
 * @method static Builder|LocationImage whereSequence($value)
 * @method static Builder|LocationImage whereUpdatedAt($value)
 * @method static Builder|LocationImage whereUpdatedBy($value)
 * @mixin Eloquent
 */
class LocationImage extends Model
{

}

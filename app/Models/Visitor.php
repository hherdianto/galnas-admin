<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\Visitor
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $full_name
 * @property string $phone
 * @property string $gender
 * @property int $indonesian 1: WNI  2: WNA
 * @property int|null $age
 * @property int|null $parent_visitor_id If exist then he/she part of parent group
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Visitor newModelQuery()
 * @method static Builder|Visitor newQuery()
 * @method static Builder|Visitor query()
 * @method static Builder|Visitor whereAge($value)
 * @method static Builder|Visitor whereCreatedAt($value)
 * @method static Builder|Visitor whereEmail($value)
 * @method static Builder|Visitor whereFullName($value)
 * @method static Builder|Visitor whereGender($value)
 * @method static Builder|Visitor whereId($value)
 * @method static Builder|Visitor whereIndonesian($value)
 * @method static Builder|Visitor whereParentVisitorId($value)
 * @method static Builder|Visitor wherePassword($value)
 * @method static Builder|Visitor wherePhone($value)
 * @method static Builder|Visitor whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Visitor extends Model
{
    protected $fillable = ['email', 'password', 'full_name', 'phone', 'gender', 'indonesian', 'age', 'parent_visitor_id'];

    protected $hidden = ['password'];
}

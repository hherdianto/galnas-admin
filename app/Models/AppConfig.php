<?php


namespace App\Models;


use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * App\Models\AppConfig
 *
 * @property string $id
 * @property string $value
 * @property string|null $default_value
 * @property string|null $notes
 * @property int $created_by
 * @property Carbon $created_at
 * @property int|null $updated_by
 * @property Carbon|null $updated_at
 * @method static Builder|AppConfig newModelQuery()
 * @method static Builder|AppConfig newQuery()
 * @method static Builder|AppConfig query()
 * @method static Builder|AppConfig whereCreatedAt($value)
 * @method static Builder|AppConfig whereCreatedBy($value)
 * @method static Builder|AppConfig whereDefaultValue($value)
 * @method static Builder|AppConfig whereId($value)
 * @method static Builder|AppConfig whereNotes($value)
 * @method static Builder|AppConfig whereUpdatedAt($value)
 * @method static Builder|AppConfig whereUpdatedBy($value)
 * @method static Builder|AppConfig whereValue($value)
 * @mixin Eloquent
 */
class AppConfig extends Model
{
public $incrementing = false;
}

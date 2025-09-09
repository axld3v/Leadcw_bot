<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QueueSenderMsg
 *
 * @property int $id
 * @property bool $is_active
 * @property string $owner_user_id
 * @property array $info_json
 * @property array $result
 * @property Carbon $date_end
 */
class QueueSenderMsg extends Model
{
    use HasFactory;

    // Какие поля есть в модели
    protected $fillable = [
        'is_active', 'owner_user_id', 'info_json', 'result', 'date_end'
    ];

    //Чтобы сразу конвертировать из одного типа в другой и при получении значения будет уже другой тип данных
    protected $casts = [
        'owner_user_id' => 'string',
        'info_json'     => 'array',
        'result'        => 'array',
        'is_active'     => 'bool',
    ];

    //Если есть по умолчанию какие-то атрибуты, то лучше их прописать
    protected $attributes = [
        'is_active' => true,
    ];

    protected array $dates = [
        "date_end"
    ];
}

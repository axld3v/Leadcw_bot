<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\QueueSenderMsg
 *
 * @property int $id
 * @property bool $title
 * @property array $info_json
 */
class DictionaryVariable extends Model
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'dictionary_variables';

    protected $fillable = [
        'title ', 'info_json'
    ];

    //Чтобы сразу конвертировать из одного типа в другой и при получении значения будет уже другой тип данных
    protected $casts = [
        'title'     => 'string',
        'info_json' => 'array',
    ];
}

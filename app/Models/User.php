<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Throwable;

/**
 * App\Models\User
 *
 * @property string $user_id
 * @property string $username
 * @property string $first_name
 * @property string $last_name
 * @property string $lastmessage
 * @property string $telegram_name
 * @property array $json_info
 * @property int $support_chat
 * @property bool $blocked
 * @property bool $is_premium
 */
class User extends Model
{
    use HasFactory;

    // Какие поля есть в модели
    protected $fillable = [
        'user_id', 'username', 'lastmessage', 'telegram_name', 'json_info', 'support_chat'
    ];

    //Чтобы сразу конвертировать из одного типа в другой и при получении значения будет уже другой тип данных
    protected $casts = [
        'user_id'      => 'string',
        'json_info'    => 'array',
        'support_chat' => 'int',
        'is_premium'   => 'bool',
        'blocked'      => 'bool',
    ];

    //Если есть по умолчанию какие-то атрибуты, то лучше их прописать
    protected $attributes = [
        'first_name' => "",
        'last_name'  => "",
        'blocked'    => false,
        'is_premium' => false,
    ];

    public static function registration(array $fromUserMessage, string $user_id = ""): User|null
    {
        if (empty($user_id))
            $user_id = $fromUserMessage['id'] ?? "";

        if (empty($user_id)) return null;
        try
        {
            $username = $fromUserMessage['username'] ?? "";
            $first_name = $fromUserMessage['first_name'] ?? "";
            $last_name = $fromUserMessage['last_name'] ?? "";
            $is_premium = $fromUserMessage['is_premium'] ?? false;
            $user = User::query()->firstOrCreate(
                ['user_id' => $user_id],
                [
                    'username'    => $username,
                    'first_name'  => $first_name,
                    'last_name'   => $last_name,
                    'lastmessage' => '',
                ]
            );
            $update = false;
            if ($user->username != $username)
            {
                $user->username = $username;
                $update = true;
            }

            if ($user->first_name != $first_name)
            {
                $user->first_name = $first_name;
                $update = true;
            }

            if ($user->last_name != $last_name)
            {
                $user->last_name = $last_name;
                $update = true;
            }

            if ($user->is_premium != $is_premium)
            {
                $user->is_premium = $is_premium;
                $update = true;
            }

            if ($update) $user->save();
            return $user;
        } catch (Throwable $throwable)
        {
            logging($throwable);
        }
        return null;
    }

    public function get_chat_id(): string
    {
        return $this->user_id;
    }

    public function updateAndSave(array $attributes = []): void
    {
        try
        {
            if (!empty($attributes))
            {
                foreach ($attributes as $attribute => $value)
                {
                    if (empty($attribute)) continue;
                    $this->$attribute = $value;
                }
            }
            $this->save();
        } catch (\Throwable $throwable)
        {
            logging($throwable);
        }
    }

    public function setJsonInfo(string $key, mixed $values): void
    {
        $totalJson = empty($this->json_info) ? [] : json_decode($this->json_info, true);
        if (is_null($values) && isset($totalJson[$key]))
            unset($totalJson[$key]);
        else
            $totalJson[$key] = $values;
        if (empty($totalJson)) return;
        $this->json_info = json_encode($totalJson, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $this->save();
    }

    public function getJsonInfo(string $key): mixed
    {
        $totalJson = empty($this->json_info) ? [] : json_decode($this->json_info, true);
        if (isset($totalJson[$key]))
            return $totalJson[$key];

        return null;
    }
}

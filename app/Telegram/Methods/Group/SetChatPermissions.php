<?php

namespace App\Telegram\Methods\Group;

use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the setChatPermissions method in the Telegram Bot API.
 */
class SetChatPermissions extends TelegramMethods
{
    public string $methodName = "SetChatPermissions";

    /**
     * Required
     * Unique identifier for the target chat or username of the target supergroup (in the format @supergroupusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * A JSON-serialized object for new default chat permissions
     * @var array
     */
    public array $permissions;

    /**
     * Optional
     * Pass True if chat permissions are set independently. Otherwise, the can_send_other_messages and can_add_web_page_previews permissions will imply the can_send_messages, can_send_audios, can_send_documents, can_send_photos, can_send_videos, can_send_video_notes, and can_send_voice_notes permissions; the can_send_polls permission will imply the can_send_messages permission.
     * @var bool|null
     */
    public ?bool $use_independent_chat_permissions = null;

    /**
     * Constructor with optional clear flag.
     *
     * @param bool $clear
     */
    public function __construct(bool $clear = false)
    {
        // Set default settings
        if ($clear) return;
    }
}

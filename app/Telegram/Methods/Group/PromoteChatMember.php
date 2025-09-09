<?php

namespace App\Telegram\Methods\Group;


use App\Models\User;
use App\Telegram\Types\Keyboard\KeyboardTypes;
use App\Telegram\Types\TelegramRequest\TelegramMethods;

/**
 * This class represents the promoteChatMember method in the Telegram Bot API.
 */
class PromoteChatMember extends TelegramMethods
{
    public string $methodName = "PromoteChatMember";

    /**
     * Required
     * Unique identifier for the target chat or username of the target channel (in the format @channelusername)
     * @var string|int
     */
    public string|int $chat_id;

    /**
     * Required
     * Unique identifier of the target user
     * @var int|User
     */
    public int|User $user_id;

    /**
     * Optional
     * Pass True if the administrator's presence in the chat is hidden
     * @var bool|null
     */
    public bool $is_anonymous;

    /**
     * Optional
     * Pass True if the administrator can access the chat event log, get boost list, see hidden supergroup and channel members, report spam messages and ignore slow mode. Implied by any other administrator privilege.
     * @var bool|null
     */
    public bool $can_manage_chat;

    /**
     * Optional
     * Pass True if the administrator can delete messages of other users
     * @var bool|null
     */
    public bool $can_delete_messages;

    /**
     * Optional
     * Pass True if the administrator can manage video chats
     * @var bool|null
     */
    public bool $can_manage_video_chats;

    /**
     * Optional
     * Pass True if the administrator can restrict, ban or unban chat members, or access supergroup statistics
     * @var bool|null
     */
    public bool $can_restrict_members;

    /**
     * Optional
     * Pass True if the administrator can add new administrators with a subset of their own privileges or demote administrators that they have promoted, directly or indirectly (promoted by administrators that were appointed by him)
     * @var bool|null
     */
    public bool $can_promote_members;

    /**
     * Optional
     * Pass True if the administrator can change chat title, photo and other settings
     * @var bool|null
     */
    public bool $can_change_info;

    /**
     * Optional
     * Pass True if the administrator can invite new users to the chat
     * @var bool|null
     */
    public bool $can_invite_users;

    /**
     * Optional
     * Pass True if the administrator can post stories to the chat
     * @var bool|null
     */
    public bool $can_post_stories;

    /**
     * Optional
     * Pass True if the administrator can edit stories posted by other users, post stories to the chat page, pin chat stories, and access the chat's story archive
     * @var bool|null
     */
    public bool $can_edit_stories;

    /**
     * Optional
     * Pass True if the administrator can delete stories posted by other users
     * @var bool|null
     */
    public bool $can_delete_stories;

    /**
     * Optional
     * Pass True if the administrator can post messages in the channel, or access channel statistics; for channels only
     * @var bool|null
     */
    public bool $can_post_messages;

    /**
     * Optional
     * Pass True if the administrator can edit messages of other users and can pin messages; for channels only
     * @var bool|null
     */
    public bool $can_edit_messages;

    /**
     * Optional
     * Pass True if the administrator can pin messages; for supergroups only
     * @var bool|null
     */
    public bool $can_pin_messages;

    /**
     * Optional
     * Pass True if the user is allowed to create, rename, close, and reopen forum topics; for supergroups only
     * @var bool|null
     */
    public bool $can_manage_topics;

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

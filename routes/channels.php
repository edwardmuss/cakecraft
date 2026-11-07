<?php

use App\Models\WhatsappConversation;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('whatsapp.conversation.{conversationId}', function ($user, $conversationId) {
    return WhatsappConversation::where('id', $conversationId)
        ->where('branch_id', $user->branch_id)
        ->exists();
});

Broadcast::channel('whatsapp.inbox.{accountId}', function ($user, $branchId) {
    return (int) $user->branch_id === (int) $branchId;
});

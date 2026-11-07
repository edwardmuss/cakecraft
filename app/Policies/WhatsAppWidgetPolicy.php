<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WhatsappWidget;

class WhatsappWidgetPolicy
{
    /**
     * Determine if the user's account owns the widget.
     */
    public function update(User $user, WhatsappWidget $widget)
    {
        return $user->branch_id === $widget->branch_id;
    }

    public function delete(User $user, WhatsappWidget $widget)
    {
        return $user->branch_id === $widget->branch_id;
    }

    public function view(User $user, WhatsappWidget $widget)
    {
        return $user->branch_id === $widget->branch_id;
    }

    public function create(User $user)
    {
        // Check if user has permission to create widgets for their account
        return $user->branch !== null;
    }
}

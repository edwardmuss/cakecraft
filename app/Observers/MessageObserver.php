<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\Shortcode;

class MessageObserver
{
    /**
     * Handle the Message "creating" event.
     */
    public function creating(Message $message)
    {
        // Make sure operator is set
        if (empty($message->operator) && $message->phone) {
            $message->operator = identifyOperator($message->phone);
        }

        // Default route
        $route = 'default';

        // Try to match route based on shortcode provider and operator
        if ($message->sender) {
            $shortcode = Shortcode::where('name', $message->sender)->first();

            if ($shortcode) {
                if (strtolower($message->operator) === 'safaricom') {
                    $route = $shortcode->safaricom_provider ?? 'default';
                } elseif (strtolower($message->operator) === 'airtel' || strtolower($message->operator) === 'telkom' || strtolower($message->operator) === 'equitel' || strtolower($message->operator) === 'unknown') {
                    $route = $shortcode->airtel_provider ?? 'default';
                } else {
                    $route = $shortcode->network ?? 'default';
                }
            }
        }

        $message->route = $route;
    }

    /**
     * Handle the Message "created" event.
     */
    public function created(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}

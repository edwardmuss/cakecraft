<?php

namespace App\Services\SMS;

use App\Models\Message;
use Illuminate\Support\Facades\Log;

abstract class BaseSmsService
{
    protected string $route;   // e.g. 'advanta', 'bongatech'
    protected int $limit = 100;
    protected string $logChannel;

    public function __construct()
    {
        $this->logChannel = "{$this->route}_processor";
    }

    /**
     * Fetch messages for the given route and limit
     */
    public function fetchData()
    {
        $messages = Message::where('route', $this->route)
            ->where('processed', 0)
            ->where('time_to_send', '<=', now())
            // ->where('locked', false)
            ->where('sms_status', 'queued')
            ->orderBy('id', 'asc')
            ->take($this->limit)
            ->get();

        $count = $messages->count();
        Log::channel($this->logChannel)->info("{$count} messages selected via route '{$this->route}'");

        if ($count <= 0) {
            $this->sleep();
        }

        return $messages;
    }

    /**
     * Default sleep mechanism when no messages are found
     */
    protected function sleep()
    {
        Log::channel($this->logChannel)->info("No messages found. Sleeping for 5 seconds...");
        sleep(5);
    }

    /**
     * Must be implemented by the child class to send messages
     */
    abstract public function send($messages);
}

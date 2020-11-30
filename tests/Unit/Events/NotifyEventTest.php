<?php

namespace Tests\Unit\Events;

use PHPUnit\Framework\TestCase;
use App\Events\NotifyEvent;
use Illuminate\Broadcasting\PrivateChannel;

class NotifyEventTest extends TestCase
{
    public function test_broadcast_channel()
    {
        $data = [
            'channel' => 1,
            'notifications' => [],
        ];
        $event = new NotifyEvent($data);
        $channel = $event->broadcastOn();
        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertEquals('private-notify.1', $channel->name);
    }
}

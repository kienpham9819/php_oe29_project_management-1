<?php

namespace Tests\Unit\Jobs;

use Tests\TestCase;
use App\Jobs\SendWarning;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotiGrade;
use App\Models\User;

class SendWarningTest extends TestCase
{
    protected $data;
    protected $users;
    protected $sendWarning;

    public function setUp() : void
    {
        parent::setUp();
        $this->data = [
            'grade' => 10,
            'review' => 'good',
        ];
        $this->users = factory(User::class, 5)->make();
        $this->sendWarning = new SendWarning($this->data, $this->users);
    }

    public function tearDown() : void
    {
        unset($this->data);
        unset($this->users);
        unset($this->sendWarning);
        parent::tearDown();
    }

    public function test_handle_success()
    {
        Mail::fake();
        $this->sendWarning->handle();
        Mail::assertSent(NotiGrade::class, function ($mail) {
            return $mail->hasBcc($this->users);
        });
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\User\UserRepositoryInterface;

class NotificationController extends Controller
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->middleware('auth');
        $this->userRepository = $userRepository;
    }

    public function getAllNotifications()
    {
        return $this->userRepository->getNotifications(auth()->user()->id);
    }

    public function markAsRead($notificationId)
    {
        return $this->userRepository->markAsRead(auth()->user()->id, $notificationId);
    }
}

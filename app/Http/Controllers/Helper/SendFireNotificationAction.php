<?php

namespace App\Http\Controllers\Helper;

use App\Http\Controllers\Controller;
use App\Http\Services\Notification\FcmDispatcherService;
use Illuminate\Http\Request;

class SendFireNotificationAction extends Controller
{
    private $fcmDispatcherService;

    public function __construct(
        FcmDispatcherService $fcmDispatcherService
    )
    {
        $this->fcmDispatcherService = $fcmDispatcherService;
    }

    public function __invoke()
    {
        $args = [
            'title' => 'Go cheers yourself',
        ];

        $response = $this->fcmDispatcherService->send($args);

        return response()->json($response);
    }
}

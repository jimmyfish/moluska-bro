<?php

namespace App\Http\Controllers\Dashboard\Notification;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use Illuminate\Support\Str;

class GetEmployeeNotificationAction extends Controller
{
    public function __invoke(Request $request)
    {
        $this->validate($request, [
            'page' => 'nullable|int',
            'perPage' => 'nullable|int'
        ]);

        /** @var Employee $user */
        $user = Auth::guard('api-employee')->user();
        $notifications = $user->notifications()
            ->orderBy('state', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('perPage') ?? 20);

        collect($notifications->items())->each(function ($notification) {
            $notification->title_slug = Str::slug($notification->title);
        });

        return response()->json([
            'meta' => [
                'code' => 200,
                'status' => 'success',
                'paginate' => [
                    'totalData' => $notifications->total(),
                    'perPage'   => $notifications->perPage(),
                    'page'      => $notifications->currentPage(),
                    'lastPage'  => $notifications->lastPage()
                ],
            ],
            'data' => $notifications->items()
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\NotificationTransformer;

class NotificationsController extends Controller
{
    public function index()
    {
        $notification = $this->user->notifications()->paginate(20);

        return $this->response->paginator($notification,new NotificationTransformer());
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Transformers\PermissionTransformer;

class PermissionsController extends Controller
{
    public function index()
    {
        $permission = $this->user()->getAllPermissions();

        return $this->response->collection($permission, new PermissionTransformer());
    }
}

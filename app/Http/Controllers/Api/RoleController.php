<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * 役割一覧取得
     */
    public function index(): JsonResponse
    {
        $roles = Role::all();

        return $this->responseJson($roles);
    }
}

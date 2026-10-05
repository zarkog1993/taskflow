<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\JsonResponse;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Role::all(['id', 'name', 'slug'])
        ]);
    }
}

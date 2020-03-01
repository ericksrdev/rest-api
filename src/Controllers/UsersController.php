<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;
use App\Models\User;

class UsersController extends Controller
{

    public function index(Request $request)
    {
        $users = User::findAll();

        return $this->json($users);
    }

    public function show(Request $request)
    {
        $user_id = $request->get('user_id');

        if ($user_id === null)
        {
            throw new \Exception('Undefined user id value');
        }

        $user = User::find($user_id);

        if ($user === null)
        {
            return $this->json(['message' => 'User not found'], 404);
        }

        return $this->json($user);
    }
}
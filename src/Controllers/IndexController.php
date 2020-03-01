<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;

class IndexController extends Controller
{
    public function index(Request $request)
    {
        return $this->json($request->all(), 201);
    }

    public function sayHi(Request $request)
    {
        return $this->html("<h1>Hello API Rest</h1>");
    }
}
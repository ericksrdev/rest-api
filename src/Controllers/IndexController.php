<?php

namespace App\Controllers;

use App\Lib\Http\Controller;
use App\Lib\Http\Request;
use App\Models\User;

class IndexController extends Controller
{
    public function sayHi(Request $request)
    {
        return $this->html("<h1>Hello API Rest</h1>");
    }
}
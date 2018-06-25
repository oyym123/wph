<?php

namespace App\Api\Controllers;

use App\Api\components\WebController;
use Illuminate\Http\Request;

class HomeController extends WebController
{
    public function successView(Request $request)
    {

        return view('api.home.success', ['data' => $request->input()]);
    }
}

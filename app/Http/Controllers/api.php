<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class api extends Controller
{
    public function index(){
        return response()->json([
            'data' => ['name' => 'john', 'email' => 'john.doe@example.com']
        ]);
    }
}

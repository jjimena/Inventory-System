<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class api extends Controller
{
    public function index(){

        $fakeData = [
            'id' => 1,
            'name' => 'John',
            'email' => 'john@example.com',
        ];

        return response()->json($fakeData);
        
        // return response()->json([
        //     'data' => ['name' => 'john', 'email' => 'john.doe@example.com']
        // ]);
    }
}

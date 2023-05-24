<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get user token.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function getToken(Request $request)
    {
        $user = User::
            where('email', $request->email)
            ->where('password', $request->password)
            ->first();

        return response()->json([
            'token'=> $user->api_token
        ], 200); 
    }
}

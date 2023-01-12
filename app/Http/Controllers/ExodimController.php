<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


use App\Models\ResToken;

use Illuminate\Http\Request;

class ExodimController extends Controller
{
    
    public function checkCadryExodim(Request $request)
    {
        $tok = ResToken::get();

        if($tok->count())
            $token = ResToken::find(1)->res_token; else $token = $this->tokenRefresh();

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $token,
        ])->get('http://cadry.pro/api/administration/checkcadry/' . $request->pinfl);
        
        if($res->status() == 401) {
            $token = $this->tokenRefresh();

            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '. $token,
            ])->get('http://cadry.pro/api/administration/checkcadry/' . $request->pinfl);
        }
        
        return response()->json($res->body());
    }

    public function tokenRefresh()
    {
        $res = Http::post('http://cadry.pro/api/auth/login', [
            'email' => 'admin@gmail.com',
            'password' => 'admin123321',
        ]);
        $tok = ResToken::get();
        if($tok->count()) {
            $token = ResToken::find(1);
            $token->res_token = $res->json('access_token');
            $token->save();
        } else {
            $token = new ResToken();
            $token->res_token = $res->json('access_token');
            $token->save();
        }
        return $res->json('access_token');
    }

}

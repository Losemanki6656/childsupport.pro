<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Railway;
use App\Models\Organization;
use App\Models\TelegramToken;
use App\Models\Member;
use App\Models\Message;
use App\Models\Result;
use App\Http\Resources\RailwayResource;
use App\Http\Resources\OrganizationResource;
use App\Http\Resources\OrganizationCollection;
use App\Http\Resources\MessageCollection;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;


use App\Models\ResToken;

use Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    // public function __construct() {
    //     $this->middleware('auth:api', ['except' => ['login', 'register']]);
    // }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->createNewToken($token);
    }
    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:users',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if($validator->fails()){
            return response()->json($validator->errors()->toJson(), 400);
        }
        $user = User::create(array_merge(
                    $validator->validated(),
                    ['password' => bcrypt($request->password)]
                ));
        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }
    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){

        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function railways(){

        $railways = Railway::all();

        return response()->json([
            'railways' => RailwayResource::collection($railways)
        ]);
    }

    public function organizations(Request $request){

        $organizations = Organization::paginate();

        return response()->json([
            'organizations' => new OrganizationCollection($organizations)
        ]);
    }

    public function send_message(Request $request){

        $org = Organization::where('exodim_org_id', $request->organization_id)->first();

        if($org) {
            $member = Member::where('chat_id', $request->chat_id)->first();

            $message = new Message();
            $message->railway_id = $org->railway_id;
            $message->organization_id = $org->id;
            $message->pinfl = $request->pinfl;
            $message->member_id = $member->id;
            $message->fullname = $request->fullname;
            $message->comment = $request->comment;
            $message->save();
    
           
            return response()->json([
                'message' => "Sizning arizangiz qabul qilindi! Arizangiz 5 ish soatida ko'rib chiqilib sizga ma'lumot yetqaziladi!",
                'message_id' => $message->id,
                'chat_reception_id' => $org->chat_reception_id
            ]);
        } else {
            return response()->json([
                'message' => "Xozirda ushbu xizmatdan foydalanishni imkoniyati mavjud emas! Iltimos bir ozdan keyin qayta urunib ko'ring!"
            ], 400);
        }

        
    }

    public function checkCadryExodim($pinfl)
    {
        $tok = ResToken::get();

        if($tok->count())
            $token = ResToken::find(1)->res_token; else $token = $this->tokenRefresh();

        $res = Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '. $token,
        ])->get('https://exodim.railway.uz/api/administration/checkcadry/' . $pinfl);
        
        if($res->status() == 401) {
            $token = $this->tokenRefresh();

            $res = Http::withHeaders([
                'Accept' => 'application/json',
                'Authorization' => 'Bearer '. $token,
            ])->get('https://exodim.railway.uz/api/administration/checkcadry/' . $pinfl);
        }
        
        return response()->json($res->json());
    }

    public function tokenRefresh()
    {
        $res = Http::post('https://exodim.railway.uz/api/auth/login', [
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

    public function send_token(Request $request){

        $tokens = TelegramToken::get()->count();
        if($tokens == 0) {
            $message = new TelegramToken();
            $message->token = $request->token;
            $message->save();

            return response()->json([
                'message' => "Token muvaffaqqiyatli qo'shildi!"
            ]);

        } else {
            $message = TelegramToken::find(1);
            $message->token = $request->token;
            $message->save();

            return response()->json([
                'message' => "Token muvaffaqqiyatli yangilandi!"
            ]);
        }
          
    }

    public function addreception(Request $request){

        $org = Organization::find($request->organization_id);
        $org->reception_name = $request->reception_name;
        $org->reception_phone = $request->reception_phone;
        $org->reception_staff = $request->reception_staff;
        $org->chat_reception_id = $request->chat_reception_id;
        $org->save();

       
        return response()->json([
            'message' => "Sizning profilingiz ro'yxatdan o'tqazildi!"
        ]);
    }

    public function addmember(Request $request){

        $members = Member::where('chat_id', $request->chat_id)->count();
        
        if($members == 0) {
            $org = new Member();
            $org->chat_id = $request->chat_id;
            $org->name = $request->name;
            $org->phone = $request->phone;
            $org->save();
        }

        return response()->json([
            'message' => "Sizning profilingiz ro'yxatdan o'tqazildi!"
        ]);
    }

    public function updatemember($chat_id, Request $request){

        $org = Member::where('chat_id', $chat_id)->first();
        $org->name = $request->name;
        $org->phone = $request->phone;
        $org->save();

        return response()->json([
            'message' => "Sizning profilingiz yangilandi!"
        ]);
    }

    public function reply_message($message_id, Request $request){

        $org = Message::find($message_id);
        $org->result_id = $request->result_id;
        $org->comment_result = $request->comment_result;
        $org->status_message = true;
        $org->save();

        return response()->json([
            'message' => "Javob yuborildi!"
        ]);
    }

    public function results(){

        $results = Result::get();

        return response()->json([
            'results' => $results
        ]);
    }

    public function information($chat_id){

        $member_id = Member::where('chat_id', $chat_id)->first();

        $messages = Message::where('member_id', $member_id->id)->with(['organization','result'])->paginate(10);

        return response()->json([
            'history' => new MessageCollection($messages)
        ]);
    }
}
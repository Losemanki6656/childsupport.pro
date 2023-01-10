<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Railway;
use App\Models\Organization;
use App\Models\Message;
use App\Http\Resources\RailwayResource;
use App\Http\Resources\OrganizationResource;
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

        $organizations = Organization::query()
            ->when(\Request::input('railway_id'),function($query,$railway_id){
                $query->where(function ($query) use ($railway_id) {
                    $query->where('railway_id',$railway_id);
                });
            })->get();

        return response()->json([
            'organizations' => OrganizationResource::collection($organizations)
        ]);
    }

    public function send_message(Request $request){

        $org = Organization::find($request->organization_id);

        $message = new Message();
        $message->railway_id = $org->railway_id;
        $message->organization_id = $request->organization_id;
        $message->chat_id = $request->chat_id;
        $message->phone = $request->phone;
        $message->token = $request->token;
        $message->fullname = $request->fullname;
        $message->comment = $request->comment;
        $message->save();

       
        return response()->json([
            'message' => "Sizning arizangiz qabul qilindi! Arizangiz 5 ish soatida ko'rib chiqilib sizga ma'lumot yetqaziladi!"
        ]);
    }
}
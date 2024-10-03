<?php
   
namespace App\Http\Controllers;
   
use Illuminate\Http\Request;
use App\Http\Controllers\BaseController as BaseController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\JsonResponse;
   
class AuthenticationController extends BaseController
{
    /**
     * Function for register user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name'      => 'required',
            'email'     => 'required|email|unique:users',
            'password'  => 'required',
            'confirm_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }
   
        $req = $request->all();
        $req['password'] = bcrypt($req['password']);
        $user = User::create($req);
   
        return $this->sendResponse($user, 'User register successfully.');
    }
   
    /**
     * Function for login
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'      => 'required',
            'password'  => 'required',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(), 422);       
        }

        $credentials = $request->only('email', 'password');

        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return $this->sendError('Unauthorized!', [], 401);
        }

        $res['token'] =  $token;
        $res['token_type'] = 'bearer';
        $res['expires_in'] = auth()->factory()->getTTL() * 60;

        return $this->sendResponse($res, 'User login successfully.');
    }

    /**
     * Function for refresh token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request): JsonResponse
    {
        try{
            $res['token'] = auth()->refresh();
            $res['token_type'] = 'bearer';
            $res['expires_in'] = auth()->factory()->getTTL() * 60;
        }catch(\Exception $ex){
            \Log::error('AuthenticationController:refresh :: '.$ex);
            return $this->sendError('Internal server error',[],500);
        }

        return $this->sendResponse($res, 'User login successfully.');
    }
}
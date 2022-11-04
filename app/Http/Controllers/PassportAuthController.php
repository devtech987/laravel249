<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Log;

class PassportAuthController extends Controller
{

        /**
         * handle user registration request
         */
        public function registerUser(Request $request){
           
            $this->validate($request,[
                'name'=>'required',
                'email'=>'required|email|unique:users',
                'password'=>'required|min:8',
            ]);
             
            $user= User::create([
                'name' =>$request->name,
                'email'=>$request->email,
                'password'=>bcrypt($request->password)
            ]);
    
            $access_token = $user->createToken('LaravelAuthApp')->accessToken;
            
            //return the access token we generated in the above step
            return response()->json(['token'=>$access_token],200);
        }
    
        /**
         * login user to our application
         */
        public function loginUser(Request $request){

            $login_credentials=[
                'email'=>$request->email,
                'password'=>$request->password,
            ];
            if(auth()->attempt($login_credentials)){
                //generate the token for the user
                $user_login_token= auth()->user()->createToken('LaravelAuthApp')->accessToken;
                //now return this token on success login attempt
                return response()->json(['token' => $user_login_token], 200);
            }
            else{
                //wrong login credentials, return, user not authorised to our system, return error code 401
                return response()->json(['error' => 'UnAuthorised Access'], 401);
            }
        }

        public function walletamount(Request $req){
            try{
                $user = Auth::user();
                $userId = $user->id;
                $this->validate($req,[
                    'amount'=>'required|numeric|min:3|max:100',
                ]);
                $user = User::where('id', $userId)->first();
                $user->wallet = $req->amount + $user->wallet;
                $user->wallet = number_format((float)$user->wallet, 2, '.', '');
                if($user->save()){
                    return response()->json(['type'=>'success','msg'=>'Amount Add Succesfully']);
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return  response()->json(['type'=>'error','msg'=>$e->getMessage()],400);;
            }
        }
    
        /**
         * This method returns authenticated user details
         */
        public function authenticatedUserDetails(){
            //returns details
            return response()->json(['authenticated-user' => auth()->user()], 200);
        }
    }

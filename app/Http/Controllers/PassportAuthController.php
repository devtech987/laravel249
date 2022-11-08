<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 
use Log;
use Validator;

class PassportAuthController extends Controller
{

        /**
         * handle user registration request
         */
        public function registerUser(Request $request){
            $rules = [
                'name'=>'required|min:4|max:250',
                'email'=>'required|email|unique:users|max:250',
                'password'=>'required|min:8|max:16',
            ];
            $message = [
                'name.required' => 'Name is required',
                'name.min' => 'Name should be 4 letter or greater',
                'name.max' => 'Name can have maximum 250 character',
                'email.required' => 'Email is required',
                'email.email' => 'Email should be valid',
                'email.unique' => 'Email should be unique',
                'email.max' => 'Email can have maximum 250 character',
                'password.required' => 'Password is required',
                'password.min' => 'Password should be 8 or more character',
                'password.max' => 'Password can have maximum 250 character',
            ];
            $validator = Validator::make($request->all(),$rules,$message);
            if ($validator->fails()) {
                return response()->json([
                  'errors' => $validator->errors(),
                  'status' => 400]
                );
            } 
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
                $rules = [
                    'amount'=>'required|numeric|min:3|max:100',
                ];
                $message = [
                    'amount.required' => 'Amount is required',
                    'amount.min' => 'Amount should be greater or equal to 3',
                    'amount.max' => 'Amount can not be more than 100',
                    'amount.numeric'=> 'Amount should be numeric'
                ];
                $validator = Validator::make($req->all(),$rules,$message);
                if ($validator->fails()) {
                    return response()->json([
                      'errors' => $validator->errors(),
                      'status' => 400]
                    );
                } 
                $user = User::where('id', $userId)->increment('wallet',$req->amount);
                return response()->json(['type'=>'success','msg'=>'Amount Add Succesfully']);
                
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

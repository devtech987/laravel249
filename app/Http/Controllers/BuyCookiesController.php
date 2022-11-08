<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CookieTransaction; 
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use DB;
use Log;
use Validator;

class BuyCookiesController extends Controller
{
    //
   function buyCookies(Request $request){
    try {
        $user = Auth::user();
        $validator = Validator::make($request->all(),[
            'no_of_cookie'=>'required|numeric'
        ],[
            'no_of_cookie.required'=> 'Please enter the number of cookie you want to buy',
            'no_of_cookie.numeric'=> 'Value should be numeric'
        ]);
        if ($validator->fails()) {
            return response()->json([
              'errors' => $validator->errors(),
              'status' => 400]
            );
        }
        $userId = $user->id;
        $getUserWallet = User::find($user->id);
        if($getUserWallet->wallet > $request->no_of_cookie){
            $req = $request->all();
            $data = ['no_of_cookie'=>$req['no_of_cookie'], 'price'=>$req['no_of_cookie'],'user_id'=>$user->id];
            $db = CookieTransaction::create($data);
            User::where('id',$userId)->decrement('wallet',$request->no_of_cookie);
            return  response()->json($db,200);
        }else{
            return  response()->json(['type'=>'error','msg'=>'Not Enough Money'],400);
        }
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return  response()->json(['type'=>'error','msg'=>$e->getMessage()],400);;
    }

   }

}

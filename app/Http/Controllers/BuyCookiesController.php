<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CookieTransaction; 
use App\Models\User;
use Illuminate\Support\Facades\Auth; 
use DB;
use Log;

class BuyCookiesController extends Controller
{
    //
   function buyCookies(Request $request){
    try {
        $user = Auth::user();
        $this->validate($request,[
            'no_of_cookie'=>'required'
        ]);
        $userId = $user->id;
        $getUserWallet = User::find($user->id);
        if($getUserWallet->wallet > $request->no_of_cookie){
            $req = $request->all();
            $data = ['no_of_cookie'=>$req['no_of_cookie'], 'price'=>$req['no_of_cookie'],'user_id'=>$user->id];
            $db = CookieTransaction::create($data);
            User::where('id',$userId)->update(['wallet'=>DB::raw("wallet - $request->no_of_cookie")]);
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

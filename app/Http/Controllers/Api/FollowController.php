<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowController extends Controller
{
    /**
     * Add follower to resource.
     *
     * @param  int  $user_id
     * @return \Illuminate\Http\Response
     */
    public function add_follow($user_id){
        if (auth()->check()) {
            $current_user_id = auth()->user()->id;
            $user = auth()->user();
//           dd($user->follows->first());
            if($user_id != $current_user_id) {
                $follower_array = $user->follows;
                $find = false;
                foreach ($follower_array as $key => $value):
                    if($user_id == $value->user_id){
                        $find = true;
                        break;
                    }
                endforeach;
                if($find == true){
                    return response()->json([
                        "success" => false,
                        "message" => "You added follower successfully.",
                    ]);
                }else{
                    $follower = Follow::where('user_id','=',$user_id)->get()->first();
                    if (is_null($follower)) {
                        $follower = Follow::create(array(
                            'user_id' => $user_id
                        ));
                    }
                    $user->follows()->attach($follower->id);
                    return response()->json([
                        "success" => true,
                        "message" => "Add follower successfully.",
                    ]);
                }
            }else{
                return response()->json([
                    "success" => false,
                    "message" => "You can not follow yourself.",
                ]);
            }
        }
    }

    public function user_follow_one()
    {
        $users = DB::table('user_follow')
            ->select('user_id', DB::raw('count(id) as total'))
            ->groupBy('user_id')
            ->get();
        $user_arr = [];
        foreach ($users as $user) {
            if($user->total == 1){
                $user_arr[] = User::find($user->user_id);
            }
        }
        return response()->json([
            "success" => true,
            "message" => "user retrieved successfully.",
            "data" => $user_arr
        ]);
    }

    public function one_follow_users()
    {
        $users = DB::table('user_follow')
            ->select('follow_id', DB::raw('count(id) as total'))
            ->groupBy('follow_id')
            ->get();
        $user_arr = [];
        foreach ($users as $user) {
            if($user->total == 1){
                $follow =  Follow::find($user->follow_id);
                $user_arr[] = User::find($follow->user_id);
            }
        }
        return response()->json([
            "success" => true,
            "message" => "user retrieved successfully.",
            "data" => $user_arr
        ]);
    }

}

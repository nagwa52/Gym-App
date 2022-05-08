<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function update_profile(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [

            'name' => 'min:2|max:55',
            'email' => 'unique:users,email,'.$user->id,
            'avatar_url'=>'nullable|image|mimes:jpg,bmp,png'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message'=>'Validations fails',
                'errors'=>$validator->errors()
            ], 422);
        }

        $user=$request->user();
        $allData=$request->all();
        if ($request->hasFile('avatar_url')) {
            if ($user->avatar_url) {
                $old_path=public_path().'/storage/'.$user->avatar_url;
                if (File::exists($old_path)) {
                    File::delete($old_path);
                }
            }

            $image_name='avatar_url-'.time().'.'.$request->avatar_url->extension();
            $allData['avatar_url']=$image_name;
            $request->avatar_url->move(public_path('/storage/'), $image_name);
        } else {
            $image_name=$user->avatar_url;
            $allData['avatar_url']=$image_name;
        }

        $allData['password'] = $user->password;
        
        $user->update($allData);

        return response()->json([
            'message'=>'Profile successfully updated',
        ], 200);
    }
}

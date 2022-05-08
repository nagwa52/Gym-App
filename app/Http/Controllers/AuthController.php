<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class AuthController extends Controller {
    public function register(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'password' => 'required',
            'national_id' => 'required',
            'gender' => 'required',
            'password_confirmation' => 'required',
            'date_of_birth' => 'required',
            'avatar_url' => 'nullable|image|mimes:jpg,bmp,png'
        ]);

        $validatedData['password'] = bcrypt($request->password);

        if ($request->hasFile('avatar_url')) {
            $image_name = 'avatar_url-' . time() . '.' . $request->avatar_url->extension();
            $validatedData['avatar_url'] = $image_name;
            $request->avatar_url->move(public_path('/storage/'), $image_name);
        }
        $user = User::create($validatedData);
        $user->assignRole('member');

        $event = event(new Registered($user));


        $accessToken = $user->createToken('MyApp')->plainTextToken;

        return response(['user' => $user, 'access_token' => $accessToken, 'event' => $event]);
    }

    public function login(Request $request) {
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            //  return $this->sendResponse($success, 'User login successfully.');
            return response(['user' => $user, 'access_token' => $success['token']]);
        } else {
            return response(['message' => 'Invalid Credentials']);
        }
    }
}

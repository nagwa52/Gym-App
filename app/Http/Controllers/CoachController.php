<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\City;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use \App\Http\Requests\StoreUserRequest;
use yajra\Datatables\Datatables;

class CoachController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            return Datatables::of(Role::where("name", "coach")->first()->users)
                ->addColumn('avatar', function ($Coaches) {
                    $user_img_name = explode('/', $Coaches->avatar_url)[1];
                    $avatar = '<img src="/storage/'
                        . $user_img_name
                        . '" alt="avatar" width="30" height="30">';
                    return $avatar;
                })
                ->addColumn('action', function ($Coaches) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $Coaches->id .
                        '" data-original-title="Edit" class="edit btn btn-primary mx-1 btn-sm editUser">Edit</a>';

                    $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $Coaches->id .
                        '" data-original-title="Edit" class="delete btn btn-danger btn-sm mx-1 deleteUser">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['avatar', 'action'])
                ->make(true);
        } else {
            return view('Coaches.index');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request) {
        $user_img_path = !empty($request->file('user_img')) ?
            $request->file('user_img')->store('public') :
            "public/default_avatar.png";

        $user = User::Create(
            [
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'national_id' => $request->national_id,
                'avatar_url' => $user_img_path,
            ],
        );

        $user->assignRole('coach');

        return response()->json(['success' => 'User saved successfully.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(StoreUserRequest $request, $user_id) {
        $user = User::find($user_id);

        if (!empty($request->file('user_img'))) {
            if ($user->avatar_url != "public/default_avatar.png") File::delete("storage/" . explode('/', $user->avatar_url)[1]);
            $user_img_path = $request->file('user_img')->store('public');
        } else {
            $user_img_path = $user->avatar_url;
        }

        User::find($user_id)->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'national_id' => $request->national_id,
            'avatar_url' => $user_img_path,
        ]);

        return response()->json(['success' => 'User updated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($user_id) {
        $user = User::find($user_id);
        if ($user->avatar_url != "public/default_avatar.png") File::delete("storage/" . explode('/', $user->avatar_url)[1]);
        $user->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}

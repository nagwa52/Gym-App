<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Gym;
use yajra\Datatables\Datatables;
use Illuminate\Support\Facades\File;
use Barryvdh\Debugbar\Facades\Debugbar;

class GymManagerController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request) {
        if ($request->ajax()) {
            return Datatables::of(Role::where("name", "gym_manager")->first()->users)
                ->addColumn('avatar', function ($gym_managers) {
                    $avatar_img_name = explode('/', $gym_managers->avatar_url)[1];
                    $avatar = '<img src="/storage/'
                        . $avatar_img_name
                        . '" alt="avatar" width="30" height="30">';
                    return $avatar;
                })
                ->addColumn('action', function ($gym_managers) {

                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $gym_managers->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editUser">Edit</a>';

                    $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $gym_managers->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteUser">Delete</a>';

                    if (is_null($gym_managers->banned_at)) {
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $gym_managers->id . '" data-original-title="ban" class="ban btn btn-warning btn-sm banUser">ban</a>';
                    } else {
                        $btn = $btn . ' <a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $gym_managers->id . '" data-original-title="ban" class="ban btn btn-success btn-sm unbanUser">unban</a>';
                    }

                    return $btn;
                })
                ->rawColumns(['avatar', 'action'])
                ->make(true);
        } else {
            $userRole = auth()->user()->roles->first()->name;
            switch ($userRole) {
                case 'admin':
                    $gyms = Gym::all();
                    break;
                case 'city_manager':
                    $gyms = Gym::query()->with('has_gyms')
                        ->where('has_gyms_id', auth()->user()->manageable_id)->get();
                    break;
            }

            return view('gym_managers.index', [
                'gyms' => $gyms,
            ]);
        }
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create() {
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
                'manageable_id' => $request->manageable_id,
                'manageable_type' => "App\Models\Gym",
            ],
        );

        $user->assignRole('gym_manager');

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

    public function ban($gym_manager) {
        $gymManager = User::find($gym_manager);

        if (!is_null($gymManager)) {
            // Check if the user is already banned.
            $gymManager->ban();
            return response()->json(['success' => 'Gym manager banned successfully.'], 200);
        }
        return response()->json(['error' => 'Gym manager not found.'], 404);
    }

    public function unban(Request $request, $gym_manager) {
        $gymManager = User::find($gym_manager);
        if (!is_null($gymManager)) {
            // Check if the user is already unbanned.
            $gymManager->unban();
            return response()->json(['success' => 'Gym manager updated successfully.']);
        }
        return response()->json(['error' => 'Gym manager not found.'], 404);
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
        $user->roles()->detach();
        if ($user->avatar_url != "public/default_avatar.png") File::delete("storage/" . explode('/', $user->avatar_url)[1]);
        $user->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}

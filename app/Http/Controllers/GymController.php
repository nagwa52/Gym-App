<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGymRequest;
use Illuminate\Http\Request;

use App\Models\Gym;
use App\Models\City;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

use yajra\Datatables\Datatables;

class GymController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            return Datatables::of(Gym::query())
                ->editColumn('created_at', function ($gym) {
                    return $gym->created_at->format('d-m-Y');
                })
                ->addColumn('gym_creator', function ($gym) {
                    return $gym->creatable->name;
                })
                ->addColumn('action', function ($gym) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $gym->id .
                        '" data-original-title="Edit" class="edit btn btn-primary mx-1 btn-sm editGym">Edit</a>';

                    $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $gym->id .
                        '" data-original-title="Edit" class="delete btn btn-danger btn-sm mx-1 deleteGym">Delete</a>';

                    return $btn;
                })
                ->addColumn('cover', function ($gym) {
                    $gym_img_name = explode('/', $gym->cover_url)[1];
                    $cover = '<img src="/storage/'
                        . $gym_img_name
                        . '" alt="avatar" width="300" height="100">';
                    return $cover;
                })
                ->rawColumns(['action', 'cover'])
                ->make(true);
        } else {
            $cities = City::all();
            return view('gyms.index', [
                'cities' => $cities,
            ]);
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
    public function store(StoreGymRequest $request) {
        $cover_img_path = !empty($request->file('cover_img')) ?
            $request->file('cover_img')->store('public') :
            "public/default_cover.png";

        $gym = Gym::Create(
            [
                'name' => $request->name,
                'cover_url' => $cover_img_path,
                'has_gyms_type' => 'App\Models\City',
                'has_gyms_id' => $request->has_gyms_id,
                'creatable_type' => 'App\Models\User',
                'creatable_id' => auth()->user()->id,
            ],
        );
        return response()->json(['success' => 'Gym saved successfully.']);
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
    public function update(StoreGymRequest $request, $gym_id) {
        $gym = Gym::find($gym_id);

        if (!empty($request->file('cover_img'))) {
            if ($gym->cover_url != "public/default_cover.png") File::delete("storage/" . explode('/', $gym->cover_url)[1]);
            $cover_img_path = $request->file('cover_img')->store('public');
        } else {
            $cover_img_path = $gym->cover_url;
        }

        Gym::find($gym_id)->update([
            'name' => $request->name,
            'cover_url' => $cover_img_path,
            'has_gyms_type' => 'App\Models\City',
            'has_gyms_id' => $request->has_gyms_id,
            'creatable_type' => 'App\Models\User',
            'creatable_id' => auth()->user()->id,
        ]);

        return response()->json(['success' => 'Gym updated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($gym_id) {
        $gym = Gym::find($gym_id);
        if ($gym->cover_url != "public/default_cover.png") File::delete("storage/" . explode('/', $gym->cover_url)[1]);
        $gym->delete();
        return response()->json(['success' => 'Gym deleted successfully.']);
    }
}

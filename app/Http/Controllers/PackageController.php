<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\package;
use App\Models\Gym;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use \App\Http\Requests\StorePackageRequest;
use yajra\Datatables\Datatables;

class PackageController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $userRole = auth()->user()->roles->first()->name;
            switch ($userRole) {
                case 'admin':
                    $packages = package::all();
                    break;

                case 'city_manager':
                    $packages = package::with('has_packages', 'has_packages.has_gyms')
                        ->whereHasMorph('has_packages', [Gym::class], function ($q) {
                            $q->where('has_packages_id', auth()->user()->manageable_id);
                        });
                    break;

                case 'gym_manager':
                    $packages = package::where('has_packages_id', auth()->user()->manageable->id);
                    break;
            }

            return Datatables::of($packages)
                ->editColumn('price', function ($package) {
                    return (int)$package->price;
                })
                ->addColumn('gym', function ($package) {
                    $gym = Package::with('has_packages')
                        ->where('id', $package->id)
                        ->first()->has_packages->name;
                    return $gym;
                })
                ->addColumn('city', function ($package) {
                    $city = Package::with('has_packages', 'has_packages.has_gyms')
                        ->where('id', $package->id)
                        ->first()->has_packages->has_gyms->name;
                    return $city;
                })
                ->addColumn('action', function ($package) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $package->id .
                        '" data-original-title="Edit" class="edit btn btn-primary mx-1 btn-sm editPackage">Edit</a>';

                    $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $package->id .
                        '" data-original-title="Edit" class="delete btn btn-danger btn-sm mx-1 deletePackage">Delete</a>';

                    return $btn;
                })
                ->addColumn('purchase', function ($package) {
                    $btn = '<a href="purchases" data-toggle="tooltip"  data-id="' .
                        $package->id .
                        '" data-original-title="Edit" class="buy btn btn-success mx-1 btn-sm buyPackage">Buy</a>';
                    return $btn;
                })
                ->rawColumns(['action', 'purchase'])
                ->make(true);
        } else {
            $gyms = Gym::all();
            return view('packages.index', [
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
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StorePackageRequest $request) {
        $package = Package::Create(
            [
                'name' => $request->name,
                'price' => $request->price,
                'sessions_amount' => $request->sessions_amount,
                'has_packages_id' => $request->has_packages_id,
                'has_packages_type' => "App\Models\Gym",
            ],
        );

        // $user->assignRole('city_manager');

        return response()->json(['success' => 'User saved successfully.']);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
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
    public function update(StorePackageRequest $request, $package_id) {
        $package = Package::find($package_id);

        Package::find($package_id)->update([
            'name' => $request->name,
            'price' => $request->price,
            'sessions_amount' => $request->sessions_amount,
            'has_packages_id' => $request->has_packages_id,
            'has_packages_type' => "\App\Models\Gym",
        ]);

        return response()->json(['success' => 'User updated successfully.']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($package_id) {
        $package = Package::find($package_id);
        $package->delete();
        return response()->json(['success' => 'User deleted successfully.']);
    }
}

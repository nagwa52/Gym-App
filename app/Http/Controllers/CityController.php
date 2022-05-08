<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCityRequest;
use Illuminate\Http\Request;
use App\Models\City;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use yajra\Datatables\Datatables;

class CityController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            return Datatables::of(City::query()->with('gyms'))
                ->addColumn('gyms', function ($city) {
                    return $city->gyms->pluck('name')->implode(', ');
                })

                ->addColumn('action', function ($city) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $city->id .
                        '" data-original-title="Edit" class="edit btn btn-primary mx-1 btn-sm editCity">Edit</a>';

                    $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $city->id .
                        '" data-original-title="Edit" class="delete btn btn-danger btn-sm mx-1 deleteCity">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        } else {
            $cities = City::all();
            return view('cities.index', [
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
    public function store(StoreCityRequest $request) {
        $city = City::Create(
            [
                'name' => $request->name,
            ],
        );
        return response()->json(['success' => 'City saved successfully.']);
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
    public function update(StoreCityRequest $request, $city_id) {
        $city = City::find($city_id);

        City::find($city_id)->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'City updated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($city_id) {
        $city = City::find($city_id);
        if ($city->gyms()->exists()) {
            return response()->json([
                'error' => 'Cant delete City that has gyms, please delete related gyms first to proceed.'
            ], 403);
        };
        $city->delete();
        return response()->json(['success' => 'City deleted successfully.']);
    }
}

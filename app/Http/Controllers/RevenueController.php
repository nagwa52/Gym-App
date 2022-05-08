<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\session_user;
use App\Models\User;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\Package;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use yajra\Datatables\Datatables;

class RevenueController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $order = Purchase::query();
            return Datatables::of($order)
                ->addColumn('member_name', function ($row) {
                    return User::where('id', $row->sellable_id)->first()->name;
                })
                ->addColumn('email', function ($row) {
                    return User::where('id', $row->sellable_id)->first()->email;
                })
                ->addColumn('package_name', function ($row) {
                    return $row->name;
                })
                ->addColumn('price', function ($row) {
                    return (int)$row->price;
                })
                ->addColumn('gym', function ($row) {
                    $gym = Package::with('has_packages')
                        ->where('name', $row->name)
                        ->first()->has_packages->name;
                    return $gym;
                })
                ->addColumn('city', function ($row) {
                    $city = Package::with('has_packages', 'has_packages.has_gyms')
                        ->where('name', $row->name)
                        ->first()->has_packages->has_gyms->name;
                    return $city;
                })
                ->make(true);
        } else {
            $userRole = auth()->user()->roles->first()->name;
            $total_orders = 0;
            switch ($userRole) {
                case 'admin':
                    $orders = Purchase::all();
                    break;
                case 'city_manager':
                    $orders = Purchase::where('buyable_id', auth()->user()->id)->get();
                    break;
            }
            foreach ($orders as $order) {
                $total_orders += $order->price;
            }
            return view('revenue.index', [
                'total_orders' => $total_orders,
            ]);
        }
    }
}

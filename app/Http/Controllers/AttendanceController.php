<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\session_user;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use yajra\Datatables\Datatables;

class AttendanceController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        if ($request->ajax()) {
            $attendance_history = session_user::query()
                ->whereNotNull('attendance_date')->get();
            return Datatables::of($attendance_history)
                ->editColumn('attendance_time', function ($row) {
                    return Carbon::parse($row->attendance_time)->format('g:i a');
                })
                ->addColumn('member_name', function ($row) {
                    return User::where('id', $row->user_id)->first()->name;
                })
                ->addColumn('email', function ($row) {
                    return User::where('id', $row->user_id)->first()->email;
                })
                ->addColumn('session_name', function ($row) {
                    return Session::where('id', $row->session_id)->first()->name;
                })
                ->addColumn('gym', function ($row) {
                    $gym = Session::with('has_sessions')->where('id', $row->session_id)
                        ->first()->has_sessions->name;
                    return $gym;
                })
                ->addColumn('city', function ($row) {
                    $gym = Session::with('has_sessions', 'has_sessions.has_gyms')->where('id', $row->session_id)
                        ->first()->has_sessions->has_gyms->name;
                    return $gym;
                })
                ->make(true);
        } else {
            return view('attendance.index');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Session;
use App\Models\Purchase;
use App\Models\Session_user;
use App\Models\City;
use App\Models\Gym;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;
use App\Http\Requests\StoreSessionRequest;
use yajra\Datatables\Datatables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionController extends Controller {
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
                    $sessions = Session::query()->with('has_sessions', 'has_sessions.has_gyms');
                    break;

                case 'city_manager':
                    $sessions = Session::query()->with('has_sessions', 'has_sessions.has_gyms')
                        ->whereHasMorph('has_sessions', [Gym::class], function ($q) {
                            $q->where('has_gyms_id', auth()->user()->manageable_id);
                        });
                    break;

                case 'gym_manager':
                    $sessions = Session::query()->with('has_sessions', 'has_sessions.has_gyms')
                        ->where('has_sessions_id', auth()->user()->manageable->id);
                    break;
            }

            return DataTables::of($sessions)
                ->addColumn('day', function ($session) {
                    return Carbon::parse($session->starts_at)->format('d/m/Y');
                })

                ->editColumn('starts_at', function ($session) {
                    return Carbon::parse($session->starts_at)->format('g:i a');
                })

                ->editColumn('finishes_at', function ($session) {
                    return Carbon::parse($session->finishes_at)->format('g:i a');
                })

                ->addColumn('gym', function ($session) {
                    return $session->has_sessions->name;
                })

                ->addColumn('city', function ($session) {
                    return $session->has_sessions->has_gyms->name;
                })

                ->addColumn('coaches', function (Session $session) {
                    $query = Session::query()->with(['users' => function ($q) {
                        $q->role('coach');
                    }])->where("name", $session->name)->first()->users->pluck('name')->implode(', ');
                    return $query;
                })

                ->addColumn('members', function (Session $session) {
                    $query = Session::query()->with(['users' => function ($q) {
                        $q->role('member');
                    }])->where("name", $session->name)->first()->users->pluck('name')->implode(', ');
                    return $query;
                })

                ->addColumn('action', function ($session) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $session->id .
                        '" data-original-title="Edit" class="edit btn btn-primary mx-1 btn-sm editSession">Edit</a>';

                    $btn = $btn . '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' .
                        $session->id .
                        '" data-original-title="Edit" class="delete btn btn-danger btn-sm mx-1 deleteSession">Delete</a>';

                    return $btn;
                })
                ->rawColumns(['action'])

                ->make(true);
        } else {
            $userRole = auth()->user()->roles->first()->name;

            $coaches = Role::where("name", "coach")->first()->users;
            $members = Role::where("name", "member")->first()->users;

            switch ($userRole) {
                case 'admin':
                    $gyms = Gym::all();
                    break;
                case 'city_manager':
                    $gyms = Gym::query()->with('has_gyms')
                        ->where('has_gyms_id', auth()->user()->manageable_id)->get();
                    break;
                case 'gym_manager':
                    $gyms = Gym::where('id', auth()->user()->manageable_id)->get();
                    break;
            }
            return view('sessions.index', [
                'coaches' => $coaches,
                'members' => $members,
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
    public function store(StoreSessionRequest $request) {
        // Adding session details.
        $session = Session::Create(
            [
                'name' => $request->name,
                'starts_at' => $request->starts_at,
                'finishes_at' => $request->finishes_at,
                'has_sessions_type' => 'App\Models\Gym',
                'has_sessions_id' => $request->has_sessions_id,
            ],
        );

        // Connecting the session with their coaches
        $session_coaches_ids = explode(',', $request->coaches);
        foreach ($session_coaches_ids as $user_id) {
            session_user::Create(
                [
                    'session_id' => $session->id,
                    'user_id' => $user_id,
                ],
            );
        }
        return response()->json(['success' => 'Session saved successfully.']);
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
    public function update(StoreSessionRequest $request, $session_id) {
        $session = Session::find($session_id);
        if (!empty($session->with(['users' => function ($q) {
            $q->role('member');
        }])->where("name", $session->name)->first()->users->pluck('name')->implode(', '))) {
            return response()->json([
                'error' => 'Cant modify a Session that has members, Please choose another session.'
            ], 403);
        };

        DB::transaction(function () use ($request, $session_id) {
            $session = Session::find($session_id);
            $session->update(
                [
                    'name' => $request->name,
                    'starts_at' => $request->starts_at,
                    'finishes_at' => $request->finishes_at,
                    'has_sessions_type' => 'App\Models\Gym',
                    'has_sessions_id' => $request->has_sessions_id,
                ],
            );

            // Connecting the session with their coaches
            $session_coaches_ids = explode(',', $request->coaches);

            // Delete old session users.
            session_user::where('session_id', $session_id)->delete();

            // Add the new session users.
            foreach ($session_coaches_ids as $user_id) {
                session_user::Create(
                    [
                        'session_id' => $session_id,
                        'user_id' => $user_id,
                    ],
                );
            }
        });
        return response()->json(['success' => 'Session updated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($session_id) {
        $session = Session::find($session_id);
        if (!empty($session->with(['users' => function ($q) {
            $q->role('member');
        }])->where("name", $session->name)->first()->users->pluck('name')->implode(', '))) {
            return response()->json([
                'error' => 'Cant delete a Session that has members, Please choose another session.'
            ], 403);
        };
        Session::find($session_id)->delete();
        session_user::where('session_id', $session_id)->delete();
        return response()->json(['success' => 'Session deleted successfully.']);
    }

    public function calculate_remaining(Request $request, $user_id) {
        $userSessions = Purchase::where('sellable_id', $user_id)->get();
        $totalSessions = 0;
        foreach ($userSessions as $userSession) {
            $totalSessions += $userSession->sessions_amount;
        }
        $attendedSessions = session_user::where('user_id', $user_id)->count();
        $remainingSessions = $totalSessions - $attendedSessions;
        return response()->json(['remainingSessions' => $remainingSessions, 'totalSessions' => $totalSessions]);
    }


    public function calculate_attendance(Request $request, $user_id) {
        $sessions_name = User::find($user_id)->sessions()->select('name')->orderBy('name')->get();
        $gym_id = Purchase::select('gym_id')->where('sellable_id', $user_id)->first();
        $gym_name = Gym::select('name')->where('id', $gym_id->gym_id)->get();
        $dates = session_user::select('attendance_date', 'attendance_time')->where('user_id', $user_id)->get();
        return response()->json(['training Sessions name' => $sessions_name, 'gym name' => $gym_name, 'attendance date and time' => $dates]);
    }


    public function attend_session(Request $request, $user_id) {
        $Sessions = $this->calculate_remaining($request, $user_id)->original;
        $sessions_id = $request->session_id;
        $remainingSessions = $Sessions['remainingSessions'];
        $date = $request->attendance_date;
        $time = $request->attendance_time;
        if ($remainingSessions == 0) {
            return  response()->json([
                'message' => 'you need to buy training sessions in order to attend'
            ]);
        }

        if ($date !== Carbon::now()->format('Y-m-d')) {
            return  response()->json([
                'message' => 'it is not allowed to attend '
            ]);
        }
        Session_user::create([
            'attendance_date' => $date,
            'attendance_time' => $time,
            'session_id' => $sessions_id,
            'user_id' => $user_id,
        ]);
        return response()->json(['message' => 'success']);
    }
}

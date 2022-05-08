<?php

namespace App\Http\Controllers;

use App\DataTables\PurchasesDataTable;
use App\Http\Resources\PurchaseResource;
use App\Models\Gym;
use App\Models\Purchase;
use App\Http\Requests\UpdatePurchaseRequest;
use App\Models\TrainingPackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Yajra\DataTables\Facades\DataTables;
use Spatie\Permission\Models\Role;
use \App\Http\Requests\StoreUserRequest;
use App\Models\Package;
use Stripe;


class PurchaseController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $members = Role::where("name", "member")->first()->users;
        $packages = Package::query()->with('has_packages')
            ->where('has_packages_id', auth()->user()->manageable->id)->get();
        
        return view('purchases.index', [
            'packages' => $packages,
            'members' => $members,
        ]);
    }

    public function store(Request $request) {
        $package = Package::find($request->package);
        Purchase::create([
            'name' => $package->name,
            'price' => $package->price,
            'sessions_amount' => $package->sessions_amount,
            'buyable_type' => 'App\Models\User',
            'buyable_id' => Auth::user()->id,
            'sellable_type' => 'App\Models\User',
            'sellable_id' => $request->user,
            'gym_id' => Auth::user()->manageable->id,

        ]);

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));
        $session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => $package->name . ' Training Package',
                    ],
                    'unit_amount' => (int)$package->price * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => URL::to('/purchases/finish/success'),
            'cancel_url' => URL::to('/purchases/finish/cancel'),
        ]);
        return redirect($session->url, 303);
    }
    public function pay($status) {
        $purchase_id = DB::table("purchases")->where('is_paid', 0)->where('buyable_id', Auth::user()->id)->max('id');
        if ($purchase_id) {
            $purchase = DB::table("purchases")->where('id', $purchase_id);
            if ($status == 'success') {
                $purchase->update(['is_paid' => true]);
                Session::flash('message', 'Payment Finished Successfully');
                Session::flash('alert-class', 'alert-success');
                return redirect()->route('purchases.create');
            } else {
                $purchase->delete();
            }
        }

        Session::flash('message', 'Payment Canceled!');
        Session::flash('alert-class', 'alert-danger');
        return redirect()->route('purchases.create');
    }
}

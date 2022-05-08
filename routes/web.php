<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CityManagerController;
use App\Http\Controllers\GymManagerController;
use App\Http\Controllers\GymController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\CoachController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\RevenueController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes(['register' => false]);

Route::get('/ban', function () {
    return view('ban');
});

Route::redirect('/', 'login');


Route::group(['middleware' => ['auth', 'logs-out-banned-user', 'forbid-banned-user']], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home');

    Route::resource('city_managers', CityManagerController::class)->middleware('permission:CRUD_city_managers');

    Route::resource('gym_managers', GymManagerController::class)->middleware('permission:CRUD_gym_managers');
    Route::delete('gym_managers/{gym_manager}/ban', ['uses' => GymManagerController::class . '@ban', 'middleware' => 'permission:CRUD_gym_managers']);
    Route::get('gym_managers/{gym_manager}/unban', ['uses' => GymManagerController::class . '@unban', 'middleware' => 'permission:CRUD_gym_managers']);

    Route::resource('coaches', CoachController::class)->middleware('permission:CRUD_coaches');

    Route::resource('members', MemberController::class)->middleware('permission:CRUD_members');

    Route::resource('cities', CityController::class)->middleware('permission:CRUD_cities');

    Route::resource('gyms', GymController::class)->middleware('permission:CRUD_gyms');

    Route::resource('sessions', SessionController::class)->middleware('permission:CRUD_sessions');

    Route::get('attendance', [AttendanceController::class, 'index'])->name('attendance.index')->middleware('permission:Read_attendance');

    Route::resource('packages', PackageController::class)->middleware('permission:CRUD_packages|Read_packages');

    Route::get('revenue', [RevenueController::class, 'index'])->name('revenue.index')->middleware('permission:Read_revenue');

    Route::get('purchases/', [PurchaseController::class, 'index'])->name('purchases.index')->middleware('permission:Purchase_package');
    Route::post('purchases/store', [PurchaseController::class, 'store'])->name('purchases.store')->middleware('permission:Purchase_package');

    Route::get('purchases/finish/{status}', [PurchaseController::class, 'pay'])->name('payment');
});

<?php

use App\Http\Controllers\API\ParkingLotController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group(['auth:sanctum'], function() {
    Route::get('parkingLot', [ParkingLotController::class, 'get']);
    Route::get('parkingLot/motorcycle', [ParkingLotController::class, 'getByMotorcycle']);
    Route::get('parkingLot/car', [ParkingLotController::class, 'getByCar']);
    Route::get('parkingLot/van', [ParkingLotController::class, 'getByVan']);
});

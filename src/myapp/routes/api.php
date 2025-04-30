<?php

use App\Http\Controllers\ParkingController;
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

Route::post('/parking-spot/{id}/unpark', [ParkingController::class, 'unpark']);
Route::post('/parking-spot/{id}/park', [ParkingController::class, 'park']);
Route::get('/parking-lot', [ParkingController::class, 'parkingLot']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

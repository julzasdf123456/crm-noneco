<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ServiceConnectionInspectionsAPI;
use App\Http\Controllers\API\TelleringController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ServiceConnectionsEnergization;
use App\Http\Controllers\API\OtherData;
use App\Http\Controllers\API\TicketrepositoriesController;
use App\Http\Controllers\API\TicketsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// VERIFIES
// DOWNLOAD DATA
Route::get('get-service-connections/', [ServiceConnectionInspectionsAPI::class, 'getServiceConnections']);
Route::get('get-service-inspections/', [ServiceConnectionInspectionsAPI::class, 'getServiceInspections']);
Route::post('update-service-inspections/', [ServiceConnectionInspectionsAPI::class, 'updateServiceInspections']);

Route::get('get-payment-queues/', [TelleringController::class, 'fetchApprovedServiceConnections']);

// FOR ENERGIZATION ON CREW
Route::get('get-for-energization-data', [ServiceConnectionsEnergization::class, 'getForEnergizationData']);
Route::get('get-inspections-for-energization-data', [ServiceConnectionsEnergization::class, 'getInspectionsForEnergizationData']);
Route::post('update-energized', [ServiceConnectionsEnergization::class, 'updateEnergized']);
Route::post('create-timeframes', [ServiceConnectionsEnergization::class, 'createTimeFrames']);

Route::get('get-towns', [OtherData::class, 'getTowns']);
Route::get('get-barangays', [OtherData::class, 'getBarangays']);
Route::get('get-all-crew', [OtherData::class, 'getAllCrew']);

Route::post('login', [UserController::class, 'login']);

// TICKETS
Route::get('get-ticket-types', [TicketrepositoriesController::class, 'getTicketTypes']);
Route::get('get-downloadable-tickets', [TicketsController::class, 'getDownloadableTickets']);
Route::get('update-downloaded-status', [TicketsController::class, 'updateDownloadedStatus']);

// IMAGES
Route::post('save-uploaded-images', [ServiceConnectionsEnergization::class, 'saveUploadedImages']);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



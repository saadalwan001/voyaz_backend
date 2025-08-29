<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\Admin\AttractionController;

//Public route adding point before Sanctum auth
Route::get('/latest-packages', [TourPackageController::class, 'latest']);
Route::get('/packages/{id}', [TourPackageController::class, 'showPublic']); // to fetch pack detials in new page
Route::post('/send-enquiry', [EnquiryController::class, 'send']);


Route::post('/admin-login', [AdminAuthController::class, 'login']);
Route::post('/admin-logout', [AdminAuthController::class, 'logout'])->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/admin-dashboard', function () {
        return response()->json(['message' => 'Welcome Admin', 'admin' => auth()->user()]);
    });

    // Tour Package Routes
    Route::get('/admin-packages', [TourPackageController::class, 'index']);
    Route::get('/admin-packages/{id}', [TourPackageController::class, 'show']);
    Route::post('/admin-packages', [TourPackageController::class, 'store']);
    Route::patch('/admin-packages/{id}', [TourPackageController::class, 'update']);
    Route::delete('/admin-packages/{id}', [TourPackageController::class, 'destroy']);
    Route::patch('/admin-packages/{id}/toggle', [TourPackageController::class, 'toggle']);
    Route::get('/admin-packages/all',[TourPackageController::class,'allPackages']);



    // Itineraries
    Route::get('/admin-packages/{package}/itineraries', [ItineraryController::class, 'index']);
    Route::post('/admin-packages/{package}/itineraries', [ItineraryController::class, 'store']);
    Route::patch('/admin-itineraries/{itinerary}', [ItineraryController::class, 'update']);
    Route::delete('/admin-itineraries/{itinerary}', [ItineraryController::class, 'destroy']);

    //Attraction & Experience related Images and other data routes
    Route::get('/admin-attractions', [AttractionController::class, 'index']);
    Route::get('/admin-attractions/{id}', [AttractionController::class, 'show']);
    Route::post('/admin-attractions', [AttractionController::class, 'store']);
    Route::patch('/admin-attractions/{id}', [AttractionController::class, 'update']);
    Route::delete('/admin-attractions/{id}', [AttractionController::class, 'destroy']);
});

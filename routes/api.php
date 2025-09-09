<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\BlogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\Admin\TourPackageController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\Admin\AttractionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\Admin\CompanyContactController;




//Public route adding point before Sanctum auth
Route::get('/latest-packages', [TourPackageController::class, 'latest']);
Route::get('/packages/{id}', [TourPackageController::class, 'showPublic']); // to fetch pack detials in new page
Route::post('/send-enquiry', [EnquiryController::class, 'send']);


//public routes for attraction and destinations
Route::get('/attractions',[AttractionController::class, 'index']);
Route::get('/attractions/latest',[AttractionController::class, 'latest']);
Route::get('/attractions/{id}', [AttractionController::class, 'show']);
Route::get('/attractions/{id}/tour-packages', [AttractionController::class, 'tourPackages']);

//public routes for comments
Route::post('/comments', [CommentController::class, 'store']);
Route::get('/comments/{blogId}',[CommentController::class,'index']);

//public routes for company contact
Route::get('/company-contact', [CompanyContactController::class, 'index']);
Route::put('/company-contact/{id}', [CompanyContactController::class, 'update']);

//blog frontend visible parts
Route::get('/admin-blogs', [BlogController::class, 'index']);
Route::get('/admin-blogs/{id}', [BlogController::class,'show']);




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

    //to  blog insertion, update and delete
    Route::post('/admin-blogs',[BlogController::class, 'store']);
    Route::patch('/admin-blogs/{id}',[BlogController::class, 'update']);
    Route::delete('/admin-blogs/{id}', [BlogController::class,'destroy']);

    //to provide admin name and id as dropdown
    Route::get('/admins', [AdminController::class, 'index']);

    // fetch admin details to profile page
    Route::get('/admin-profile', [AdminController::class, 'getProfile']);


    //to update admin profile
    Route::patch('/admin-profile', [AdminController::class, 'updateProfile']);


});

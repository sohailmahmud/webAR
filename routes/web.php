<?php

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

// Auth
Auth::routes([
    'verify' => true, 
    'register' => false,
    'reset' => true // password reset
]);

// HOME
Route::get('/', 'HomeController@index');

// AR
Route::get('/ar/{scene}','ArController@browser');
Route::get('/ar-test/{custommarker}','ArController@test');

Route::middleware(['web', 'auth', 'verified', 'notblocked'])->group(function () {

    // Dashboard
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');       

    // Configs
    Route::get('/configs','ConfigController@index');                                
    Route::post('/configs','ConfigController@index');                               
    Route::get('/configs/preview/qrcode','ConfigController@previewQrCode');         
    
    // Users
    Route::get('/users', 'UserController@admin_index')->name('users_index');           
    Route::get('/users/add', 'UserController@admin_add');                           
    Route::post('/users/add', 'UserController@admin_add')->name('users_add');       
    Route::get('/users/edit/{user}', 'UserController@edit');                        
    Route::put('/users/edit/{user}', 'UserController@edit')->name('users_edit');    
    Route::get('/users/email/resend/{user}', 'UserController@admin_resend'); 
    Route::delete('/users/{user}','UserController@admin_delete');         

    // Scenes
    Route::get('/scene/create','SceneController@create');                         
    Route::get('/myscenes','SceneController@index');                            
    Route::get('/scenes','SceneController@admin_index');                            
    Route::get('/scenes/{scene}','SceneController@view');                         
    Route::get('/scenes/edit/{scene}','SceneController@edit');                                          
    Route::put('/scenes/{scene}','SceneController@addOrEdit');                  
    Route::delete('/scenes/{scene}','SceneController@delete');                

    // Entities
    Route::post('/entities','EntityController@add');                                
    Route::put('/entities/{entity}','EntityController@edit');                       
    Route::delete('/entities/{entity}','EntityController@delete');                  

    // Markers
    Route::post('/markers','MarkerController@add');                                 
    Route::put('/markers/{marker}','MarkerController@edit');                        
    Route::get('/markers/{marker}/download','MarkerController@download');   
    
    // Custom Markers
    Route::get('custommarker/create', 'CustomMarkerController@create');
    Route::get('/mycustommarkers', 'CustomMarkerController@index');
    Route::get('/custommarkers', 'CustomMarkerController@admin_index');
    Route::get('/custommarkers/edit/{custommarker}', 'CustomMarkerController@edit');
    Route::put('/custommarkers/edit/{custommarker}', 'CustomMarkerController@edit');
    Route::delete('/custommarkers/{custommarker}', 'CustomMarkerController@delete');

    // EXTENSIONS
    Route::post('/extensions', 'ExtensionController@store');
    Route::put('/extensions/{extension}', 'ExtensionController@store');
    Route::delete('/extensions/{extension}', 'ExtensionController@delete');
    Route::get('/extensions/scene/{scene_id}', 'ExtensionController@extensionsByScene');

});
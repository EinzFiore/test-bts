<?php

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

Route::group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    $router->group([
        'prefix' => 'auth',
    ], function($router) {
        $router->post('login', 'AuthController@login');
        $router->post('register', 'AuthController@register');
    });

    Route::group([
        'middleware' => 'auth'
    ], function($router) {
        // ROUTE ITEMS
        $router->get('/items', 'ItemController@listItem');
        $router->post('/items', 'ItemController@newItem');
        
        // ROUTE CHECKLISTS
        $router->get('/checklist', 'ChecklistController@listChecklist');
        $router->post('/checklist', 'ChecklistController@newChecklist');
        $router->delete('/checklist/{id}', 'ChecklistController@deleteChecklist');

        // ROUTE CHECKLIST WITH ITEMS
        $router->get('/checklist/{id}/item', 'ChecklistItemController@list');
        $router->post('/checklist/{id}/item', 'ChecklistItemController@store');
        $router->get('/checklist/{id}/item/{id}', 'ChecklistItemController@show');
        $router->put('/checklist/{id}/item/{id}', 'ChecklistItemController@update');
        $router->delete('/checklist/{id}/item/{id}', 'ChecklistItemController@destroy');
        $router->put('/checklist/{id}/item/rename/{id}', 'ChecklistItemController@renameItem');
    });
});

<?php


use App\Http\API\V1\Controllers\Admin\Category\CategoryController;
use App\Http\API\V1\Controllers\Admin\Permission\PermissionController;
use App\Http\API\V1\Controllers\Admin\Product\ProductController;
use App\Http\API\V1\Controllers\Admin\Role\RoleController;
use App\Http\API\V1\Controllers\Admin\User\UserController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'admin'], function () {
    Route::controller(RoleController::class)->group(function () {
        Route::get('roles/{role}/permissions', 'indexPermissions');
        Route::post('roles/{role}/permissions', 'storePermissions');
    });
    Route::controller(UserController::class)->group(function () {
        Route::prefix('users')->group(function () {
            Route::get('{user}/roles', 'indexRoles');
            Route::post('{user}/roles', 'storeRoles');

        });
        Route::get('profile', 'profile');
    });

    Route::apiResources([
         'permissions' => PermissionController::class,
         'users' => UserController::class,
         'roles' => RoleController::class,
         'categories' => CategoryController::class,
         'products' => ProductController::class,
]);



});



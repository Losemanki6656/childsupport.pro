<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('railway', 'RailwayCrudController');
    Route::crud('organization', 'OrganizationCrudController');
    Route::crud('message', 'MessageCrudController');
    Route::crud('result', 'ResultCrudController');

    Route::get('send-sms-to-worker/{id}', 'MessageCrudController@getSendSmsToWorker')
        ->name('get-send-sms-to-worker');

    Route::post('send-sms-to-worker', 'MessageCrudController@postSendSmsToWorker')
        ->name('post-send-sms-to-worker');
    Route::crud('member', 'MemberCrudController');
}); // this should be the absolute last line of this file
<?php

Route::redirect('/', 'login');
Route::get('/home', function () {
    if (session('status')) {
        return redirect()->route('admin.home')->with('status', session('status'));
    }

    return redirect()->route('admin.home');
});

Auth::routes(['register' => false]);
// Admin

Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    // Permissions
    Route::delete('permissions/destroy', 'PermissionsController@massDestroy')->name('permissions.massDestroy');
    Route::resource('permissions', 'PermissionsController');

    // Roles
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');

    // Users
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');
    Route::resource('users', 'UsersController');

    // Facility
    Route::delete('facilities/destroy', 'FacilitiesController@massDestroy')->name('facilities.massDestroy');
    Route::get('facilities/state-email', 'FacilitiesController@stateEmail')->name('facilities.stateEmail');
    Route::post('facilities/state-email', 'FacilitiesController@stateEmail')->name('facilities.stateEmail');
    Route::resource('facilities', 'FacilitiesController');

    // Inwards
    Route::delete('inwards/destroy', 'InwardsController@massDestroy')->name('inwards.massDestroy');
    Route::get('inwards/pick-samples', 'InwardsController@pickSamples')->name('inwards.pick');
    Route::get('inwards/select-samples', 'InwardsController@selectSamples')->name('inwards.select');
    Route::get('inwards/status-samples', 'InwardsController@statusSamples')->name('inwards.status');
    Route::post('inwards/status-update', 'InwardsController@status_update')->name('inwards.status_update');
    Route::get('inwards/mark-status', 'InwardsController@markStatus')->name('inwards.markStatus');
    Route::get('inwards', 'InwardsController@index')->name('inwards.index');
    Route::get('inwards/review/{last_id}/{latest_id}', 'InwardsController@review')->name('inwards.review');
    Route::post('inwards/review-update', 'InwardsController@bulk_update')->name('inwards.review_update');
    Route::post('inwards/pick-update', 'InwardsController@pick_update')->name('inwards.pick_update');
    Route::post('inwards/index', 'InwardsController@index')->name('inwards');
    Route::get('inwards/index', 'InwardsController@index')->name('inwards');
    Route::post('inwards/check-sample-ids', 'InwardsController@checkSampleIds')->name('inwards.checkSampleIds');
    Route::get('inwards/bulk-samples', 'InwardsController@bulkSample')->name('inwards.bulkSample');
    Route::post('inwards/bulk-samples', 'InwardsController@bulkSample')->name('inwards.bulkSample');
    Route::post('inwards/edit-sample', 'InwardsController@editSample')->name('inwards.editSample');
    Route::resource('inwards', 'InwardsController');

    Route::get('reports/generate', 'ReportsController@generate')->name('reports.generate');
    Route::post('reports/generate', 'ReportsController@generate')->name('reports.generate');
    Route::post('reports/reverify', 'ReportsController@reverify')->name('reports.reverify');
    Route::get('reports', 'ReportsController@index')->name('reports.index');
    Route::post('reports', 'ReportsController@index')->name('reports.index');
    Route::get('reports/state-reports', 'ReportsController@stateReports')->name('reports.stateReports');
    Route::post('reports/state-reports', 'ReportsController@stateReports')->name('reports.stateReports');
    Route::get('reports/state-pdf-download/{id}', 'ReportsController@downloadStateReportPdf')->name('reports.downloadStateReportPdf');
    Route::post('reports/state-mail-review/{id}', 'ReportsController@stateMailReview')->name('reports.stateMailReview');
    Route::post('reports/individual-report/{id}', 'ReportsController@individualPdf')->name('reports.individualPdf');
    Route::post('reports/state-mail-send', 'ReportsController@sendReportEmail')->name('reports.sendReportEmail');
    Route::post('reports/preview', 'ReportsController@previewReport')->name('reports.preview');
    Route::get('reports/pdf-download/{id}', 'ReportsController@downloadPdf')->name('reports.pdf');
    Route::get('reports/excel-download/{id}', 'ReportsController@downloadExcel')->name('reports.excel');
    Route::post('reports/change-status', 'ReportsController@changeStatus')->name('reports.changeStatus');
	
	Route::get('reports/dline-reports', 'ReportsController@dlineReports')->name('reports.dlineReports');
    Route::post('reports/dline-reports', 'ReportsController@dlineReports')->name('reports.dlineReports');
    Route::get('reports/dline-pdf-download/{id}', 'ReportsController@downloadDlineReportPdf')->name('reports.downloadDlineReportPdf');
    Route::post('reports/dline-mail-review/{id}', 'ReportsController@dlineMailReview')->name('reports.dlineMailReview');
    Route::post('reports/dline-mail-send', 'ReportsController@sendDlineReportEmail')->name('reports.sendDlineReportEmail');
    //Route::resource('reports', 'ReportsController');
});
Route::group(['prefix' => 'profile', 'as' => 'profile.', 'namespace' => 'Auth', 'middleware' => ['auth']], function () {
// Change password
    if (file_exists(app_path('Http/Controllers/Auth/ChangePasswordController.php'))) {
        Route::get('password', 'ChangePasswordController@edit')->name('password.edit');
        Route::post('password', 'ChangePasswordController@update')->name('password.update');
        Route::post('sign_password', 'ChangePasswordController@updateSignPassword')->name('password.updateSignPassword');
    }
});
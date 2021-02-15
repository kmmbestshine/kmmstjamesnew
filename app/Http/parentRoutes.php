<?php

Route::group(['prefix'=>'parent', 'namespace'=>'Parent','middleware'=>'auth'], function(){

	Route::get('dashboard', ['as'=>'parent.dashboard', 'uses'=>'mainController@dashboard']);

	Route::get('attendance', ['as'=>'parent.attendance','uses'=>'mainController@attendance']);

	Route::get('homework', ['as'=>'parent.homework', 'uses'=>'mainController@homeWork']);

	Route::get('leave/request', ['as'=>'parent.leaveRequest', 'uses'=>'mainController@leaveRequest']);

	Route::post('leave/request/post', ['as'=>'parent.leavePost', 'uses'=>'mainController@leavePost']);

	Route::get('marks', ['as'=>'parent.markes', 'uses'=>'mainController@marks']);

	Route::get('resources/school/profile', ['as'=>'parent.schoolProfile', 'uses'=>'mainController@schoolProfile']);

	Route::get('resources/employee', ['as'=>'parent.employee', 'uses'=>'mainController@resourceEmployee']);

	Route::get('resources/time-table', ['as'=>'parent.timeTable', 'uses'=>'mainController@timeTable']);

	Route::get('resources/fee/structure', ['as'=>'parent.feeStructure', 'uses'=>'mainController@feeStructure']);

	Route::get('gallery', ['as'=>'parent.gallery', 'uses'=>'mainController@gallery']);
});
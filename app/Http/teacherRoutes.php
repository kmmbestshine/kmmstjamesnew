<?php
/*
|----------------------------------------------
|---------Teacher Panel------------------------
|----------------------------------------------
*/
Route::group(['prefix'=>'teacher', 'middleware'=>['auth','teacher']], function(){

	Route::post('get/section', ['as' => 'get.section', 'uses' => 'MasterController@getSection']);
});

Route::group(['prefix'=>'teacher','namespace'=>'Teacher', 'middleware'=>['auth','teacher']], function(){

	Route::get('dashboard', ['as'=>'teach.dashboard', 'uses'=>'MainController@dashBoard']);

	Route::get('attendance', ['as'=>'teach.attendance', 'uses'=>'MainController@getAttendance']);

	Route::post('/post/attendance', ['as' => 'post.teacher.attendance', 'uses' => 'MainController@postAttendance']);
	Route::get('view/attendance', ['as'=>'view.teach.attendance', 'uses'=>'MainController@viewAttendance']);	

	Route::get('homework', ['as' => 'teach.homework', 'uses' => 'MainController@homework']);
	Route::post('post/homework', ['as' => 'post.teacher.homework', 'uses' => 'MainController@postHomework']);
	Route::get('view/homework', ['as' => 'view.teach.homework', 'uses' => 'MainController@viewHomework']);

	Route::get('leave/approval', ['as' => 'teach.leaveApproval', 'uses'=>'MainController@leaveApproval']);
	Route::post('post/leave', ['as' => 'post.teach.leave', 'uses' => 'MainController@postLeave']);
	Route::get('edit/leave/{id}', ['as' => 'edit.leave', 'uses' => 'MainController@editLeave']);
	Route::post('update/leave', ['as' => 'update.teach.leave', 'uses' => 'MainController@updateLeave']);

	Route::get('resources/school/profile', ['as'=>'teach.schoolProfile', 'uses'=>'MainController@schoolProfile']);

	Route::get('resources/employee', ['as'=>'teach.employee', 'uses'=>'MainController@resourceEmployee']);

	Route::get('resources/time-table', ['as'=>'teach.timeTable', 'uses'=>'MainController@timeTable']);

	Route::get('resources/fee/structure', ['as'=>'teach.feeStructure', 'uses'=>'MainController@feeStructure']);

	Route::get('gallery', ['as'=>'teach.gallery', 'uses'=>'MainController@gallery']);
});

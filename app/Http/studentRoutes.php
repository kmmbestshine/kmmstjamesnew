<?php
	
	Route::group(['prefix'=>'student', 'namespace'=>'Student','middleware'=>'auth'], function(){

		Route::get('dashboard', ['as'=>'student.dashboard', 'uses'=>'StudentController@dashBorad']);

		Route::get('attendance', ['as'=>'student.attendance','uses'=>'StudentController@attendance']);

		Route::get('homework', ['as'=>'student.homework', 'uses'=>'StudentController@homeWork']);

		Route::get('leave/request', ['as'=>'student.leaveRequest', 'uses'=>'StudentController@leaveRequest']);

		Route::post('leave/request/post', ['as'=>'student.leavePost', 'uses'=>'StudentController@leavePost']);

		Route::get('marks', ['as'=>'student.markes', 'uses'=>'StudentController@marks']);

		Route::get('resources/school/profile', ['as'=>'student.schoolProfile', 'uses'=>'StudentController@schoolProfile']);

		Route::get('resources/employee', ['as'=>'student.employee', 'uses'=>'StudentController@resourceEmployee']);

		Route::get('resources/time-table', ['as'=>'student.timeTable', 'uses'=>'StudentController@timeTable']);

		Route::get('resources/fee/structure', ['as'=>'student.feeStructure', 'uses'=>'StudentController@feeStructure']);

		Route::get('gallery', ['as'=>'student.gallery', 'uses'=>'StudentController@gallery']);
	});
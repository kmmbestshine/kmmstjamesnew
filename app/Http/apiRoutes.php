<?php

Route::get('app', ['uses' => 'UserController@shareApp']);

	Route::get('/reg/{type}', function($typeF){
		if(Request::has('reg_id')){
			DB::table('reg')->insert(['reg_id'=> \Request::get('reg_id'), 'type' => $type]);
			return api(['msg' => 'Success!']);
		}
		return api()->notFound();
	});

Route::group(['prefix' => 'api/{type}', 'middleware' => ['api']], function(){
	Route::get('/update', ['uses' => 'UserController@getVersonUpdate']);
	Route::post('free/search', ['uses' => 'UserController@freeSearch']);
	Route::get('get/school/{id}', ['uses' => 'UserController@getSchool']);
	
	Route::get('get/schools/list', ['uses' => 'UserController@getSchoolsList']);

	Route::post('users/login', ['uses' => 'ApiTeacherController@authenticate']);

	Route::get('app/help', ['uses' => 'UserController@appHelp']);

	Route::group(['middleware' => ['jwt.auth']], function(){
                Route::post('/post/geolocation', ['uses' => 'ApiDriverController@postgeolocation']);
                Route::post('/get/drivermaplocation', ['uses' => 'ApiDriverController@parentDrivermaplocation']);
                
		Route::post('/users/logout', ['uses' => 'UserController@logout']);

		Route::post('/users/change/password', ['uses' => 'UserController@changePassPost']);
		
		Route::get('/get/all/details', ['uses' => 'UserController@getAllDetails']);

		Route::get('schools/profile', ['uses' => 'UserController@getSchoolProfile']);
		
		Route::get('/get/user/profile', ['uses' => 'UserController@getUserProfile']);
		
		Route::get('/get/exam/type', ['uses' => 'ApiTeacherController@getExamTypes']);
		Route::get('/get/exam/marks/{id}/{roles}',['uses' => 'ApiTeacherController@getExammarks']);

		Route::post('post/device', ['uses' => 'UserController@postDevice']);

		Route::get('/get/gallery', ['uses' => 'UserController@getGallery']);

		Route::get('get/notification', ['uses' => 'UserController@getNotification']);
		
		Route::get('get/class', ['uses' => 'UserController@getClass']);

		Route::get('get/section/{class}', ['uses'=>'UserController@getSection']);
		
		Route::get('get/fee/structure', ['uses' => 'UserController@getFeeStructure']);
		Route::get('get/classmark/{tid}', ['uses' => 'UserController@getClasstid']);
		//mari29-09
		Route::get('get/section-by-class-id/{teacher_id}/{class_id}', ['uses' => 'UserController@getSectionmark']);
		
		Route::get('get/latest/event', ['uses' => 'UserController@getLatestEvent']);

		Route::post('edit/profile', ['uses' => 'UserController@editProfile']);

		Route::get('get/fee/structure/{id}', ['uses'=>'UserController@getFeeStructureStudent']);

		Route::get('get/leave/requests/parent', ['uses'=>'UserController@getLeaveRequestsParent']);
		Route::get('get/leave/requests/parentcount', ['uses'=>'UserController@getLeaveRequestsParentcount']);
		Route::get('/get/viewexamtimetables/{id}', ['as' => 'user.viewexamTimeTable', 'uses' => 'HomeController@appviewExamTimeTable']);
	});

	Route::group(['middleware' => ['jwt.auth', 'TeacherAPI']], function(){
		
		Route::get('get/subjects', ['uses' => 'ApiTeacherController@getSubjects']);

		// Route::get('get/subjects/{exid}/{section}', ['uses' => 'ApiTeacherController@getSubjects']);

		// changes done by parthiban 19-11-2017(sunday)
		// Route::get('get/subject/{section}', ['uses'=>'ApiTeacherController@getSectionSubject']);

		Route::get('get/subject/{teacher_id}/{section_id}', ['uses'=>'ApiTeacherController@getSectionSubject']);

		Route::get('get/feedback/students/{class_id}/{section_id}', ['uses' => 'ApiTeacherController@getFeedbackStudents']);
		// upto this changed by sunday 
		
		Route::get('get/students', ['uses' => 'ApiTeacherController@getStudents']);

		// changes done by parthiban 19-11-2017(sunday)
		// Route::get('get/students-by-mark/{class_id}/{section_id}', ['uses' => 'ApiTeacherController@getStudentsByMark']);
		Route::post('get/students-by-mark', ['uses' => 'ApiTeacherController@getStudentsByMark']);
		// upto this changed by sunday 
		
		// changes done by parthiban 19-11-2017(sunday)
        // Route::get('get/attendance/students', ['uses' => 'ApiTeacherController@getAttendanceStudents']);
		
		Route::get('get/attendance/students/{class_id}/{section_id}', ['uses' => 'ApiTeacherController@getAttendanceStudents']);
		// upto this changed by sunday 

		Route::get('get/staff', ['uses' => 'ApiTeacherController@getStaff']);
		Route::get('/get/employees/{staffId}', ['uses' => 'ApiTeacherController@getEmployees']);
		
		// Homework
		Route::post('/post/homework', ['uses' => 'ApiTeacherController@postHomeWork']);
		Route::post('/update/homework', ['uses' => 'ApiTeacherController@updateHomeWork']);
		Route::get('get/homework', ['uses' => 'ApiTeacherController@getHomework']);
		
		// Leave Request
		Route::post('/leave/request', ['uses' => 'ApiTeacherController@postLeaveRequest']);
		Route::post('update/leave/request', ['uses' => 'ApiTeacherController@updateLeaveRequest']);
		Route::get('get/leave/requests', ['uses' => 'ApiTeacherController@getLeaveRequests']);
		// code added by parthiban 30-11-2017
		Route::get('get/leave/requestscount', ['uses' => 'ApiTeacherController@leaveRequestcount']);		
		//Feedback
		Route::post('/post/feedback', ['uses' => 'ApiTeacherController@postFeedBack']);
		// adding one route by parthiban 19-11-2017(sunday)
		Route::post('/post/common/feedback', ['uses' => 'ApiTeacherController@postCommonFeedBack']);
		// upto this changed by sunday
		Route::get('get/feedback/{student_id}', ['uses' => 'ApiTeacherController@getFeedBack']);

		//Route::get('get/feedback/{student_id}', ['uses' => 'ApiTeacherController@getFeedBack']);
		
		// attendance
		Route::post('/post/attendance', ['uses' => 'ApiTeacherController@postAttendance']);
		Route::get('/get/attendance/{date}', ['uses' => 'ApiTeacherController@getAttendance']);	
		

		Route::post('/post/time/table', ['uses' => 'ApiTeacherController@postTimeTable']);		
		Route::get('/get/time/table', ['uses' => 'ApiTeacherController@getTimeTables']);			

		Route::post('/post/result', ['uses' => 'ApiTeacherController@postResult']);
		
		Route::post('/post/exam/grade',['uses'=>'ApiTeacherController@getGrade']);
		// add one new route by parthiban 19-11-2017(sunday) 
		// Route::get('get/subjects/{exid}/{section}', ['uses' => 'ApiTeacherController@getSubjects']);
		Route::get('get/subjects/{teacher_id}/{section_id}/{exam_id}', ['uses' => 'ApiTeacherController@getSubjectsForMark']);
		// upto this much changed by parthiban 19-11-2017(sunday) 
		Route::get('/get/results/{examId}', ['uses' => 'ApiTeacherController@getResults']);
		// Route::get('/delete/result/{id}', ['uses' => 'PrincipalController@deleteResult']);		
		// Route::get('/edit/result/{id}', ['uses' => 'PrincipalController@editResult']);		
		// Route::post('/update/result', ['uses' => 'ApiTeacherController@updateResult']);	
		

		Route::get('get/notice', ['uses' => 'ApiTeacherController@getNotice']);

		
	});
    
	Route::group(['prefix'=>'student', 'middleware' => ['jwt.auth', 'StudentAPI']], function(){
		Route::get('/get/homework/{date}', ['uses' => 'ApiStudentController@getHomework']);
		//mari 29-09-2017
		Route::get('/get/homeworkcount/{date}', ['uses' => 'ApiStudentController@getHomeworkCount']);

		Route::get('/get/attendance/date/{date}', ['uses' => 'ApiStudentController@getAttendanceDate']);
		Route::get('/get/attendance/month/{month}', ['uses'=>'ApiStudentController@getAttendanceMonth']);
		Route::get('/get/feedback', ['uses' => 'ApiStudentController@getFeedback']);
		Route::get('/get/timetable', ['uses'=>'StudentController@getTimeTableByStudent']);
		Route::get('/get/results/{examId}/{month}', ['uses' => 'ApiStudentController@getResult']);
		Route::get('dash/home', ['uses' => 'ApiStudentController@dashHome']);
		Route::get('get/notice', ['uses' => 'ApiStudentController@getNotice']);
		Route::get('get/employee/{id}', ['uses' => 'ApiParentController@getEmployee']);
		Route::get('get/knowledge', ['uses'=>'ApiStudentController@getKnowledge']);
		
	});
		Route::post('student/post/updatepass',['uses'=>'ApiTeacherController@updatepass']);
		

   	Route::group(['prefix'=>'parent', 'middleware' => ['jwt.auth', 'ParentAPI']], function(){
		Route::get('/get/students', ['uses' => 'ApiParentController@getStudents']);
		Route::post('post/leave', ['uses' => 'ApiParentController@postLeave']);
		Route::get('/get/homework/{id}/{date}', ['uses' => 'ApiParentController@getHomework']);
		//29-09-2017
        Route::get('/get/homeworkcount/{id}/{date}', ['uses' => 'ApiParentController@getHomeworkCount']);
		Route::get('/get/attendance/{id}/{date}', ['uses' => 'ApiParentController@getAttendance']);
		Route::get('/get/attendance/month/{student}/{month}', ['uses'=>'ApiStudentController@getAttendanceMonthParent']);
		Route::get('/get/feedback/{student_id}/{teacher_id}', ['uses' => 'ApiParentController@getFeedback']);
		Route::get('/get/timetable/{id}', ['uses' => 'ApiParentController@getTimeTable']);
		Route::get('/get/result/{id}/{examid}/{month}', ['uses' => 'ApiParentController@getResult']);
		Route::get('get/notice', ['uses' => 'ApiParentController@getNotice']);
		Route::get('get/employee/{id}', ['uses' => 'ApiParentController@getEmployee']);
		Route::post('post/feedback', ['uses' => 'ApiParentController@postFeedback']);
		Route::get('get/fee/structure/{student_id}', ['uses' => 'ApiParentController@getFeeStructure']);
	});
        
  	// changes done by parthiban(26-09-2017) version 3
	Route::group(['prefix'=>'parent', 'middleware' => ['jwt.auth', 'ParentAPI']], function(){
		Route::get('get/class-id/{parent_id}', ['uses' => 'ApiParentNotificationController@getClassID']);
		
		Route::get('get/notification-count/{class_id}/{parent_id}', ['uses' => 'ApiParentNotificationController@getNotificationCount']);

		Route::get('get/notification/{class_id}', ['uses' => 'ApiParentNotificationController@getNotification']);

		Route::get('get/notification-details/{id}/{parent_id}', ['uses' => 'ApiParentNotificationController@getNotificationDetails']);
	});

	// changes done by parthiban(26-09-2017) version 3
	Route::group(['prefix'=>'student', 'middleware' => ['jwt.auth', 'StudentAPI']], function(){
		Route::get('get/class-id/{student_id}', ['uses' => 'ApiStudentNotificationController@getClassID']);

		Route::get('get/notification-count/{class_id}/{student_id}', ['uses' => 'ApiStudentNotificationController@getNotificationCount']);

		Route::get('get/notification/{class_id}', ['uses' => 'ApiStudentNotificationController@getNotification']);

		Route::get('get/notification-details/{id}/{student_id}', ['uses' => 'ApiStudentNotificationController@getNotificationDetails']);		
	});

	// changes done by parthiban(26-09-2017) version 3
	Route::group(['prefix'=>'teacher', 'middleware' => ['jwt.auth', 'TeacherAPI']], function(){
		Route::get('get/class-id/{teacher_id}', ['uses' => 'ApiTeacherNotificationController@getClassID']);

		Route::get('get/notification-count/{class_id}/{teacher_id}', ['uses' => 'ApiTeacherNotificationController@getNotificationCount']);

		Route::get('get/notification/{class_id}', ['uses' => 'ApiTeacherNotificationController@getNotification']);

		Route::get('get/notification-details/{id}/{teacher_id}', ['uses' => 'ApiTeacherNotificationController@getNotificationDetails']);		
	});


	// changes done by mari
	Route::group(['prefix'=>'parent', 'middleware' => ['jwt.auth', 'ParentAPI']], function(){
		Route::get('get/feedback-count/{parent_id}', ['uses' => 'ApiParentController@getFeedbackCount']);
	});

	Route::group(['prefix'=>'teacher', 'middleware' => ['jwt.auth', 'TeacherAPI']], function(){
		Route::get('get/feedback-count/{teacher_id}', ['uses' => 'ApiTeacherController@getFeedbackCount']);	
		Route::get('get/feedback-count-by-student/{student_id}/{teacher_id}', ['uses' => 'ApiTeacherController@getFeedbackCountByStudent']);	
	});
	
    //Route::group(['middleware' => ['jwt.auth', 'DriverAPI']], function(){
        
    //});
        
});
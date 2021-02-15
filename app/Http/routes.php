<?php

Route::get('/', ['as'=>'/', 'uses'=>'HomeController@home']);
Route::get('about', ['as'=>'about', 'uses'=>'FrontController@about']);
Route::get('contact-us', ['as'=>'contact', 'uses'=>'FrontController@contact']);
Route::get('galler', ['as'=>'gallery', 'uses'=>'FrontController@gallery']);
Route::get('facility', ['as'=>'facility', 'uses'=>'FrontController@facility']);
Route::get('registration', ['as'=>'bcpRegistration', 'uses'=>'FrontController@bcpRegistration']);
Route::post('post/registration', ['as'=>'postRegistration', 'uses'=>'FrontController@postRegistration']);
Route::get('treport', ['as' => 'treport', 'uses' => 'HomeController@treport']);
/*** @ updated 24-10-2017 by priya @   ***/
Route::post('post/user/detail', ['as' => 'postUserEnquiryDetail', 'uses' => 'FrontController@getUserDetail']);
Route::post('post/subcribe/newsletter', ['as' => 'postNewsLetter', 'uses' => 'FrontController@getSubcribeNewsletter']);
/*****/
Route::get('date/convert', ['uses'=>'FrontController@dateConvert']);

Route::get('joboppourtunityschool', ['as'=>'joboppournityschool', 'uses' => 'JoboppourtunityController@joboppournityschool']);
Route::post('joboppourtunities', ['as'=>'joboppournity', 'uses' => 'JoboppourtunityController@joboppournity']);
Route::post('post/biodata', ['as'=>'post.biodata', 'uses' => 'JoboppourtunityController@postBiodata']);
/*
|----------------------------------------------------
|-------------Front Principal------------------------
|----------------------------------------------------
*/
Route::get('logout', ['as' => 'logout','middleware'=>'auth', 'uses' => 'AdminController@logOut']);


Route::get('manage', ['as'=>'manage', 'uses' => 'HomeController@manage']);
Route::get('get/teachers/recruitment', ['as'=>'teachersRecruitment', 'uses'=>'JoboppourtunityController@teachersRecruitment']);

Route::get('teachers/question', ['as'=>'teachersQuestion', 'uses'=>'JoboppourtunityController@teachersQuestion']);
Route::get('take/teacherques', ['as'=>'teachers_onlinetest', 'uses'=>'JoboppourtunityController@teachersOnlinetest']);
Route::get('view/teacherques/{type}', ['as'=>'teachers_view_question', 'uses'=>'JoboppourtunityController@teachersQViewOnlinetest']);
Route::post('post/teacher/qns', ['as'=>'post.teachers.test', 'uses'=>'JoboppourtunityController@postteachersOnlinetest']);
Route::get('get/democlass/chklist/{id}', ['as'=>'democlass_checklist', 'uses'=>'JoboppourtunityController@democlass_checklist']);
Route::get('get/view/staffprofile/{id}', ['as'=>'view.staffprofile', 'uses'=>'JoboppourtunityController@viewstaff_profile']);
Route::get('get/view/question', ['as'=>'viewquestion', 'uses'=>'JoboppourtunityController@viewquestion']);
Route::post('view/questionss', ['as'=>'view_questionss', 'uses'=>'JoboppourtunityController@viewquestionlist']);
Route::get('get/delete/question/{id}/{type}', ['as'=>'delete.questio', 'uses'=>'JoboppourtunityController@deleteQuestions']);
Route::get('view-result', 'JoboppourtunityController@viewResult')->name('view-result');
Route::get('get/interview/result/{id}', ['as'=>'interview.test.result', 'uses'=>'JoboppourtunityController@interview_test_result']);
Route::get('get/waiting/job/{id}', ['as'=>'waiting.for.job', 'uses'=>'JoboppourtunityController@waitingJob']);
Route::get('get/rejected/job/{id}', ['as'=>'rejected.for.job', 'uses'=>'JoboppourtunityController@rejectedJob']);
Route::get('get/democlassview/chklist/{id}', ['as'=>'democlass_checklistview', 'uses'=>'JoboppourtunityController@democlass_checklistview']);
Route::get('issue/appoint/staff/{id}', ['as'=>'staff.appoint.issue', 'uses'=>'JoboppourtunityController@staffappointmentissuelist']);

Route::get('questions/{teaching_type}', 'JoboppourtunityController@index')->name('questions');
Route::post('post/teacherques', ['as'=>'postTeachersQuestion', 'uses'=>'JoboppourtunityController@postTeachersQuestion']);
Route::post('post/democlass/checklist/{id}', ['as'=>'user.democlass.checklist', 'uses'=>'JoboppourtunityController@postDemoclasschecklist']);
Route::get('get/personal/interview/{id}', ['as'=>'personal_interview', 'uses'=>'JoboppourtunityController@personal_interview']);
Route::get('get/corres/sugession/{id}', ['as'=>'corres.sugession', 'uses'=>'JoboppourtunityController@getsugession']);
Route::post('post/personal/interview/{id}', ['as'=>'user.personal.interview', 'uses'=>'JoboppourtunityController@postpersonalinterview']);
Route::get('get/selected/staff/{id}', ['as'=>'selected.for.job', 'uses'=>'JoboppourtunityController@getselectedstaff']);
Route::post('post/selected/staff', ['as'=>'postSelectedStaff', 'uses'=>'JoboppourtunityController@postSelectedStaff']);
Route::get('get/approved/list', ['as'=>'apmt.approved.list', 'uses'=>'JoboppourtunityController@getapprovedlist']);
Route::get('get/document/upload/{id}', ['as'=>'staff.document.upload', 'uses'=>'JoboppourtunityController@staffdocumentUpload']);
Route::post('post/document/upload/{id}', ['as'=>'post.upload.document', 'uses'=>'JoboppourtunityController@postdocumentUpload']);
Route::get('delete/corres/staff/{id}', ['as'=>'corres.staff.delete', 'uses'=>'JoboppourtunityController@staffRecruitmentdelete']);
Route::post('login/request', ['as' => 'loginRequest','uses'=>'AdminController@loginRequest']);
Route::get('get/personal/interviewview/{id}', ['as'=>'personal_interviewview', 'uses'=>'JoboppourtunityController@personal_interviewview']);
Route::get('staff/acknow/download/{id}', ['as'=>'staff.acknow.download', 'uses'=>'JoboppourtunityController@acknowledgementDownload']);
Route::get('staff/appoint/download/{id}', ['as'=>'staff.appoint.download', 'uses'=>'JoboppourtunityController@appointmentDownload']);
Route::get('staff/agreement/download/{id}', ['as'=>'staff.agreement.download', 'uses'=>'JoboppourtunityController@agreementDownload']);
Route::get('create/job/school', ['as'=>'create.job.school', 'uses'=>'JoboppourtunityController@createSchool']);
Route::post('post/job/school', ['as'=>'post.job.school', 'uses'=>'JoboppourtunityController@postSchool']);
// changes done by parthiban 03-10-2017

Route::get('accounts/index', ['as' => 'accounts.index', 'uses' => 'AccountController@accountsindex']);
Route::get('acc/add/category1', ['as' => 'account.add.categorys', 'uses' => 'AccountController@category']);
Route::post('accounts/create-sector1', ['as' => 'acc.add.storeSector', 'uses' => 'AccountController@storeSector']);
Route::get('accounts/incomelist', ['as' => 'acc.add.income', 'uses' => 'AccountController@income']);
Route::post('acc/storeIncome', ['as' => 'acc.add.storeIncome', 'uses' => 'AccountController@storeIncome']);
Route::get('acc/list/income', ['as' => 'acc.list.income', 'uses' => 'AccountController@incomeList']);
Route::post('acc/post/incomelist1', ['as' => 'acc.post.incomelist', 'uses' => 'AccountController@postincomeList']);
Route::get('accounts/add/expense', ['as' => 'acc.add.expense', 'uses' => 'AccountController@expense']);
Route::post('accounts/post/expense', ['as' => 'acc.post.expense', 'uses' => 'AccountController@storeexpense']);
Route::get('acc/list/expense', ['as' => 'acc.list.expense', 'uses' => 'AccountController@expenseList']);
Route::post('acc/post/expenselist', ['as' => 'acc.post.expenselist', 'uses' => 'AccountController@postexpenseList']);
Route::get('acc/list/report1', ['as' => 'acc.report.consolidate', 'uses' => 'AccountController@accountsconsolidate']);
Route::post('acc/report/consolidate1', ['as' => 'acc.post.reportlist', 'uses' => 'AccountController@accconsolidatereport']);
Route::get('accounts/delete-sector/{id}', ['as' => 'acc.add.deleteSector', 'uses' => 'AccountController@deleteSector']);
Route::delete('accounts/delete-income1/{id}', ['as' => 'acc.add.deleteincome', 'uses' => 'AccountController@deleteincome']);
Route::get('accounts/deleteexpense/{id}', ['as' => 'acc.add.deleteexpense', 'uses' => 'AccountController@deleteexpense']);
Route::get('accounts/create/bankacc', ['as' => 'acc.create.bankacc', 'uses' => 'AccountController@createBankaccount']);
Route::post('accounts/post/bankacc', ['as' => 'acc.post.bankacc', 'uses' => 'AccountController@postBankaccount']);

Route::get('purchase/add/order', ['as' => 'purchase.add.order', 'uses' => 'PurchaseorderController@purchaseOrder']);
Route::post('purchase/order/create', ['as' => 'purchase.order.create', 'uses' => 'PurchaseorderController@createPurchaseorder']);
Route::get('purchase/add/ventor', ['as' => 'ventor.add.purchase', 'uses' => 'PurchaseorderController@addventor']);
Route::post('store/add/ventor', ['as' => 'ventor.add.store', 'uses' => 'PurchaseorderController@storeVentor']);
Route::get('purchase/goods/edit', ['as' => 'purchase.goods.edit', 'uses' => 'PurchaseorderController@editPurchaseorder']);
Route::get('purchase/update-goods/{id}', ['as' => 'purchase.goods.update', 'uses' => 'PurchaseorderController@updatePurchaseorder']);
Route::post('purchase/order/update', ['as' => 'purchase.order.update', 'uses' => 'PurchaseorderController@postPurchaseorderupdate']);
Route::get('purchase/report/list', ['as' => 'purchase.reports.list', 'uses' => 'PurchaseorderController@purchaseReport']);
Route::get('/purchase-update/{id}', ['as' => 'purchase.update', 'uses' => 'PurchaseorderController@update']);
Route::get('print/order/purchase/{id}', ['as'=>'print.order.purchase', 'uses'=>'PurchaseorderController@printpurchaseorder']);
Route::get('purchase/delete-goods/{id}', ['as' => 'purchase.delete', 'uses' => 'PurchaseorderController@purchasedelete']);
Route::get('purchase/view/ventor', ['as' => 'ventor.view.purchase', 'uses' => 'PurchaseorderController@viewventor']);
Route::get('purchase/delete-vendor/{id}', ['as' => 'purchase.delete.vendor', 'uses' => 'PurchaseorderController@purchasevendordelete']);

//Route::get('/users/dashboard/send/birthday-notification','NotificationController@sendBirthdayNotification');

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'ManyRoles:user_role|school|teacher', 'userRoles']], function(){

    Route::get('dashboard', [
        'as'=>'user.dashboard',
        'uses' => 'HomeController@dashboard'
    ]);

    /** @ Send Birthday wish @ **/
    Route::post('dashboard/send/birthday-notification',['as' => 'birthday.notification', 'uses' => 'NotificationController@sendBirthdayNotification']);



    Route::get('master', ['as'=>'masterView', 'uses'=>'MasterController@masterView']);

    Route::get('change/password', ['as'=>'changePass', 'uses' => 'MasterController@changePass']);

    Route::post('post/password/{id}', ['as' => 'postPass', 'uses' => 'MasterController@postPass']);

    // Master Forms in MasterController
    Route::get('master/session', ['as' => 'master.session', 'uses' => 'MasterController@masterSession']);
    Route::post('post/session', ['as' => 'post.session', 'uses' => 'MasterController@postSession']);
    Route::get('delete/session/{id}', ['as' => 'delete.session', 'uses'=>'MasterController@deleteSession']);
    Route::get('edit/session/{id}', ['as' => 'edit.session', 'uses'=>'MasterController@editSession']);
    Route::post('update/session', ['as' => 'update.session', 'uses' => 'MasterController@updateSession']);
    Route::get('operate/session/{id}', ['as' => 'operate.session', 'uses' => 'MasterController@operateSession']);

    Route::get('master/class', ['as' => 'master.class', 'uses' => 'MasterController@masterClass']);
    Route::get('get/classmark/{tid}', ['uses' => 'UserController@getClasstid']);
    Route::post('post/class', ['as' => 'post.class', 'uses' => 'MasterController@postClass']);
    Route::get('delete/class/{id}', ['as' => 'delete.class', 'uses' => 'MasterController@deleteClass']);
    Route::get('edit/class/{id}', ['as' => 'edit.class', 'uses' => 'MasterController@editClass']);
    Route::post('update/class', ['as' => 'update.class', 'uses' => 'MasterController@updateClass']);

    Route::get('master/section', ['as' => 'master.section', 'uses' => 'MasterController@masterSection']);
    Route::post('/post/section', ['as' => 'post.section', 'uses'=>'MasterController@postSection']);
    Route::get('/delete/section/{id}', ['as' => 'delete.section', 'uses'=>'MasterController@deleteSection']);
    Route::get('/edit/section/{id}', ['as' => 'edit.section', 'uses'=>'MasterController@editSection']);
    Route::post('/update/section', ['as' => 'update.section', 'uses'=>'MasterController@updateSection']);

    Route::post('get/section/ajax', ['as' => 'get.section', 'uses' => 'MasterController@getSection']);
    Route::get('master/subject', ['as' => 'master.subject', 'uses' => 'MasterController@masterSubject']);
    Route::post('/post/subject', ['as' => 'post.subject', 'uses'=>'MasterController@postSubject']);
    Route::get('/delete/subject/{id}', ['as' => 'delete.mastersubject', 'uses'=>'MasterController@deleteSubject']);
    Route::get('/edit/subject/{id}', ['as' => 'edit.subject', 'uses'=>'MasterController@editSubject']);
    Route::post('/update/subject', ['as' => 'update.subject', 'uses'=>'MasterController@updateSubject']);

    Route::get('master/exam/type', ['as' => 'master.exam', 'uses' => 'MasterController@masterExam']);
    Route::post('/post/exam/type', ['as' => 'post.exam', 'uses'=>'MasterController@postExamType']);
    Route::get('/get/exam/grade/{id}',['as'=>'exam.grade','uses'=>'MasterController@createGrade']);
    Route::get('/get/exam/gradede/{id}',['as'=>'get.exam.grade','uses'=>'MasterController@deleteresultgrade']);
    Route::post('/post/exam/gradesave',['as'=>'post.exam.gradesave','uses'=>'MasterController@gradesystem']);

    Route::get('/get/exam/grade/{id}',['as'=>'exam.grade','uses'=>'MasterController@createGrade']);
    Route::get('/get/exam/gradede/{id}',['as'=>'get.exam.grade','uses'=>'MasterController@deleteresultgrade']);
    Route::post('/post/exam/gradesave',['as'=>'post.exam.gradesave','uses'=>'MasterController@gradesystem']);
    Route::get('/get/exam/fasagrade/{id}',['as'=>'exam.fasagrade','uses'=>'MasterController@createfasaGrade']);
    Route::post('/post/exam/fasagradesave',['as'=>'post.exam.fasagradesave','uses'=>'MasterController@fasagradesystem']);
    Route::post('post/student/hrsecmark', ['as' => 'postStudenthrsecResult', 'uses' => 'LibraryController@postStudentsExamhrsecResult']);
    Route::post('post/student/fasamark', ['as' => 'postStudentfasaResult', 'uses' => 'LibraryController@postStudentsExamfasaResult']);
    
    Route::get('/delete/exam/type/{id}', ['as' => 'delete.exam', 'uses'=>'MasterController@deleteExamType']);
    Route::get('/edit/exam/type/{id}', ['as' => 'edit.exam', 'uses'=>'MasterController@editExamType']);
    Route::post('/update/exam/type', ['as' => 'update.exam', 'uses'=>'MasterController@updateExamType']);

    Route::get('master/staff', ['as' => 'master.staff', 'uses' => 'MasterController@masterStaff']);
    Route::post('/post/staff', ['as' => 'post.staff', 'uses'=>'MasterController@postStaffType']);
    Route::get('/delete/staff/{id}', ['as' => 'delete.staff', 'uses'=>'MasterController@deleteStaffType']);
    Route::get('/edit/staff/{id}', ['as' => 'edit.staff', 'uses'=>'MasterController@editStaffType']);
    Route::post('/update/staff', ['as' => 'update.staff', 'uses'=>'MasterController@updateStaffType']);

    Route::get('master/events', ['as' => 'master.events', 'uses' => 'MasterController@masterEvents']);
    Route::post('/post/events', ['as' => 'post.events', 'uses'=>'MasterController@postEvents']);
    Route::get('/delete/events/{id}', ['as' => 'delete.events', 'uses'=>'MasterController@deleteEvents']);
    Route::get('/edit/events/{id}', ['as' => 'edit.events', 'uses'=>'MasterController@editEvents']);
    Route::post('/update/events', ['as' => 'update.events', 'uses'=>'MasterController@updateEvents']);

    Route::get('master/caste', ['as' => 'master.caste', 'uses' => 'MasterController@masterCaste']);
    Route::post('/post/caste', ['as' => 'post.caste', 'uses'=>'MasterController@postCaste']);
    Route::get('/delete/caste/{id}', ['as' => 'delete.caste', 'uses'=>'MasterController@deleteCaste']);
    Route::get('/edit/caste/{id}', ['as' => 'edit.caste', 'uses'=>'MasterController@editCaste']);
    Route::post('/update/caste', ['as' => 'update.caste', 'uses'=>'MasterController@updateCaste']);

    Route::get('master/grade', ['as' => 'master.grade', 'uses' => 'MasterController@masterGrade']);
    Route::post('/post/grade', ['as' => 'post.grade', 'uses'=>'MasterController@postGrade']);
    Route::get('/delete/grade/{id}', ['as' => 'delete.grade', 'uses'=>'MasterController@deleteGrade']);
    Route::get('/edit/grade/{id}', ['as' => 'edit.grade', 'uses'=>'MasterController@editGrade']);
    Route::post('/update/grade', ['as' => 'update.grade', 'uses'=>'MasterController@updateGrade']);

    Route::get('master/religion', ['as' => 'master.religion', 'uses' => 'MasterController@masterReligion']);
    Route::post('/post/religion', ['as' => 'post.religion', 'uses'=>'MasterController@postReligion']);
    Route::get('/delete/religion/{id}', ['as' => 'delete.religion', 'uses'=>'MasterController@deleteReligion']);
    Route::get('/edit/religion/{id}', ['as' => 'edit.religion', 'uses'=>'MasterController@editReligion']);
    Route::post('/update/religion', [ 'as' => 'update.religion','uses'=>'MasterController@updateReligion']);

    Route::get('master/bus', ['as' => 'master.bus', 'uses' => 'MasterController@masterBus']);
    Route::post('/post/bus', ['as' => 'post.bus', 'uses'=>'MasterController@postBus']);
    Route::get('/delete/bus/{id}', ['as' => 'delete.bus', 'uses'=>'MasterController@deleteBus']);
    Route::get('/edit/bus/{id}', ['as' => 'edit.bus', 'uses'=>'MasterController@editBus']);
    Route::post('/update/bus', ['as' => 'update.bus', 'uses'=>'MasterController@updateBus']);

    Route::get('master/stop', ['as' => 'master.stop', 'uses' => 'MasterController@masterBusStop']);
    Route::post('/post/stop', ['as' => 'post.stop', 'uses'=>'MasterController@postBusStop']);
    Route::get('/delete/stop/{id}', ['as' => 'delete.stop', 'uses'=>'MasterController@deleteBusStop']);
    Route::get('/edit/stop/{id}', ['as' => 'edit.stop', 'uses'=>'MasterController@editBusStop']);
    Route::post('/update/stop', ['as' => 'update.stop', 'uses'=>'MasterController@updateBusStop']);

    Route::get('master/driver', ['as' => 'master.driver', 'uses' => 'MasterController@masterDriver']);
    Route::post('/post/driver', ['as' => 'post.driver', 'uses'=>'MasterController@postDriver']);
    Route::get('/delete/driver/{id}', ['as' => 'delete.driver', 'uses'=>'MasterController@deleteDriver']);
    Route::get('/edit/driver/{id}', ['as' => 'edit.driver', 'uses'=>'MasterController@editDriver']);
    Route::post('/update/driver', ['as' => 'update.driver', 'uses'=>'MasterController@updateDriver']);

    Route::get('master/holiday', ['as' => 'master.holiday', 'uses' => 'MasterController@masterHoliday']);
    Route::post('/post/holiday', ['as' => 'post.holiday', 'uses'=>'MasterController@postHoliday']);
    Route::get('/delete/holiday/{id}', ['as' => 'delete.holiday', 'uses'=>'MasterController@deleteHoliday']);
    Route::get('/edit/holiday/{id}', ['as' => 'edit.holiday', 'uses'=>'MasterController@editHoliday']);
    Route::post('/update/holiday', ['as' => 'update.holiday', 'uses'=>'MasterController@updateHoliday']);

    Route::get('master/salary', ['as' => 'master.salary', 'uses' => 'MasterController@masterSalary']);
    Route::post('/post/salary', ['as' => 'post.salary', 'uses'=>'MasterController@postSalary']);
    Route::get('/delete/salary/{id}', ['as' => 'delete.salary', 'uses'=>'MasterController@deleteSalary']);
    Route::get('/edit/salary/{id}', ['as' => 'edit.salary', 'uses'=>'MasterController@editSalary']);
    Route::post('/update/salary', ['as' => 'update.salary', 'uses'=>'MasterController@updateSalary']);

    Route::get('class/deposit', ['as' => 'class.deposit', 'uses' => 'MasterController@masterDeposit']);
    Route::post('/post/deposit', ['as' => 'post.deposit', 'uses'=>'MasterController@postDeposit']);
    Route::get('/delete/deposit/{id}', ['as' => 'delete.deposit', 'uses'=>'MasterController@deleteDeposit']);
    Route::get('/edit/deposit/{id}', ['as' => 'edit.deposit', 'uses'=>'MasterController@editDeposit']);
    Route::post('/update/deposit', ['as' => 'update.deposit', 'uses'=>'MasterController@updateDeposit']);

    Route::get('master/notification', ['as' => 'master.notification', 'uses' => 'MasterController@masterNotification']);
    Route::post('/post/notification', ['as' => 'post.notification', 'uses' => 'MasterController@postNotification']);
    Route::get('/delete/notification/{id}', ['as' => 'delete.notification', 'uses' => 'MasterController@deleteNotification']);
    Route::get('/edit/notification/{id}', ['as' => 'edit.notification', 'uses' => 'MasterController@editNotification']);
    Route::post('/update/notification', ['as' => 'update.notification', 'uses' => 'MasterController@updateNotification']);

    Route::get('insert/student', ['as' => 'master.student', 'uses' => 'StudentController@masterStudent']);
    Route::post('/post/student', ['as' => 'post.student', 'uses' => 'StudentController@postStudent']);
    Route::get('/get/students', ['as' => 'get.students', 'uses' => 'StudentController@getStudents']);
    Route::get('view/student/{id}', ['as' => 'view.student', 'uses' => 'StudentController@viewStudent']);
    Route::get('del/sion/stufees/delete',  ['as' => 'stufee.sion.structure.delete', 'uses' => 'StudentController@deletSionstudentFeeStructure']);
    Route::get('/edit/student/{id}', ['as' => 'edit.student', 'uses' => 'StudentController@editStudent']);
    Route::get('/delete/student/{id}', ['as' => 'delete.student', 'uses'=>'PrincipalController@deleteStudent']);
    Route::post('/update/student', ['as' => 'update.student', 'uses'=>'StudentController@updateStudent']);
    Route::post('/import/student', ['as' => 'import.student', 'uses' => 'PrincipalController@importStudent']);
    Route::post('search/student', ['as' => 'search.student', 'uses' => 'StudentController@searchStudent']);
    Route::get('search/studentlist', ['as' => 'search.studentlist', 'uses' => 'StudentController@searchstudentlist']);

    /************construction *********/
     Route::get('manager/constructindex', ['as' => 'user.construct.index', 'uses' => 'LibraryController@constructindex']);
     Route::get('add/construction', ['as' => 'user.construction', 'uses' => 'LibraryController@addBuilding']);
    Route::post('details/construction', ['as' => 'post.buildingdet', 'uses' => 'LibraryController@addBuildingDetails']);
    Route::get('get/building',['as'=>'get.building', 'uses'=>'LibraryController@getBuilding']);
    Route::get('download/build/image/{id}',['as'=>'build.download.image', 'uses'=>'LibraryController@downloadBuilding']);
    Route::get('download/build/pdf/{id}',['as'=>'build.download.pdf', 'uses'=>'LibraryController@pdfdownloadBuilding']);
    Route::get('delete/building',['as'=>'building.construction.delete', 'uses'=>'LibraryController@deleteBuilding']);
    Route::get('add/constructwork', ['as' => 'user.construct.work', 'uses' => 'LibraryController@addBuildingwork']);
    Route::post('details/construct/work', ['as' => 'post.buildingdet.work', 'uses' => 'LibraryController@addBuildingworkDetails']);
    Route::get('get/building/types',['as'=>'get.buildingwork.type', 'uses'=>'LibraryController@getBuildingworktype']);
    Route::post('view/building/types',['as'=>'get.buildwork.det', 'uses'=>'LibraryController@viewBuildingworktype']);
    Route::get('delete/build/types',['as'=>'buildworks.construction.delete', 'uses'=>'LibraryController@deleteBuildingworktype']);
    Route::get('add/construct/contractor', ['as' => 'user.construct.contractor', 'uses' => 'LibraryController@addworkContractor']);
    Route::post('post/construct/contractor', ['as' => 'post.construct.contractor', 'uses' => 'LibraryController@addworkContractordetails']);
    Route::post('post/construct/labour', ['as' => 'post.buildingdet.labour', 'uses' => 'LibraryController@addBuildinglabourDetails']);
    Route::get('get/building/labour',['as'=>'get.labour', 'uses'=>'LibraryController@getLabour']);
    Route::get('cities/get_by_buildid23', ['as' => 'contractorname.get_by_build_id', 'uses' => 'LibraryController@get_by_buildid']);
    Route::get('cities/get_by_contractor_id', ['as' => 'labour.get_by_contractor_id', 'uses' => 'LibraryController@get_by_contractor_id']);
    Route::get('delete/get_by_labourid', ['as' => 'labour.construction.delete', 'uses' => 'LibraryController@deletelabourname']);
    Route::get('add/construct/payment', ['as' => 'user.construct.payment', 'uses' => 'LibraryController@addlabourPayment']);
    Route::post('step2/construct/payment', ['as' => 'post.construct.paymentstr', 'uses' => 'LibraryController@step2labourPayment']);
    Route::post('post/construct/payment', ['as' => 'create.labour.payment', 'uses' => 'LibraryController@postLabourpayment']);
    Route::post('checkbox/construct/payment', ['as' => 'user.checkboxamt.construction', 'uses' => 'LibraryController@selectelabourPayment']);
    Route::get('get/daily/wages', ['as' => 'get.dailywages', 'uses' => 'LibraryController@getdailywages']);
    Route::get('get/contractor/wages', ['as' => 'view.labour.payment', 'uses' => 'LibraryController@getdailywagesdetails']);
    Route::get('contractor/wages/details', ['as' => 'view.labour.payment.details', 'uses' => 'LibraryController@viewPaymentcontractorwise']);
    Route::get('checkbox/construct/salary', ['as' => 'user.construct.salary', 'uses' => 'LibraryController@constructionSalary']);
    Route::post('pay/labour/search', ['as' => 'pay.construction.search', 'uses' => 'LibraryController@selectlabour_paymentsearch']);
    Route::post('pay/labour/paylist', ['as' => 'user.construction.paylist', 'uses' => 'LibraryController@selectlabour_paymentlist']);
    Route::post('paid/labour/paidamt', ['as' => 'user.labour.paidamount', 'uses' => 'LibraryController@selectlabour_paymentreceipt']);
    Route::get('delete/get_by_labourid/payment', ['as' => 'labourpayment.construction.delete', 'uses' => 'LibraryController@deletelabourpayment']);

    Route::get('add/supplier/post', ['as'=>'add.supplier', 'uses'=>'LibraryController@addSupplier']);
    Route::post('post/supplier/post', ['as'=>'post.supplier', 'uses'=>'LibraryController@postSupplier']);
    Route::get('add/purchase/post', ['as'=>'add.purchase', 'uses'=>'LibraryController@addPurchase']);
    Route::post('create/purchase/post', ['as'=>'post.purchase.create', 'uses'=>'LibraryController@postPurchase']);
    Route::get('create/material/issue', ['as'=>'issue.material.create', 'uses'=>'LibraryController@addIssuematerial']);
    Route::post('add/material/issue', ['as'=>'issue.material.post', 'uses'=>'LibraryController@addIssuematerialdetails']);
    Route::post('add/material/issueone', ['as'=>'issue.material.postone', 'uses'=>'LibraryController@postIssuematerialdetails']);

        Route::get('add/docu/office', ['as'=>'user.office.index', 'uses'=>'LibraryController@addofficedocu']);
        Route::post('add/document/office', ['as'=>'post.office.document', 'uses'=>'LibraryController@postofficedocu']);
       Route::post('get/document/office', ['as'=>'get.office.document', 'uses'=>'LibraryController@getofficedocu']);
       Route::get('download/office/image/{id}',['as'=>'office.download.image', 'uses'=>'LibraryController@downloadimagedocument']);
       Route::get('download/office/pdf/{id}',['as'=>'office.download.pdf', 'uses'=>'LibraryController@pdfdownloadoffice']);
       Route::get('delete/office/school',['as'=>'document.office.delete', 'uses'=>'LibraryController@deleteofficedocument']);
       Route::get('get/office/schoolname',['as'=>'get.office.schoolname', 'uses'=>'LibraryController@getschoolnameforoffice']);

        Route::post('getcollect/student/section/ajax', ['as' => 'get.studentcollectfee.section', 'uses' => 'LibraryController@getStudentcollectSection']);
   Route::post('get/collect/student/ajax',  ['as' => 'get.fee.student.paycollect', 'uses' => 'LibraryController@singStu_collectStudent']);

    /**  @ updated 2-6-2018 by priya @ **/
    Route::get('upgrade/student', ['as' => 'upgrade_student', 'uses' => 'StudentController@masterUpgradeStudent']);
    Route::post('get/session/class/ajax', ['as' => 'get_student_upgrade_class', 'uses' => 'StudentController@get_student_upgrade_class']);
    Route::post('get/upgrade/section/ajax', ['as' => 'get.upgrade.section', 'uses' => 'StudentController@getUpgradeSection']);
    Route::post('add/upgrade/student', ['as' => 'upgrade.new.student', 'uses' => 'StudentController@postUpgradeStudent']);
    /******** end ******/

    Route::get('delete/student/{id}', ['as' => 'delete.student', 'uses' => 'StudentController@deleteStudent']);

    Route::post('post/stop/routes', ['uses' => 'post.stop.routes', 'uses' => 'StudentController@postStopRoutes']);

    Route::get('insert/employee', ['as' => 'insert.employee', 'uses' => 'TeacherController@insertEmployee']);
    Route::post('/upload/image', ['uses'=>'PrincipalController@uploadImage']);
    Route::post('/post/employee', ['as' => 'post.employee', 'uses' => 'TeacherController@postEmployee']);
    Route::get('/get/employee', ['as' => 'get.employee', 'uses' => 'TeacherController@getEmployee']);
    Route::get('/edit/employee/{id}', ['as' => 'edit.employee', 'uses' => 'TeacherController@editEmployee']);
    Route::get('/delete/employee/{id}', ['as' => 'delete.employee', 'uses'=>'TeacherController@deleteEmployee']);
    Route::post('/update/employee', ['as' => 'update.employee', 'uses' => 'TeacherController@updateEmployee']);
    Route::get('export/employee', ['as' => 'export.employee', 'uses' => 'TeacherController@exportEmployee']);
    Route::post('/import/employee', ['uses' => 'PrincipalController@importEmployee']);

    /** @ Updated 6-6-2018 by priya to  @ **/
    Route::get('delete/all/employee', ['as' => 'delete_all_employee', 'uses' => 'TeacherController@deleteAllEmployee']);
    Route::get('export/employee/{session_id}', ['as' => 'export.employee', 'uses' => 'TeacherController@exportEmployee']);

    /*****************************************************************************
     *                                  TIME TABLE
     *****************************************************************************/

    Route::post('/post/excel/time/table', ['as'=>'postTimeTableExcelSheet', 'uses'=>'HomeController@postTimeTableExcelSheet']);
    //updated 13-11-2017
    Route::get('edit/time-table/detail/{id}', ['as' => 'editTimetableDetail', 'uses' => 'HomeController@editTimetableDetail']);
    Route::post('update/timetable/detail', ['as' => 'update_timetable_detail', 'uses' => 'HomeController@updateTimetableDetail']);
 /** @ Updated 14-4-2018 by priya to  @ **/
    Route::get('delete/all/time/table', ['as' => 'delete_all_timetable', 'uses' => 'HomeController@deleteAllTimeTable']);




    /*
    |-----------------------------------------------------------
    | Employee Controller
    |-----------------------------------------------------------
    */


    /*****************************************************************************
     *                              EMPLOYEE ATTENDANCE
     *****************************************************************************/

   //
    Route::get('get/teacher/attendance', ['as'=>'getTeacherAttendance', 'uses'=>'EmployeeController@getTeacherAttendance']);
    Route::post('get/staff/type/details', ['as'=>'getStaffTypeDetails', 'uses'=>'EmployeeController@getStaffTypeDetails']);
    Route::post('get/staff/attendance/detail', ['as'=>'getStaffAttendanceDetails', 'uses'=>'EmployeeController@checkStaffAttendanceDetails']);
    Route::post('post/excel/teacher/attendance', ['as'=>'postExcelTeacherAttendance', 'uses'=>'EmployeeController@postExcelTeacherAttendance']);
    Route::post('post/all/teacher/attendance', ['as'=>'postTeacherAttendance', 'uses'=>'EmployeeController@postTeacherAttendance']);
    Route::get('/view/teacher/attendance/{attendance}', ['as'=>'viewTeacherAttendance', 'uses'=>'EmployeeController@viewTeacherAttendanceId']);
    Route::get('edit/teacher/attendance/{id}', ['as'=>'editTeacherAttendance', 'uses'=>'EmployeeController@editTeacherAttendance']);
    Route::post('update/teacher/attendance', ['as'=>'updateTeacherAttendance', 'uses'=>'EmployeeController@updateTeacherAttendance']);
    Route::get('delete/teacher/attendance/{id}', ['as'=>'deleteTeacherAttendance', 'uses'=>'EmployeeController@deleteTeacherAttendance']);
    Route::get('download/attendance/report', ['as'=>'downloadEmployeeReport', 'uses'=>'EmployeeController@downloadEmployeeReport']);
    Route::get('view/employee/report', ['as'=>'viewMonthlyReport', 'uses'=>'EmployeeController@viewMonthlyReport']);

     Route::get('view/all/teacher/attendance', ['as'=>'viewTeacherAttendanceReport', 'uses'=>'EmployeeController@viewTeacherAttendanceReport']);
    // Route::get('get/all/staff/attendance/report', ['as'=>'getStaffBasedAttendanceReport', 'uses'=>'EmployeeController@getStaffBasedAttendanceReport']);
    // Route::post('post/employee/month/report', ['as'=>'postToviewMonthlyRecord', 'uses'=>'EmployeeController@postToviewMonthlyRecord']);
   // Route::get('session/teacher/attendance', ['as'=>'getSessionBasedAttendance', 'uses'=>'EmployeeController@getSessionBasedAttendance']);




/*
|-----------------------------------------------------------
| Payroll Controller
|-----------------------------------------------------------
*/


    /*****************************************************************************
     *                              PAYROLL MODULE
     *****************************************************************************/
    Route::get('get/students/multiblefee',  ['as' => 'multible_studentfee', 'uses' => 'HomeController@multiblestudent_paymentstr']);
    Route::get('view/payroll/index', ['as'=>'viewPayrollIndex', 'uses'=>'PayrollController@viewPayrollIndex']);
    Route::get('get/single/employee/payroll/{id}/{year}/{month}', ['as'=>'viewSingleEmployeePayroll', 'uses'=>'PayrollController@get_single_employee_payroll']);
    Route::get('edit/single/employee/payroll/{id}/{year}/{month}', ['as'=>'editSingleEmployeePayroll', 'uses'=>'PayrollController@edit_single_employee_payroll']);
    Route::get('send/single/employee/payroll/{id}/{year}/{month}', ['as'=>'send_payroll', 'uses'=>'PayrollController@send_payroll_report']);
    Route::post('send/single/employee/payroll/pdf', ['as'=>'send_employee_payroll_report', 'uses'=>'PayrollController@send_employee_payroll_report_pdf']);

    /* add Allowed Leave */
    Route::get('add/school/allowed/leave', ['as'=>'add_allowed_leave', 'uses'=>'PayrollController@add_allowed_leave']);
    Route::get('edit/school/allowed/leave/{id}', ['as'=>'edit_allowed_leave', 'uses'=>'PayrollController@edit_allowed_leave']);
    Route::get('delete/school/allowed/leave/{id}', ['as'=>'delete_allowed_leave', 'uses'=>'PayrollController@delete_allowed_leave']);

    /** New Payroll */
    Route::post('add/new/payroll', ['as'=>'add_new_payroll', 'uses'=>'PayrollController@add_new_payroll']);
    Route::post('get/teacher/payroll/attendance', ['as'=>'get_payroll_attendance', 'uses'=>'PayrollController@get_payroll_attendance_report']);
    Route::post('get/employee/payroll/all/details', ['as'=>'get_payroll_all_details', 'uses'=>'PayrollController@get_payroll_all_details']);
    Route::post('get/employee/payroll/gross/details', ['as'=>'get_payroll_gross_details', 'uses'=>'PayrollController@get_payroll_gross_details']);

    /** Bonus */
    Route::get('get/add/bonus/payroll', ['as'=>'add_bonus_payroll', 'uses'=>'PayrollController@add_bonus_payroll']);
    Route::get('edit/bonus/payroll/{id}', ['as'=>'edit_bonus', 'uses'=>'PayrollController@edit_bonus']);
    Route::post('update/payroll/bonus', ['as'=>'update_bonus', 'uses'=>'PayrollController@update_bonus']);
    Route::get('delete/bonus/payroll/{id}', ['as'=>'delete_bonus', 'uses'=>'PayrollController@delete_bonus']);

    /** Deduction  */
    Route::get('get/deduction/index', ['as'=>'get_deduction', 'uses'=>'PayrollController@get_deduction']);
    Route::post('post/deduction/percentage', ['as'=>'post_deduction_percentage', 'uses'=>'PayrollController@post_deduction_percentage']);
    Route::get('delete/deduction/percentage/{id}', ['as'=>'delete_deduction', 'uses'=>'PayrollController@delete_deduction']);
    Route::get('edit/deduction/percentage/{id}', ['as'=>'edit_deduction', 'uses'=>'PayrollController@edit_deduction']);
    Route::post('update/deduction/type', ['as'=>'update_deduction_percentage', 'uses'=>'PayrollController@update_deduction_percentage']);

    /** Professional Tax **/
    Route::get('get/professional/tax/index', ['as'=>'add_professional_tax', 'uses'=>'PayrollController@add_professional_tax']);
    Route::get('edit/professional/tax/{id}', ['as'=>'edit_prof_tax', 'uses'=>'PayrollController@edit_professional_tax']);
    Route::get('delete/professional/tax/{id}', ['as'=>'delete_prof_tax', 'uses'=>'PayrollController@delete_professional_tax']);


/*********  End   **********/    
   /*
    Route::get('attendance/student', ['as' => 'user.attendance', 'uses' => 'HomeController@attendance']);
    Route::post('user/cred', ['as' => 'post.cred', 'uses' => 'HomeController@userCred']);
    Route::get('attendance/{class}/{section}', ['as' => 'user.attendata', 'uses' => 'HomeController@getAttenData']);
    Route::post('/post/attendance', ['as' => 'post.attendance', 'uses' => 'HomeController@postAttendance']);
    Route::get('view/attendance', ['as' => 'view.attendance', 'uses' => 'HomeController@viewAttendance']);
*/
    /*updated 11-5-2018 by priya*/
    Route::get('update/student/attendance/{class}/{section}/{session}', ['as'=>'update_student_attendace', 'uses'=>'HomeController@update_student_attendance']);
    Route::post('edit/student/attendance', ['as'=>'update.attendance', 'uses'=>'HomeController@edit_student_attendance']);
    /** end **/


    Route::get('attendance/student', ['as' => 'user.attendance', 'uses' => 'HomeController@attendance']);
    Route::post('user/cred', ['as' => 'post.cred', 'uses' => 'HomeController@userCred']);
    Route::get('attendance/{class}/{section}', ['as' => 'user.attendata', 'uses' => 'HomeController@getAttenData']);
    Route::post('/post/attendance', ['as' => 'post.attendance', 'uses' => 'HomeController@postAttendance']);
    Route::get('view/attendance', ['as' => 'view.attendance', 'uses' => 'HomeController@viewAttendance']);

    Route::get('get/documents/index', ['as' => 'get.documents.index', 'uses' => 'FurnitureController@documentindex']);
    Route::get('stud/attend-certify', ['as'=>'get.documents.attend_certify', 'uses'=>'FurnitureController@getAttendancecertificate']);
    Route::post('stud/hr/attend-certify', ['as'=>'documents.attend.hr.certify', 'uses'=>'FurnitureController@postAttendancecertificate']);
    Route::get('stud/bonafied-certify', ['as'=>'get.documents.bonafied', 'uses'=>'FurnitureController@getbonafiedcertificate']);
    Route::post('stud/hr/bonafied-certify', ['as'=>'documents.bonafied.hr.certify', 'uses'=>'FurnitureController@postBonafiedcertificate']);
    Route::get('stud/conduct-certify', ['as'=>'get.documents.conduct', 'uses'=>'FurnitureController@getconductcertificate']);
    Route::post('stud/hr/conduct-certify', ['as'=>'documents.conduct.hr.certify', 'uses'=>'FurnitureController@postConductcertificate']);
    Route::get('stud/feepaid-certify', ['as'=>'get.documents.feepaid', 'uses'=>'FurnitureController@getfeepaidcertificate']);
     Route::post('stud/hr/feepaid-certify', ['as'=>'documents.feepaid.hr.certify', 'uses'=>'FurnitureController@postfeepaidcertificate']);
     Route::get('stud/tobe-feepaid-certify', ['as'=>'get.documents.tobe_feepaid', 'uses'=>'FurnitureController@getfeetobepaidcertificate']);
Route::post('stud/hr/feetobe-paid-certify', ['as'=>'documents.feetobepaid.hr.certify', 'uses'=>'FurnitureController@postfeetobepaidcertificate']);
Route::get('stud/tenthpass-certify', ['as'=>'get.documents.10-pass', 'uses'=>'FurnitureController@gettenthpasscertificate']);
Route::post('stud/hr/10thpass-certify', ['as'=>'documents.10thpass.hr.certify', 'uses'=>'FurnitureController@post10thpasscertificate']);
    
    Route::get('staff/apmt-letter', ['as'=>'get.documents.apt_letter', 'uses'=>'FurnitureController@getAppointmentLetter']);
    Route::post('staff/apmt-letter/det', ['as'=>'documents.apt_letter.details', 'uses'=>'FurnitureController@appointmentLetterDetails']);
    Route::get('staff/relieve-letter', ['as'=>'get.documents.relieving_letter', 'uses'=>'FurnitureController@getRelieveLetter']);
    Route::post('staff/relieve-letter/det', ['as'=>'documents.relieve_letter.details', 'uses'=>'FurnitureController@relievingLetterDetails']);
    Route::get('staff/showcausenotice', ['as'=>'get.documents.showcause_notice', 'uses'=>'FurnitureController@getShowCauseNotice']);
    Route::post('staff/showcause/det', ['as'=>'documents.showcausenotice.details', 'uses'=>'FurnitureController@showcausenoticeDetails']);
    Route::get('staff/bonafied-IT', ['as'=>'get.documents.bonafied-IT', 'uses'=>'FurnitureController@getBonafiedCertificateIT']);
    Route::post('staff/bonafied/det', ['as'=>'documents.bonafied.details', 'uses'=>'FurnitureController@bonafiedITDetails']);
    Route::get('staff/bonafied-student', ['as'=>'get.documents.bonafied-student', 'uses'=>'FurnitureController@getBonafiedCertificatestudent']);
Route::post('staff/bonafiedstudent/det', ['as'=>'documents.bonafiedstudent.details', 'uses'=>'FurnitureController@bonafiedstudentDetails']);
Route::get('student/idcard', ['as'=>'get.documents.idcard', 'uses'=>'FurnitureController@studentIDCard']);
Route::post('post/idcard/studentcard',  ['as' => 'single.student.idcard', 'uses' => 'FurnitureController@getStudentIDcard']);
Route::post('getid/student/section/ajax', ['as' => 'get.studentidcard.section', 'uses' => 'LibraryController@getStudentidcardSection']);
Route::post('get/id/student/ajax',  ['as' => 'get.student.idcard', 'uses' => 'LibraryController@Stu_idcardStudent']);
    /*updated 11-5-2018 by priya*/
    Route::get('update/student/attendance/{class}/{section}/{session}', ['as'=>'update_student_attendace', 'uses'=>'HomeController@update_student_attendance']);
    Route::post('edit/student/attendance', ['as'=>'update.attendance', 'uses'=>'HomeController@edit_student_attendance']);
    /** end **/

    Route::get('attendance/teacher', ['as'=>'user.teacherAttendance', 'uses'=>'HomeController@teacherAttendance']);
    Route::post('attendance/submit/teacher', ['as'=>'user.postAttendanceTeacher', 'uses'=>'HomeController@postAttendanceTeacher']);
    Route::post('busroutes/stud/sect/ajax', ['as' => 'busroute.studentsbusfee.section', 'uses' => 'LibraryController@getroutef']);
    Route::get('del/sion/bus/boarddet',  ['as' => 'fee.sion.boardstop.delete', 'uses' => 'HomeController@deleteboarddetails']);
    Route::post('add/sion/bus/busfeedetails1',  ['as' => 'post.busfee.details', 'uses' => 'HomeController@busfeedetails']);
    Route::get('homework', ['as' => 'user.homework', 'uses' => 'HomeController@homework']);
    Route::get('expenses', ['as' => 'user.expend', 'uses' => 'ExpenditureController@expCreate']);
    Route::post('expensePost', ['as' => 'user.postExpenditure', 'uses' => 'ExpenditureController@expensePost']);
    Route::post('expenseUpdate/{id}', ['as' => 'updateExpenditure', 'uses' => 'ExpenditureController@expenseUpdate']);
    Route::post('get/exp-catagory/save', ['as' => 'get.expcategory', 'uses' => 'ExpenditureController@storeCategory']);
    Route::get('expenseslist', ['as' => 'user.expList', 'uses' => 'ExpenditureController@expList']);
    Route::get('editexpenses/{id}', ['as' => 'editExpense', 'uses' => 'ExpenditureController@editExpense']);
    Route::get('deleteexpenses/{id}', ['as' => 'deleteExpense', 'uses' => 'ExpenditureController@deleteExpense']);
    Route::get('viewexpenses/{id}', ['as' => 'viewExpense', 'uses' => 'ExpenditureController@viewExpense']);
    Route::get('expensesreport', ['as' => 'expensesreport', 'uses' => 'ExpenditureController@expensesreport']);
    Route::post('expensesreportGenerate', ['as' => 'expensesreportGenerate', 'uses' => 'ExpenditureController@expensesreportGenerate']);
    Route::get('furniturelist', ['as' => 'furniturelist', 'uses' => 'FurnitureController@furnitureList']);
    Route::get('furniture', ['as' => 'furniture', 'uses' => 'FurnitureController@addFurniture']);
    Route::post('get/category-type/list', ['as' => 'addFurnitureType', 'uses' => 'FurnitureController@addFurnitureType']);//ajax request
    Route::post('get/fur-catagory/save', ['as' => 'addFurnitureCategory', 'uses' => 'FurnitureController@addFurnitureCategory']);//ajax request
    Route::post('get/fur-subcatagory/save', ['as' => 'addFurnitureSubCategory', 'uses' => 'FurnitureController@addFurnitureSubCategory']);//ajax request
    Route::post('get/sub-category/list', ['as' => 'getFurnitureSubCategory', 'uses' => 'FurnitureController@getFurnitureSubCategory']);//ajax request
    //get/sub-category/list
    Route::post('furniturePost', ['as' => 'furniturePost', 'uses' => 'FurnitureController@furniturePost']);
    Route::get('editfurniture/{id}', ['as' => 'editFurniture', 'uses' => 'FurnitureController@editFurniture']);
    Route::get('deletefurniture/{id}', ['as' => 'deleteFurniture', 'uses' => 'FurnitureController@deleteFurniture']);
    Route::get('viewfurniture/{id}', ['as' => 'viewFurniture', 'uses' => 'FurnitureController@viewFurniture']);
    Route::post('furnitureUpdate/{id}', ['as' => 'furnitureUpdate', 'uses' => 'FurnitureController@furnitureUpdate']);
    Route::get('furniturereport', ['as' => 'furnitureReport', 'uses' => 'FurnitureController@furnitureReport']);
    Route::post('gennerateFurnitureReport', ['as' => 'gennerateFurnitureReport', 'uses' => 'FurnitureController@gennerateFurnitureReport']);


    Route::get('distributelist', ['as' => 'distriFurnitureList', 'uses' => 'FurnitureController@distriFurnitureList']);
    Route::get('distribute/{id}', ['as' => 'distribute', 'uses' => 'FurnitureController@distribute']);
    Route::post('distributePost', ['as' => 'distributePost', 'uses' => 'FurnitureController@distributePost']);
    Route::get('distributeedit/{id}', ['as' => 'distributeedit', 'uses' => 'FurnitureController@distributeedit']);
    Route::get('distributeview/{id}', ['as' => 'distributeview', 'uses' => 'FurnitureController@distributeview']);
    Route::get('distributedelete/{id}', ['as' => 'distributedelete', 'uses' => 'FurnitureController@distributedelete']);
    Route::get('distributereport', ['as' => 'distributereport', 'uses' => 'FurnitureController@distributereport']);
    Route::post('distributeupdate/{id}', ['as' => 'distributeupdate', 'uses' => 'FurnitureController@distributeUpdate']);
    Route::post('distributereportGenerate', ['as' => 'distributereportGenerate', 'uses' => 'FurnitureController@distributereportGenerate']);
    Route::post('get/distribute/section-id', ['as' => 'distributesectionid', 'uses' => 'FurnitureController@distributeSectionid']);
    Route::post('get/distribute/student-id', ['as' => 'distributestudentid', 'uses' => 'FurnitureController@distributeStudentid']);

    Route::post('subjects/section', ['as' => 'fetch.subjects', 'uses' => 'HomeController@fetchSubjects']);
    Route::post('/post/homework', ['as' => 'post.homework', 'uses' => 'HomeController@postHomeWork']);
    Route::get('get/homework', ['as' => 'get.homework', 'uses' => 'HomeController@getHomework']);
    Route::post('/update/homework', ['as' => 'update.homework', 'uses' => 'PrincipalController@updateHomeWork']);
    Route::get('delete/homework/{id}', ['as' => 'deleteHomework', 'uses' => 'HomeController@deleteHomework']);

    Route::get('time-table', ['as' => 'user.timeTable', 'uses' => 'HomeController@timeTable']);
    Route::get('get/time-table', ['as' => 'get.timeTable', 'uses' => 'HomeController@getTimeTable']);
    Route::post('/post/time-table', ['as' => 'post.timeTable', 'uses' => 'HomeController@postTimeTable']);
    Route::get('delete/time-table/{id}', ['as' => 'deleteTimetable', 'uses' => 'HomeController@deleteTimetable']);
    Route::get('exam-time-table', ['as' => 'user.examTimeTable', 'uses' => 'HomeController@viewExamTimeTable']);
    Route::get('get/exam-time-table', ['as' => 'user.add_exam_timetable', 'uses' => 'HomeController@addExamTimeTable']);
    Route::post('post/exam-time-table', ['as' => 'user.post_exam_timetable', 'uses' => 'HomeController@postExamTimeTable']);
    Route::get('delete/exam-time-table/{id}', ['as' => 'deleteExamTimetable', 'uses' => 'HomeController@deleteExamTimeTable']);

    Route::get('add/gallery', ['as' => 'user.gallery', 'uses' => 'HomeController@gallery']);
    Route::get('get/gallery', ['as' => 'get.gallery', 'uses' => 'HomeController@getGallery']);
    Route::post('/post/gallery/{id}', ['as' => 'post.gallery', 'uses' => 'HomeController@postGallery']);
    Route::post('/fields/gallery', ['as' => 'fields.gallery', 'uses' => 'HomeController@fieldsGallery']);
    Route::get('delete/gallery/{id}', ['as' => 'delete.gallery', 'uses' => 'HomeController@deleteGallery']);
    Route::get('edit/gallery/{id}', ['as' => 'edit.gallery', 'uses' => 'HomeController@editGallery']);
    Route::post('update/gallery', ['as' => 'update.gallery', 'uses' => 'HomeController@updateGallery']);

    Route::get('get/video', ['as' => 'get.video', 'uses' => 'LibraryController@getVideo']);
    Route::get('view/video', ['as' => 'get.video.schoolname', 'uses' => 'LibraryController@viewVideo']);
    Route::get('view/video/student', ['as' => 'get.video.sudent', 'uses' => 'LibraryController@viewStudentVideo']);
    Route::post('/fields/video', ['as' => 'fields.video', 'uses' => 'LibraryController@fieldsVideo']);
    Route::post('getvideo/student/section/ajax', ['as' => 'get.studentsvideo.section', 'uses' => 'LibraryController@getStudentvideoSection']);
    Route::get('download/video/file/{id}',['as'=>'video.download.video', 'uses'=>'LibraryController@videodownloadvideo']);
    Route::get('delete/vid/aud/pdf',['as'=>'video.audio.delete', 'uses'=>'LibraryController@deletevideodocument']);

    Route::get('post', ['as' => 'user.post', 'uses' => 'HomeController@post']);
    Route::post('post/post', ['as' => 'post.post', 'uses' => 'HomeController@postPost']);

    Route::get('result', ['as' => 'user.result', 'uses' => 'HomeController@result']);
    Route::get('singleresult', ['as' => 'user.singleresult', 'uses' => 'HomeController@singleresult']);
    Route::post('result/cred', ['as' => 'result.cred', 'uses' => 'HomeController@resultCred']);
    Route::post('result/singlecred', ['as' => 'result.singlecred', 'uses' => 'HomeController@singleresultCred']);
    Route::get('result/{class}/{section}', ['as' => 'get.result', 'uses' => 'HomeController@getResult']);
    Route::post('/post/result', ['as'=>'post.result', 'uses' => 'HomeController@postResult']);
    Route::post('/post/singleresult', ['as'=>'post.singleresult', 'uses' => 'HomeController@singlepostResult']);
    Route::get('view/result', ['as' => 'view.result', 'uses' => 'HomeController@viewResult']);
    Route::get('/get/results/{class}/{section}', ['uses' => 'PrincipalController@getResults']);
    Route::get('/delete/result/{id}', ['uses'=>'PrincipalController@deleteResult']);
    Route::get('/edit/result/{id}', ['uses'=>'PrincipalController@editResult']);
    Route::post('/update/result', ['uses'=>'PrincipalController@updateResult']);
    Route::get('result/download/{class}/{section}/{exam}/{id}', ['as'=>'resultDownload', 'uses'=> 'LibraryController@resultDownload']);
    Route::get('view/hrresult', ['as' => 'view.hrsecresult', 'uses' => 'LibraryController@viewhrsec_Result']);
    Route::get('view/hrresult/detail', ['as' => 'view.hrsecresultdetails', 'uses' => 'LibraryController@viewhrsecResultdetails']);
    Route::get('hrsecresult/download/{class}/{section}/{exam}/{id}', ['as'=>'hrsecresultDownload', 'uses'=> 'HomeController@resultDownload']);
    Route::get('delete/all/submarks', ['as' => 'delete_all_students', 'uses' => 'PrincipalController@deleteAllMarks']);
    Route::get('view/fasaresult', ['as' => 'view.fasaresult', 'uses' => 'LibraryController@viewfasa_Result']);
    Route::get('view/fasaresult/detail', ['as' => 'view.fasaresultdetails', 'uses' => 'LibraryController@viewfasaResultdetails']);
     Route::get('fasaresult/download/{class}/{section}/{exam}/{id}', ['as'=>'fasaresultDownload', 'uses'=> 'LibraryController@fasaresultDownload']);

     Route::get('/delete/{section_id}/{subject_id}', ['as' => 'delete.subject', 'uses'=>'PrincipalController@deleteMarks']);
   
    /** @ updated 31-10-2017 by priya @ */
    Route::get('get/marks', ['as' => 'addStudentsMark', 'uses' => 'LibraryController@getStudentsMarks']);
    Route::post('add/students/marks/section', ['as' => 'add.students.marks', 'uses' => 'LibraryController@addStudentsMarksSection']);
    Route::get('get/students/marks/details', ['as' => 'getStudentMarkDetails', 'uses' => 'LibraryController@getStudentsResultDetail']);
    Route::post('add/students/exam/result', ['as' => 'add.students.exam.result', 'uses' => 'LibraryController@addStudentsExamResult']);
    Route::post('post/student/mark', ['as' => 'postStudentResult', 'uses' => 'LibraryController@postStudentsExamResult']);
    Route::post('get/grade/details', ['as' => 'getGradeSystem', 'uses' => 'LibraryController@getGradeSystem']);
    Route::post('get/fa/grade/details', ['as' => 'getFAGradeSystem', 'uses' => 'LibraryController@getfaGradeSystem']);
    Route::post('get/saa/grade/details', ['as' => 'getsaaGradeSystem', 'uses' => 'LibraryController@getsaGradeSystem']);
    Route::post('post/student/markverify', ['as' => 'postStudentResultverify', 'uses' => 'LibraryController@postStudentsExamResultverify']);
    /** @ end @ */
    //send smssmssend
    Route::get('sendsms', ['as' => 'user.smssend', 'uses' => 'HomeController@sendsms']);
    Route::post('sendsmsclass',['as'=>'user.sendsmsclass', 'uses' => 'HomeController@sendsmsclass']);
    Route::get('smsusernamedit',['as'=>'user.smsusernamedit', 'uses' => 'HomeController@smsusernamedit']);

    Route::post('editsmsusername',['as'=>'editsmsusername', 'uses' => 'HomeController@editsmsusername']);


    /** @ updated 3-11-2017 @ **/
    Route::post('add/students/marks/teacher', ['as' => 'add.students.marks.teacher', 'uses' => 'LibraryController@getTeachersDetails']);
    /** @ end @ **/

    Route::get('notification', ['as' => 'user.notification', 'uses' => 'HomeController@notification']);
    Route::post('post/device/notification', ['as' => 'post.device.notification', 'uses' => 'HomeController@postDeviceNotification']);
    Route::get('view/notification', ['as' => 'view.notification', 'uses' => 'HomeController@viewNotification']);
    Route::get('delete/notification/history/{id}', ['as' => 'deleteNotificationHistory', 'uses' => 'HomeController@deleteNotificationHistory']);

    Route::get('notice', ['as' => 'user.notice', 'uses' => 'HomeController@notice']);
    Route::post('post/notice', ['as' => 'post.notice', 'uses' => 'HomeController@postNotice']);
    Route::get('get/notice', ['as' => 'get.notice', 'uses' => 'HomeController@getNotice']);
    Route::get('delete/notice/{id}', ['as' => 'delete.notice', 'uses' => 'HomeController@deleteNotice']);
    Route::get('drivertrack', [
        'as'=>'user.drivertrack',
        'uses'=>'HomeController@drivertrack'
    ]);
    Route::get('driverslocation',['as'=>'user.driversloction','uses'=>'HomeController@driverslocation']);
    Route::get('message', [
        'as'=>'user.message',
        'uses'=>'HomeController@message'
    ]);

    Route::get('student/mapping', [
        'as'=>'user.trasport',
        'uses'=>'HomeController@trasport'
    ]);

    Route::get('bus/stop/{id}', ['uses'=>'HomeController@getStopBus']);

    Route::post('post/student/mapping', [
        'as'=>'post.mapping',
        'uses'=>'HomeController@postMapping'
    ]);

    Route::get('mapping/delete/{id}', [
        'as'=>'delete.mapping',
        'uses'=>'HomeController@deleteMapping'
    ]);


    Route::get('fee/frequency', ['as'=>'fee.frequency', 'uses'=>'HomeController@feeFrequency']);
    Route::post('post/frequency', ['as'=>'post.frequency', 'uses'=>'HomeController@postFrequency']);
    Route::get('delete/frequency/{id}', ['as'=>'delete.frequency', 'uses'=>'HomeController@deleteFrequency']);
    Route::get('edit/frequency/{id}', ['as'=>'edit.frequency', 'uses'=>'HomeController@editFrequency']);
    Route::post('update/frequency', ['as'=>'update.frequency', 'uses'=>'HomeController@updateFrequency']);

    Route::get('fee/structure', ['as'=>'fee.structure', 'uses'=>'HomeController@feeStructure']);
    Route::post('installment', ['as'=>'installment', 'uses'=>'HomeController@postInstallment']);
    Route::post('fee/structure', ['as'=>'post fee structure', 'uses'=>'HomeController@postFeestructure']);
    Route::post('getfee/student/section/ajax', ['as' => 'get.studentsfee.section', 'uses' => 'LibraryController@getStudentfeeSection']);
Route::post('get/fee/student/ajax',  ['as' => 'get.fee.student.payment', 'uses' => 'LibraryController@singStu_feeStudent']);
Route::get('get/students/buspayments',  ['as' => 'get.students.buspayments', 'uses' => 'HomeController@busPaymentstr']);
Route::get('post/bufees/studentbusfee',  ['as' => 'single_studentbusfee.new', 'uses' => 'HomeController@poststudentwisebusfeedetails']);
Route::get('view/sion/bus/busdet',  ['as' => 'fee.view.sion.busdetail', 'uses' => 'HomeController@viewbusdetails']);
Route::get('del/sion/bus/busdet',  ['as' => 'fee.sion.busdet.delete', 'uses' => 'HomeController@deletebusdetails']);
Route::get('view/bufees/studentbusfee',  ['as' => 'buswise_studentbusfee.new', 'uses' => 'LibraryController@viewbuswisebusfeedetails']);

    Route::post('viewgetfeebussss/student/section/ajax', ['as' => 'viewget.studentsbusfee.section', 'uses' => 'LibraryController@viewgetStates']);
    Route::get('post/singlefees/studentfee',  ['as' => 'single_studentfee.new', 'uses' => 'HomeController@poststudentwisefeedetails']);
    Route::get('post/sectionfees/studentfee',  ['as' => 'section_studentfee.view', 'uses' => 'LibraryController@viewstudentwisefeedetails']);
    Route::post('viewfee/student/section/ajax', ['as' => 'view.studentsfee.section', 'uses' => 'LibraryController@viewStudentfeeSection']);
        Route::get('get/students/payments',  ['as' => 'get.students.payments', 'uses' => 'HomeController@studentPaymentstr']);
        Route::post('post/fees/studentfee',  ['as' => 'post.classwise.studentfee', 'uses' => 'HomeController@postClasswisedetailsforstuPayment']);
        Route::post('post/fee/paymentfee',  ['as' => 'post.classwisefee.payment', 'uses' => 'HomeController@postClasswisePaymentfee']);
         Route::get('view/sion/fees/student',  ['as' => 'fee.view.sion.structure', 'uses' => 'HomeController@viewSionFeeStructure']);
        Route::post('view/sion/fees/details',  ['as' => 'fee.view.sion.structuredetails', 'uses' => 'HomeController@viewSionFeeStructuredetails']);
        Route::get('del/sion/fees/delete',  ['as' => 'fee.sion.structure.delete', 'uses' => 'HomeController@deletSionFeeStructure']);
        Route::get('fee/collectionnewfee', ['as' => 'user.feeCollectionnewfee', 'uses' => 'HomeController@feeCollectionnewfee']);
        Route::post('payment/schoolfee', ['as' => 'schoolPaymentnew','uses' => 'HomeController@school_paymentfee']);
        Route::post('feepay/collectionnew', ['as' => 'user.checkboxamtnew', 'uses' => 'HomeController@paymentCollectionnewfee']);
        Route::post('recvdamt/collectionnew', ['as' => 'user.receivedamountnew', 'uses' => 'HomeController@paymentReceivednew']);
        Route::get('del/sion/bus/busdetails',  ['as' => 'fee.add.sion.busdetails', 'uses' => 'HomeController@busdetails']);
        Route::get('add/sion/bus/busroute',  ['as' => 'add.bus.routename', 'uses' => 'HomeController@busroutename']);
        Route::get('del/sion/stop/boardingfees',  ['as' => 'fee.view.sion.boardingfees', 'uses' => 'HomeController@addBoardingPointfees']);
        Route::post('add/sion/bus/busfeedetails1',  ['as' => 'post.busfee.details', 'uses' => 'HomeController@busfeedetails']);
        Route::get('add/sion/bus/studentmap',  ['as' => 'fee.add.sion.mapping', 'uses' => 'HomeController@addStudentMapping']);
        Route::post('add/sion/bus/mapdetails',  ['as' => 'fee.add.sion.studentmapping', 'uses' => 'HomeController@importStudent']);
        Route::get('del/sion/stufees/delete',  ['as' => 'stufee.sion.structure.delete', 'uses' => 'StudentController@deletSionstudentFeeStructure']);
        Route::get('view/sion/bus/busfee',  ['as' => 'fee.view.sion.boarding', 'uses' => 'HomeController@viewbusfeedetails']);
        Route::get('delete/sion/bus/boarding',  ['as' => 'fee.sion.busfee.delete', 'uses' => 'HomeController@deletebusfeedetails']);
        Route::get('get/students/buspaymentsindex',  ['as' => 'get.students.buspayments123', 'uses' => 'HomeController@busPaymentindex']);
        Route::get('get/multistudents/buspayments',  ['as' => 'get.multistudents.buspayments', 'uses' => 'HomeController@multibusPaymentstr']);
        Route::get('get/sion/bus/route',  ['as' => 'get.busroute.details', 'uses' => 'LibraryController@get_bus_route']);
        Route::post('getfeebussss/student/section/ajax', ['as' => 'get.studentsbusfee.section', 'uses' => 'LibraryController@getStates']);
        Route::post('get/feebusess/student/ajax',  ['as' => 'get.fee.studentbus.payment', 'uses' => 'LibraryController@singStu_feebusStudent']);
        Route::get('get/students/singlefee',  ['as' => 'single_studentfee', 'uses' => 'HomeController@singlestudentfeestr']);
        Route::get('del/sion/boardfees/delete',  ['as' => 'fee.sion.board.delete', 'uses' => 'HomeController@deletboardSionFeeStructure']);
        Route::get('duplicate/sion/recpt',  ['as' => 'DuplicateReceipt', 'uses' => 'HomeController@duplicateReceipt']);
    /** @ Updated 16-3-2018 @ **/
    Route::get('get/previous/payment',  ['as' => 'get.previous.payment', 'uses' => 'HomeController@previousYearPayment']);
    Route::post('get/previous/student/ajax',  ['as' => 'get.previous.student.payment', 'uses' => 'HomeController@previousYearPaymentStudent']);
    Route::post('post/previous/payment',  ['as' => 'post.previous.payment', 'uses' => 'HomeController@postPreviousYearPayment']);


    Route::get('fee/structure/{session}/{class}', ['as'=>'fee.structure.class', 'uses'=>'HomeController@feeStructureByclass']);
    Route::post('fee/structure/delete', ['as'=>'fee.structure.delete', 'uses'=>'HomeController@deleteFeeStructure']);
    Route::get('get/fee/head/{type}', ['uses' => 'HomeController@getFeeHead']);
    Route::post('fee/head/post/amount', ['as'=>'feeHeadAmountPost', 'uses'=>'HomeController@feeHeadAmountPost']);
    Route::get('fee/head/amount/delete/{id}', ['as'=>'feeHeadAmountDelete', 'uses'=>'HomeController@feeHeadAmountDelete']);
    Route::get('list/structure', ['as'=>'list.structure', 'uses'=>'HomeController@listStructure']);
    Route::post('list/structure', ['as'=>'post.structure', 'uses'=>'HomeController@postStructure']);
    Route::get('delete/structure/{id}', ['as'=>'delete.structure', 'uses'=>'HomeController@deleteStructure']);
    Route::get('edit/structure/{id}', ['as'=>'edit.structure', 'uses'=>'HomeController@editStructure']);
    Route::post('update/structure', ['as'=>'update.structure', 'uses'=>'HomeController@updateStructure']);

    Route::get('admission/fee', ['as'=>'fee.admission', 'uses'=>'HomeController@admissionFee']);
    Route::post('admission/fee/post', ['as'=>'post.admission', 'uses'=>'HomeController@postAdmissionFee']);
    Route::get('admission/fee/delete/{id}', ['as'=>'delete.feeAdmission', 'uses'=>'HomeController@deleteAdmissionFee']);

    Route::get('fee/head/amount', ['as'=>'fee_head.amount', 'uses'=>'HomeController@feeHeadAmount']);

    Route::get('fee/registration', ['as' => 'registration', 'uses' => 'HomeController@registration']);
    Route::post('fee/registration/post', ['as' => 'feeregistrationPost', 'uses' => 'HomeController@feeregistrationPost']);
    Route::get('fee/registration/delete/{id}', ['as' => 'registration.Delete', 'uses' => 'HomeController@feeregistrationDelete']);
    Route::get('del/sion/boardingfees/delete',  ['as' => 'fee.sion.boardingwise.delete', 'uses' => 'HomeController@deleboardingSionFeeStructure']);
    Route::post('payment/student/{register_no}', ['as' => 'single student payment','uses' => 'HomeController@single_stu_payment']);
    Route::get('payfee/{register_no}/{fee_id}', ['as' => 'payfee','uses' => 'HomeController@payfee']);

    Route::get('fee/security', ['as' => 'security', 'uses' => 'HomeController@security']);
    Route::post('fee/security/post', ['as' => 'feeSecurityPost', 'uses' => 'HomeController@feeSecurityPost']);
    Route::get('fee/security/delete/{id}', ['as' => 'security.Delete', 'uses' => 'HomeController@feeSecurityDelete']);

    Route::get('fee/collection', ['as' => 'user.feeCollection', 'uses' => 'HomeController@feeCollection']);

    Route::post('payfee',  ['as' => 'pay', 'uses' => 'HomeController@pay']);
    Route::post('search/student', ['uses'=>'HomeController@searchStudent']);
    Route::get('get/student/info/{id}', ['uses'=>'HomeController@getInfoStudent']);
    Route::get('get/fee/head/{id}/{month}', ['uses' => 'HomeController@getMonthFeeHead']);

    Route::post('fee/collection/post', ['as'=>'fee.collection.post', 'uses'=>'HomeController@feeCollectionPost']);
    Route::get('invoice', ['as'=>'invoiceCreate', 'uses'=>'HomeController@invoiceCreate']);

    Route::post('post/fee', ['as' => 'post.fee', 'uses'=> 'HomeController@postFee']);
    Route::get('view/fee', ['as' => 'view.fee', 'uses' => 'HomeController@viewFee']);
    Route::get('fee/statement/{reg}', ['as'=> 'feeStatement', 'uses'=> 'HomeController@feeStatement']);


    Route::get('report', [
        'as'=>'user.report',
        'uses'=>'HomeController@report'
    ]);

    Route::get('attendance/report', ['as' => 'attendanceReport', 'uses' => 'HomeController@attendanceReport']);

    Route::get('report/students', ['as'=>'studentsReport', 'uses'=>'HomeController@studentsReport']);

    Route::get('report/fee-collection', ['as'=>'feeCollectionReport', 'uses'=>'HomeController@feeCollectionReport']);

    Route::get('newtermreport/stafffee-collection', ['as'=>'new.fee.staffcollectionreport', 'uses'=>'HomeController@selected_term_staffreport']);
    Route::post('newtermreport/staff-report', ['as'=>'new.fee.term.class.staffreport', 'uses'=>'HomeController@selected_term_staffreportdetails']);
    Route::get('term/indiv-collectionadmin', ['as'=>'term.individual.staffreportadmin', 'uses'=>'HomeController@individual_collectionReportadmin']);
    Route::post('term/indiv-collection', ['as'=>'term.individual.staffreport', 'uses'=>'HomeController@individual_collection']);
    Route::get('term/indiv-balanceadmin', ['as'=>'term.individual.staffreportbalance', 'uses'=>'HomeController@individual_balanceReportadmin']);
    Route::post('term/indiv-balanceadmindetails', ['as'=>'new.fee.term.class.balancereport', 'uses'=>'HomeController@individual_balanceReportadmindetails']);
    Route::get('manager/school_fee', ['as' => 'user.schoolfee.index', 'uses' => 'LibraryController@schoolfeeindex']);
    Route::get('school/received/report', ['as'=>'schoolAllfee_Report', 'uses'=>'HomeController@school_Received_Report']);
    Route::post('school/recd/reportdet', ['as'=>'school.received.reportdetails', 'uses'=>'HomeController@school_Received_Reportdetails']);
    Route::get('school/balance/report', ['as'=>'schoolAllbalance_report', 'uses'=>'HomeController@school_balance_Report']);
Route::post('school/balan/reportdet', ['as'=>'school.fee.balance.reportdet', 'uses'=>'HomeController@school_Balance_reportDetails']);
Route::get('school/Overall/report', ['as'=>'schoolOverall_report', 'uses'=>'HomeController@school_Overall_Report']);
Route::post('school/Overall/reportdet', ['as'=>'school.fee.overall.reportdet', 'uses'=>'HomeController@school_Overall_reportDetails']);
Route::get('school/Datewise/report', ['as'=>'schoolDatewise_report', 'uses'=>'HomeController@school_Datewise_Report']);
Route::post('school/Datewise/reportdet', ['as'=>'school.fee.Datewise.reportdet', 'uses'=>'HomeController@school_Datewise_reportDetails']);
Route::get('get/students/indexfee',  ['as' => 'get.students.feeindex', 'uses' => 'HomeController@studentfeeindexstr']);
/****** home visit form************/
Route::get('manager/homevisit', ['as' => 'user.homevisit.index', 'uses' => 'LibraryController@homevisitindex']);
Route::get('manager/homevisitcreate', ['as' => 'user.homevisitcreat.index', 'uses' => 'LibraryController@homevisitcreate']);

Route::post('get/homevisit/student/ajax',  ['as' => 'get.homevisit.student', 'uses' => 'LibraryController@singStu_homevisitStudent']);
Route::post('gethomevisit/student/section/ajax', ['as' => 'get.homevisit.section', 'uses' => 'LibraryController@getStudenthomevisitSection']);
Route::get('post/homevisit/form',  ['as' => 'homevisit.new', 'uses' => 'LibraryController@poststudenthomevisitdetails']);
Route::post('gethomevisit/report/section/ajax', ['as' => 'get.homevisit.report.section', 'uses' => 'LibraryController@getReporthomevisitSection']);
Route::post('post/homevisit/newform',  ['as' => 'user.homevisitaddform', 'uses' => 'LibraryController@posthomevisitdetails']);

Route::get('manager/homevisitreport', ['as' => 'user.homevisitreport.index', 'uses' => 'LibraryController@homevisitreport']);
Route::post('gethomevisitrep/student/section/ajax', ['as' => 'get.homevisit.section', 'uses' => 'LibraryController@getStudenthomevisitrepSection']);
Route::get('post/homevisit/report',  ['as' => 'homevisitreport.new', 'uses' => 'LibraryController@poststudenthomevisitreportdetails']);

Route::get('manager/homevisitreport', ['as' => 'user.homevisitreport.index', 'uses' => 'LibraryController@homevisitreport']);
Route::post('manager/homevisitreportdet', ['as' => 'canvashReport', 'uses' => 'LibraryController@homevisitreportdetails']);
Route::get('manager/daily/homevisitreportdet', ['as' => 'dailycanvashReport', 'uses' => 'LibraryController@homevisitreportdetails']);
    /**** updated 14-10-2017 by priya *****/
    Route::get('report/download', ['as'=>'reportDownload', 'uses'=>'LibraryController@reportDownload']);
    Route::get('library/report', ['as' => 'libraryReport', 'uses' => 'LibraryController@libraryReport']);
    Route::get('student/analyst/report', ['as' => 'analystReport', 'uses' => 'LibraryController@analystReport']);
    Route::post('get/student/section/ajax', ['as' => 'get.student.section', 'uses' => 'LibraryController@getStudentSection']);
    Route::get('teacher/analyst/report', ['as' => 'teacherReport', 'uses' => 'LibraryController@teacherReport']);
   /***/

   Route::get('library/index', ['as' => 'user.library.index', 'uses' => 'HomeController@libraryindex']);
    Route::get('library/report/index', ['as' => 'user.library.reportindex', 'uses' => 'HomeController@libraryreportindex']);
    Route::get('library/report/bookIssue', ['as' => 'user.bookIssue.report', 'uses' => 'HomeController@libraryreportbookIssue']);
    Route::post('get/library/issuereport', ['as' => 'get.library.issuereport', 'uses' => 'HomeController@issuebookreportdetails']);
    Route::get('library/report/gateentry', ['as' => 'user.gateentry.report', 'uses' => 'HomeController@getgateentryReport']);
    Route::post('library/report/gatereg_report', ['as' => 'get.library.gatereg_report', 'uses' => 'HomeController@gateentryReport']);
    Route::get('library/report/subject', ['as' => 'librarysubjectReport', 'uses' => 'HomeController@getlibrarysubjectReport']);
    Route::post('library/report/subject_report', ['as' => 'get.library.subject_report', 'uses' => 'HomeController@librarysubject_report']);

    Route::get('library', ['as' => 'user.library', 'uses' => 'HomeController@library']);
    Route::get('book/lib/list', ['as'=>'user.bookview', 'uses'=>'HomeController@bookList']);
    Route::post('book/view/list', ['as'=>'list.book.lib', 'uses'=>'HomeController@bookviewlist']);
    Route::get('edit/library/{id}', ['as' => 'edit.books', 'uses' => 'HomeController@editbooks']);
    Route::post('update/library', ['as' => 'update.library', 'uses' => 'HomeController@updatebooks']);
    
    Route::get('delete/library{id}', ['as' => 'deleteBook', 'uses' => 'HomeController@deleteBook']);
    Route::get('book/category', ['as'=>'user.bookCategory', 'uses'=>'HomeController@bookCategory']);
    Route::post('book/category/post', ['as'=>'post.category', 'uses'=>'HomeController@postCategory']);
    Route::get('book/category/delete/{id}', ['as'=>'deleteCategory', 'uses'=>'HomeController@deleteCategory']);
    Route::post('post/library', ['as' => 'post.library', 'uses' => 'HomeController@postLibrary']);
    Route::get('issue/book', ['as' => 'user.issue.book', 'uses' => 'HomeController@issueBook']);
    Route::post('issue/book/post', ['as' => 'issue.book.post', 'uses' => 'HomeController@issueBookPost']);
    Route::get('library/subject', ['as' => 'user.bookSubject', 'uses' => 'HomeController@bookSubject']);
    Route::post('book/subject/post', ['as'=>'post.bookSubject', 'uses'=>'HomeController@postbookSubject']);
    /**** updated 14-10-2017 by priya *****/
    Route::post('get/student/library/detail', ['as' => 'get.stduent.library.detail', 'uses' => 'LibraryController@getStudentReturnBook']);
    Route::post('get/teacher/library/detail', ['as' => 'get.teacher.library.detail', 'uses' => 'LibraryController@getTeacherReturnBook']);
    Route::post('get/student/library', ['as' => 'get.stduent.library', 'uses' => 'LibraryController@getStudentLibrary']);
    Route::post('get/teacher/library', ['as' => 'get.teacher.library', 'uses' => 'LibraryController@getTeacherLibrary']);
    /*****/
    /** @ updated 26-10-2017 @ */
    Route::get('get/teacher/report', ['as' => 'post.teacher.detail', 'uses' => 'LibraryController@getTeacherReport']);
    /** @ end @ */
    Route::get('get/gate/register', ['as' => 'user.library.register', 'uses' => 'LibraryController@getgateregister']);
    Route::get('get/gate/register/in', ['as' => 'user.register.libin', 'uses' => 'LibraryController@getgateregisterin']);
    Route::get('get/gate/register/out', ['as' => 'user.register.libout', 'uses' => 'LibraryController@getgateregisterout']);
    Route::post('post/gate/register/in', ['as' => 'gate.inregister.post', 'uses' => 'LibraryController@postgateinregister']);

    Route::post('get/book/library', ['as' => 'get.book.library', 'uses' => 'HomeController@getbookLibrary']);
    Route::get('return/book', ['as' => 'return.book', 'uses' => 'HomeController@returnBook']);
    Route::post('return/book/post', ['as' => 'return.book.post', 'uses' => 'HomeController@returnBookPost']);
    Route::post('book/info', ['as' => 'book.info', 'uses' => 'HomeController@bookInfo']);
    Route::get('fine/receipt/{id}', ['as' => 'fineReceipt', 'uses' => 'HomeController@fineReceipt']);

    Route::get('holiday', [
        'as'=>'user.holiday',
        'uses'=>'HomeController@holiday'
    ]);

    Route::get('manager/data', ['as' => 'user.managerData', 'uses' => 'HomeController@managerData']);
    Route::post('/import/student', ['as' => 'post.manager', 'uses' => 'HomeController@importStudent']);

    Route::get('manager/export', ['as' => 'user.managerExport', 'uses' => 'HomeController@managerExport']);
    Route::get('export/session', ['as' => 'export.session', 'uses' => 'MasterController@expotSession']);
    Route::get('export/session/view', ['as' => 'export.session.view', 'uses' => 'MasterController@expotSessionView']);
    Route::get('export/class', ['as' => 'export.class', 'uses' => 'MasterController@exportClass']);
    Route::get('export/class/view', ['as' => 'export.class.view', 'uses' => 'MasterController@expotClassView']);
    Route::get('export/section', ['as' => 'export.section', 'uses' => 'MasterController@exportSection']);
    Route::get('export/section/view', ['as' => 'export.section.view', 'uses' => 'MasterController@expotSectionView']);
    Route::get('export/subject', ['as' => 'export.subject', 'uses' => 'MasterController@exportSubject']);
    Route::get('export/subject/view', ['as' => 'export.subject.view', 'uses' => 'MasterController@expotSubjectView']);
    Route::get('export/exam/type', ['as' => 'export.exam', 'uses' => 'MasterController@exportExamType']);
    Route::get('export/exam/view', ['as' => 'export.exam.view', 'uses' => 'MasterController@exportExamView']);
    Route::get('export/staff/type', ['as' => 'export.staff', 'uses' => 'MasterController@exportStaffType']);
    Route::get('export/staff/view', ['as' => 'export.staff.view', 'uses' => 'MasterController@exportStaffView']);
    Route::get('export/events', ['as' => 'export.events', 'uses' => 'MasterController@exportEvents']);
    Route::get('export/events/view', ['as' => 'export.events.view', 'uses' => 'MasterController@exportEventsView']);
    Route::get('export/caste', ['as' => 'export.caste', 'uses' => 'MasterController@exportCaste']);
    Route::get('export/caste/view', ['as' => 'export.caste.view', 'uses' => 'MasterController@exportCasteView']);

    Route::get('export/religion', ['as' => 'export.religion', 'uses' => 'MasterController@exportReligion']);
    Route::get('export/religion/view', ['as' => 'export.religion.view', 'uses' => 'MasterController@exportReligionView']);
    Route::get('export/bus', ['as' => 'export.bus', 'uses' => 'MasterController@exportBus']);
    Route::get('export/bus/view', ['as' => 'export.bus.view', 'uses' => 'MasterController@exportBusView']);

    Route::get('post', [
        'as'=>'user.post',
        'uses'=>'HomeController@post'
    ]);

    Route::group(['prefix'=>'payroll'], function(){

        Route::get('deduction', ['as'=>'deduction', 'uses'=>'HomeController@deduction']);

        Route::post('deduction/post', ['as'=>'postDeduction', 'uses'=>'HomeController@postDeduction']);

        Route::get('deduction/delete/{id}', ['as'=>'deleteDeduction', 'uses'=>'HomeController@deleteDeduction']);

        Route::get('salary/input', ['as'=>'inputSalary', 'uses'=>'HomeController@inputSalary']);

        Route::get('employee/get/{staff}', ['uses'=>'HomeController@employeeGet']);

        Route::post('employee/salary/post', ['as'=>'employeeSalaryPost','uses'=>'HomeController@employeeSalaryPost']);

        Route::get('employee/salary/delete/{id}', ['as'=>'employeeSalaryDelete','uses'=>'HomeController@employeeSalaryDelete']);

        Route::get('calculate/salary', ['as'=>'calculateSalary', 'uses'=>'HomeController@calculateSalary']);
    });

    Route::get('knowledge/bank', ['as'=>'knowledgeBank', 'uses'=>'HomeController@knowledgeBank']);

    Route::post('post/knowledge/{id}', ['as'=>'postKnowledge', 'uses'=>'HomeController@postKnowledge']);

    Route::get('knowledge/view', ['as'=>'viewKnowledge', 'uses'=>'HomeController@viewKnowledge']);

    Route::get('knowledge/delete/{id}', ['as'=>'deleteQuestion', 'uses'=>'HomeController@deleteQuestion']);


    /*****************************************************************************
     *                              SYLLABUS MODULE
     *****************************************************************************/

    Route::get('syllabus/view/index', ['as'=>'master.syllabus.index', 'uses'=>'HomeController@viewSyllabusIndex']);
    Route::post('get/syllabus/subject/ajax', ['as'=>'get.syllabus.subject.ajax', 'uses'=>'HomeController@getSyllabusSubjects']);
    Route::post('post/syllabus/subject', ['as'=>'post.syllabus.class', 'uses'=>'HomeController@postSyllabus']);
    Route::get('view/syllabus/subject', ['as'=>'view.syllabus.list', 'uses'=>'HomeController@viewSyllabusList']);
    Route::get('delete/syllabus/id/{id}', ['as'=>'deleteSyllabusId', 'uses'=>'HomeController@deleteSyllabusId']);
    Route::get('edit/syllabus/id/{id}', ['as'=>'editSyllabusId', 'uses'=>'HomeController@editSyllabusId']);
    Route::post('update/syllabus/id', ['as'=>'update.syllabus.class', 'uses'=>'HomeController@updateSyllabusId']);
    Route::get('get/syllabus/detail', ['as'=>'get.syllabus.index', 'uses'=>'HomeController@getSyllabusDetail']);


    /*****************************************************************************
     *                              TRANING MATERIAL MODULE
     *****************************************************************************/

    Route::get('get/training/material/index', ['as'=>'get.training.material.index', 'uses'=>'HomeController@getTraningMaterial']);



    // Route::get('gallery', [
    // 	'as'=>'user.gallery',
    // 	'uses'=>'HomeController@gallery'
    // 	]);

    Route::get('users/role', ['as' => 'user.usersRole', 'uses' => 'HomeController@usersRole']);
    Route::get('asign/role/{id}', ['as' => 'asign.usersRole', 'uses' => 'HomeController@asignUsersRole']);
    Route::get('role/delete/{id}', ['as'=>'deleteUserRole', 'uses'=>'HomeController@deleteUserRole']);
    Route::get('change/password/{id}', ['as'=>'changePassword', 'uses'=>'HomeController@changePassword']);

    Route::post('change/password/post/{id}', ['as'=>'postPassword', 'uses'=>'HomeController@postPassword']);
    Route::post('user/role/post', ['as' => 'user.role.post', 'uses' => 'HomeController@userRolePost']);

    //Route::get('total/attendance', ['uses'=>'PrincipalController@totalAttendance']);


    Route::get('class/attendance/count', ['uses'=>'PrincipalController@classAttendanceCount']);

    Route::post('report/attendance/single/student', ['as'=>'attendanceReportStudent', 'uses'=>'PrincipalController@attendanceReportStudent']);

    Route::post('report/attendance/class', ['as'=>'classAttendanceReport', 'uses'=>'PrincipalController@classAttendanceReport']);

    Route::get('download', ['as'=>'download', 'uses'=>'PrincipalController@download']);
    Route::get('download123', ['as'=>'download123', 'uses'=>'PrincipalController@download123']);
});

Route::group(['prefix' => 'users', 'middleware' => ['auth', 'ManyRoles:user_role|school|']], function(){
    Route::get('total/attendance', ['uses'=>'PrincipalController@totalAttendance']);
    Route::get('total/attendance/employer', ['uses'=>'PrincipalController@totalEmployerAttendance']);
});


/*
|-----------------------------------------------------------
| Admin Panel
|-----------------------------------------------------------
*/

Route::get('admin', ['as' => 'login', 'uses'=>'AdminController@adminLogin']);

Route::group(['middleware'=>['auth','admin'], 'prefix'=>'auth'], function(){

    Route::get('dashboard', ['as' => 'admin.dashboard', 'uses'=>'AdminController@dashBoard']);

    // School Name Input
    Route::get('view/school', ['as' => 'viewSchool', 'uses' => 'AdminController@viewSchool']);
    Route::get('add/school', ['as' => 'createSchool', 'uses' => 'AdminController@createSchool']);

    Route::post('school/input', ['as' => 'schoolInput', 'uses' => 'AdminController@schoolInput']);

    Route::get('school/delete/{id}', ['as' => 'deleteSchool', 'uses' => 'AdminController@deleteSchool']);

    Route::get('edit/school/{id}', ['as' => 'editSchool', 'uses' => 'AdminController@editSchool']);

    Route::post('update/school', ['as' => 'updateSchool', 'uses' => 'AdminController@updateSchool']);
    // Account Create
    // Route::get('view/schools', ['as' => 'viewSchools', 'uses' => 'AdminController@viewSchools']);
    Route::get('smsusers', ['as' => 'smsusers', 'uses' => 'AdminController@smsusername']);
    Route::post('smsuseradd', ['as' => 'smsuseradd', 'uses' => 'AdminController@smsuseradd']);
    Route::post('editsmsuser',['as' => 'editsmsuser', 'uses' => 'AdminController@editsmsuser']);
    Route::get('delete/smsuser/{id}',['as' => 'deletesmsuser', 'uses' => 'AdminController@deletesmsuser']);
    Route::get('exportmobileuser/{id}',['as' => 'exportmobileuser', 'uses' => 'AdminController@exportmobileuser']);
    
});

/*
|----------------------------------------------
| Principal
|----------------------------------------------
*/


// Route::get('/', ['as' => 'login','uses' => 'PrincipalController@login']);

// Route::group(['prefix' => 'api/{type}', 'middleware' => ['api']], function(){

// 	Route::post('free/search', ['uses' => 'UserController@freeSearch']);
// 	Route::get('get/school/{id}', ['uses' => 'UserController@getSchool']);

// 	Route::post('users/login', ['uses' => 'UserController@authenticate']);

// //-------------------------------- Principal Routes------------------------------------------------

// 		Route::group(['middleware' => ['jwt.auth']], function(){
// 			Route::post('/users/logout', ['uses' => 'UserController@logout']);

// 			Route::post('/users/change/password', ['uses' => 'UserController@changePassPost']);

// 			Route::get('/get/all/details', ['uses' => 'UserController@getAllDetails']);
// 			Route::get('/get/school/profile', ['uses' => 'UserController@getSchoolProfile']);

// 			Route::get('/get/user/profile', ['uses' => 'UserController@getUserProfile']);

// 			Route::get('/get/exam/type', ['uses'=>'MasterController@getExamTypes']);
// 		});

// 		Route::group(['middleware' => ['jwt.auth', 'school']], function(){

// 			Route::post('/post/session', ['uses' => 'MasterController@postSession']);
// 			Route::get('/delete/session/{id}', ['uses'=>'MasterController@deleteSession']);
// 			Route::get('/edit/session/{id}', ['uses'=>'MasterController@editSession']);
// 			Route::post('/update/session', ['uses' => 'MasterController@updateSession']);

// 			Route::post('/post/class', ['uses' => 'MasterController@postClass']);
// 			Route::get('/delete/class/{id}', ['uses'=>'MasterController@deleteClass']);
// 			Route::get('/edit/class/{id}', ['uses'=>'MasterController@editClass']);
// 			Route::post('/update/class', ['uses' => 'MasterController@updateClass']);

// 			Route::post('/post/section', ['uses'=>'MasterController@postSection']);
// 			Route::get('/delete/section/{id}', ['uses'=>'MasterController@deleteSection']);
// 			Route::get('/edit/section/{id}', ['uses'=>'MasterController@editSection']);
// 			Route::post('/update/section', ['uses'=>'MasterController@updateSection']);

// 			Route::post('/post/subject', ['uses'=>'MasterController@postSubject']);
// 			Route::get('/delete/subject/{id}', ['uses'=>'MasterController@deleteSubject']);
// 			Route::get('/edit/subject/{id}', ['uses'=>'MasterController@editSubject']);
// 			Route::post('/update/subject', ['uses'=>'MasterController@updateSubject']);

// 			Route::post('/post/exam/type', ['uses'=>'MasterController@postExamType']);
// 			Route::get('/delete/exam/type/{id}', ['uses'=>'MasterController@deleteExamType']);
// 			Route::get('/edit/exam/type/{id}', ['uses'=>'MasterController@editExamType']);
// 			Route::post('/update/exam/type', ['uses'=>'MasterController@updateExamType']);

// 			Route::post('/post/staff/type', ['uses'=>'MasterController@postStaffType']);
// 			Route::get('/delete/staff/type/{id}', ['uses'=>'MasterController@deleteStaffType']);
// 			Route::post('/update/staff/type/{id}', ['uses'=>'MasterController@updateStaffType']);

// 			Route::post('/post/events', ['uses'=>'MasterController@postEvents']);
// 			Route::get('/delete/events/{id}', ['uses'=>'MasterController@deleteEvents']);
// 			Route::get('/edit/events/{id}', ['uses'=>'MasterController@editEvents']);
// 			Route::post('/update/events', ['uses'=>'MasterController@updateEvents']);

// 			Route::post('/post/caste', ['uses'=>'MasterController@postCaste']);
// 			Route::get('/delete/caste/{id}', ['uses'=>'MasterController@deleteCaste']);
// 			Route::get('/edit/caste/{id}', ['uses'=>'MasterController@editCaste']);
// 			Route::post('/update/caste', ['uses'=>'MasterController@updateCaste']);

// 			Route::post('/post/blood/group', ['uses'=>'MasterController@postBloodGroup']);
// 			Route::get('/delete/blood/group/{id}', ['uses'=>'MasterController@deleteBloodGroup']);
// 			Route::get('/edit/blood/group/{id}', ['uses'=>'MasterController@editBloodGroup']);
// 			Route::post('/update/blood/group', ['uses'=>'MasterController@updateBloodGroup']);

// 			Route::post('/post/religion', ['uses'=>'MasterController@postReligion']);
// 			Route::get('/delete/religion/{id}', ['uses'=>'MasterController@deleteReligion']);
// 			Route::get('/edit/religion/{id}', ['uses'=>'MasterController@editReligion']);
// 			Route::post('/update/religion', ['uses'=>'MasterController@updateReligion']);

// 			Route::post('/post/bus', ['uses'=>'MasterController@postBus']);
// 			Route::get('/get/buses', ['uses'=>'MasterController@getBuses']);
// 			Route::get('/delete/bus/{id}', ['uses'=>'MasterController@deleteBus']);
// 			Route::get('/edit/bus/{id}', ['uses'=>'MasterController@editBus']);
// 			Route::post('/update/bus', ['uses'=>'MasterController@updateBus']);

// 			Route::post('/post/bus/stop', ['uses'=>'MasterController@postBusStop']);
// 			Route::get('/get/bus/stops', ['uses'=>'MasterController@getBusStops']);
// 			Route::get('/delete/bus/stop{id}', ['uses'=>'MasterController@deleteBusStop']);
// 			Route::get('/edit/bus/stop/{id}', ['uses'=>'MasterController@editBusStop']);
// 			Route::post('/update/bus/stop', ['uses'=>'MasterController@updateBusStop']);

// 			Route::post('/post/driver', ['uses'=>'MasterController@postDriver']);
// 			Route::get('/get/drivers', ['uses'=>'MasterController@getDrivers']);
// 			Route::get('/delete/driver/{id}', ['uses'=>'MasterController@deleteDriver']);
// 			Route::get('/edit/driver/{id}', ['uses'=>'MasterController@editDriver']);
// 			Route::post('/update/driver', ['uses'=>'MasterController@updateDriver']);

// 			Route::post('/post/holiday', ['uses'=>'MasterController@postHoliday']);
// 			Route::get('/get/holidays', ['uses'=>'MasterController@getHolidays']);
// 			Route::get('/delete/holiday/{id}', ['uses'=>'MasterController@deleteHoliday']);
// 			Route::get('/edit/holiday/{id}', ['uses'=>'MasterController@editHoliday']);
// 			Route::post('/update/holiday', ['uses'=>'MasterController@updateHoliday']);

// 			Route::post('/post/splash', ['uses'=>'PrincipalController@postSplash']);
// 			Route::get('/get/splash', ['uses'=>'PrincipalController@getSplash']);

// 			Route::post('/upload/image', ['uses'=>'PrincipalController@uploadImage']);
// 			Route::post('/post/employee', ['uses'=>'PrincipalController@postEmployee']);
// 			Route::get('/get/employee', ['uses'=>'PrincipalController@getEmployee']);
// 			Route::get('/delete/employee/{id}', ['uses'=>'PrincipalController@deleteEmployee']);
// 			Route::post('/update/employee/{id}', ['uses'=>'PrincipalController@updateEmployee']);
// 			Route::post('/import/employee', ['uses' => 'PrincipalController@importEmployee']);

// 			Route::post('/post/student', ['uses'=>'PrincipalController@postStudent']);
// 			Route::get('/delete/student/{id}', ['uses'=>'PrincipalController@deleteStudent']);
// 			Route::post('/update/student/{id}', ['uses'=>'PrincipalController@updateStudent']);
// 			Route::post('/import/student', ['uses'=>'PrincipalController@importStudent']);

// 		});


// 		Route::group(['middleware' => ['jwt.auth', 'teacher']], function(){

// 			Route::get('get/subjects', ['uses' => 'TeacherController@getSubjects']);
// 			// attendance
// 			Route::post('/post/attendance', ['uses' => 'TeacherController@postAttendance']);
// 			// Homework
// 			Route::post('/post/homework', ['uses' => 'TeacherController@postHomeWork']);
// 			Route::post('/update/homework', ['uses' => 'PrincipalController@updateHomeWork']);

// 			Route::post('/post/feedback', ['uses' => 'PrincipalController@feedBack']);

// 			Route::post('/leave/request', ['uses' => 'PrincipalController@leaveRequest']);

// 			Route::post('/post/gallery', ['uses' => 'PrincipalController@postGallery']);
// 			Route::get('/get/gallery', ['uses' => 'PrincipalController@getGallery']);
// 			Route::get('/get/attendance/{class}/{section}/{date}', ['uses' => 'PrincipalController@getAttendanceInTeacher']);
// 			Route::get('/get/employees/{staffId}', ['uses' => 'PrincipalController@getEmployees']);


// 			Route::post('/post/time/table', ['uses' => 'PrincipalController@postTimeTable']);
// 			Route::get('/get/time/tables/{class}/{section}', ['uses' => 'PrincipalController@getTimeTables']);
// 			Route::get('/delete/time/table/{id}', ['uses' => 'PrincipalController@deleteTimeTable']);
// 			Route::get('/edit/time/table/{id}', ['uses '=> 'PrincipalController@editTimeTable']);
// 			Route::post('/update/time/table', ['uses' => 'PrincipalController@updateTimeTable']);

// 			Route::post('/post/result', ['uses' => 'PrincipalController@postResult']);
// 			Route::get('/get/results/{class}/{section}', ['uses'=>'PrincipalController@getResults']);
// 			Route::get('/delete/result/{id}', ['uses'=>'PrincipalController@deleteResult']);
// 			Route::get('/edit/result/{id}', ['uses'=>'PrincipalController@editResult']);
// 			Route::post('/update/result', ['uses'=>'PrincipalController@updateResult']);
// 		});


// //-----------------------------------------------Teacher Routes ----------------------------------------------

// 		// Common Routes for teacher and principal
// 		Route::group(['middleware' => ['jwt.auth', 'ManyRoles:school|teacher']], function(){
// 			Route::get('/get/sessions', ['uses' => 'MasterController@getSessions']);
// 			Route::get('/get/classes', ['uses' => 'MasterController@getClasses']);
// 			Route::get('/get/sections/{class}', ['uses'=>'MasterController@getSections']);
// 			Route::get('/get/subjects/{section}', ['uses'=>'MasterController@getSubjects']);
// 			Route::get('/get/staff', ['uses'=>'MasterController@getStaffTypes']);
// 			Route::get('/get/events', ['uses'=>'MasterController@getEvents']);
// 			Route::get('/get/castes', ['uses'=>'MasterController@getCastes']);
// 			Route::get('/get/blood/groups', ['uses'=>'MasterController@getBloodGroups']);
// 			Route::get('/get/religions', ['uses'=>'MasterController@getReligions']);
// 			Route::get('/get/months', ['uses'=>'MasterController@getMonths']);
// 			Route::get('/get/attendance', ['uses' => 'PrincipalController@getAttendance']);
// 			Route::get('/get/student/{class}/{section}', ['uses'=>'PrincipalController@getStudentSection']);
// 			Route::get('/get/fee/structure/{class}', ['uses'=>'PrincipalController@getFeeStructure']);
// 			Route::get('/get/leave/request/{class}/{section}/{month}', ['uses'=>'PrincipalController@getLeaveRequest']);
// 			Route::post('/update/leave/request', ['uses'=>'PrincipalController@updateLeaveRequest']);
// 		});

// //---------------------------------------------Students Routes--------------------------------------------------

// 		Route::group(['prefix'=>'student', 'middleware' => ['jwt.auth', 'student']], function(){
// 			Route::get('/get/homework/{id}/{date}', ['uses'=>'StudentController@getHomeworkByStudent']);
// 			Route::get('/get/attendance', ['uses'=>'StudentController@getAttendanceByStudent']);
// 			Route::get('/get/attendance/{month}', ['uses'=>'StudentController@getAttendanceByMonth']);
// 			Route::get('/get/feedback', ['uses'=>'StudentController@getFeedbackByStudent']);
// 			Route::get('/get/attendance/{id}/{date}', ['uses'=>'StudentController@getAttendanceByDate']);
// 			Route::get('/get/timetable', ['uses'=>'StudentController@getTimeTableByStudent']);
// 		});


// 		Route::group(['prefix'=>'parent', 'middleware' => ['jwt.auth', 'parent']], function(){
// 			Route::get('/get/students', ['uses'=>'ParentController@getStudentsByParent']);
// 			Route::post('/leave/request', ['uses'=>'ParentController@postLeaveByParent']);
// 			Route::get('/get/homework/{id}/{date}', ['uses'=>'ParentController@getHomeworkByParent']);
// 			Route::get('/get/attendance/{id}/{date}', ['uses'=>'ParentController@getAttendanceByParent']);
// 			Route::get('/get/feedback/{id}', ['uses'=>'ParentController@getFeedbackByParent']);
// 			Route::get('/get/timetable/{id}', ['uses'=>'ParentController@getTimeTableByParent']);
// 		});
// });


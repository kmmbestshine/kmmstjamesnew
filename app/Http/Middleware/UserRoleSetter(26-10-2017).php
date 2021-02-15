<?php namespace App\Http\Middleware;

use Closure;

class UserRoleSetter {

	private $permissions = [

			'user.dashboard' => '0',

			//student
			'master.student' => '1',
			'post.student' => '1',
			'get.students' => '1',
			'view.student' => '1',
			'edit.student' => '1',
			'update.student' => '1',
			'delete.student' => '1',
			'import.student' => '1',
			'post.stop.routes' => '0',
			'user.managerExport' => '1',
			'export.session' => '1',
			'export.session.view' => '1',
			'export.class' => '1',
			'export.class.view' => '1',
			'export.section' => '1',
			'export.section.view' => '1',
			'export.subject' => '1',
			'export.subject.view' => '1',
			'export.exam' => '1',
			'export.exam.view' => '1',
			'export.staff' => '1',
			'export.staff.view' => '1',
			'export.events' => '1',
			'export.caste' => '1',
			'export.caste.view' => '1',
			'export.religion' => '1',
			'export.religion.view' => '1',
			'export.bus' => '1',
			'export.bus.view' => '1',

			//employee
			'insert.employee' => '2',
			'post.employee' => '2',
			'get.employee' => '2',
			'edit.employee' => '2',
			'delete.employee' => '2',
			'update.employee' => '2',
			'export.employee' => '2',
			'export.staff' => '2',
			'export.staff.view' => '2',
			'user.managerExport' => '2',
			'asign.usersRole' => '2',//updated
			'user.role.post' => '2',//updated
			'changePassword' => '2',//updated
			'postPassword' => '2',//updated
			'user.usersRole' => '2',//updated
			'deleteUserRole' => '2',//updated


			//homework
			'user.homework' => '3',
			'fetch.subjects' => '0',
			'get.section' => '0',
			'post.homework' => '3',
//			'get.homework' => '1',
            'get.homework' => '3',//updated
			'deleteHomework' => '3',//updated
			
			'update.homework' => '3',

			//attendance
			'user.attendance' => '4',
			'post.cred' => '4',
			'user.attendata' => '4',
			'post.attendance' => '4',
			'view.attendance' => '4',

			//timetable
			'user.timeTable' => '5',
			'get.timeTable' => '5',
			'post.timeTable' => '5',
            'deleteTimetable' => '5',/* updated 22-9-2017 by priya */
             'user.examTimeTable' => '5',//updated 27-9-2017
			 'user.add_exam_timetable' => '5',//updated 27-9-2017
			 'user.post_exam_timetable' => '5',//updated 27-9-2017
			 'deleteExamTimetable' => '5',//updated 27-9-2017


			//gallery
			'user.gallery' => '6',
			'get.gallery' => '6',
			'post.gallery' => '6',
			'fields.gallery' => '6',
			'delete.gallery' => '6',
			'edit.gallery' => '6',
			'update.gallery' => '6',

			//post 
			'user.post' => '7',
			'post.post' => '7',

			//result
			'user.result' => '8',
			'result.cred' => '8',
			'get.result' => '8',
			'post.result' => '8',
			'view.result' => '8',

			//notification
			'user.notification' => '9',
			'post.device.notification' => '9',
			'view.notification' => '9',
			'deleteNotificationHistory' => '9',//updated



			//notice
			'user.notice' => '10',
			'post.notice' => '10',
			'get.notice' => '10',
			'delete.notice' => '10',

			//fee
			//'fee.frequency' => '11',
			//'post.frequency' => '11',
			//'delete.frequency' => '11',
			//'edit.frequency' => '11',
			//'update.frequency' => '11',
			'fee.structure' => '11',
			'list.structure' => '11',
			'post.structure' => '11',
			'delete.structure' => '11',
			'edit.structure' => '11',
			'update.structure' => '11',
			'user.feeCollection' => '11',
			'post.fee' => '11',
			'view.fee' => '11',
			'post fee structure' => '11',//updated
			'fee.structure.class' => '11',//updated
			'fee.structure.delete' => '11',//updated
			'single student payment' => '11',//updated
			'fee.collection.post' => '11',//updated

 


			//library 
			'user.library' => '12',
			'post.library' => '12',
			'user.issue.book' => '12',
			'issue.book.post' => '12',
			'get.stduent.library' => '12',
			'return.book' => '12',
			'return.book.post' => '12',
			'book.info' => '12',
			'user.bookCategory' => '12',//updated
			'post.category' => '12',//updated
			'deleteCategory' => '12',//updated
			'get.book.library' => '12',
			'get.teacher.library' => '12',//updated 20-10-2017 by priya
			'get.stduent.library.detail' => '12',//updated 20-10-2017 by priya
			'get.teacher.library.detail' => '12',//updated 20-10-2017 by priya
			'get.student.section' => '12',//updated 20-10-2017 by priya

			//data manager
			'user.managerData' => '13',
			'post.manager' => '13',

			//master
			'master.session' => '14',
			'post.session' => '14',
			'delete.session' => '14',
			'edit.session' => '14',
			'update.session' => '14',
			'operate.session' => '14',
			'master.class' => '14',
			'post.class' => '14',
			'delete.class' => '14',
			'edit.class' => '14',
			'update.class' => '14',
			'master.section' => '14',
			'post.section' => '14',
			'edit.section' => '14',
			'delete.section' => '14',
			'update.section' => '14',
			'master.subject' => '14',
			'post.subject' => '14',
			'edit.subject' => '14',
			'delete.subject' => '14',
			'update.subject' => '14',
			'master.exam' => '14',
			'post.exam' => '14',
			'edit.exam' => '14',
			'delete.exam' => '14',
			'update.exam' => '14',
			'master.staff' => '14',
			'post.staff' => '14',
			'edit.staff' => '14',
			'delete.staff' => '14',
			'update.staff' => '14',
			'master.events' => '14',
			'post.events' => '14',
			'edit.events' => '14',
			'delete.events' => '14',
			'update.events' => '14',
			'export.events.view' => '14',//Updated
			'master.caste' => '14',
			'post.caste' => '14',
			'edit.caste' => '14',
			'delete.caste' => '14',
			'update.caste' => '14',
			'exam.grade' => '14',
			'post.exam.gradesave' => '14',
			'get.exam.grade' => '14', 
			'master.grade' => '14',
			'post.grade' => '14',
			'edit.grade' => '14',
			'delete.grade' => '14',
			'update.grade' => '14',
			'master.religion' => '14',
			'post.religion' => '14',
			'edit.religion' => '14',
			'delete.religion' => '14',
			'update.religion' => '14',
			'master.holiday' => '14',
			'post.holiday' => '14',
			'edit.holiday' => '14',
			'delete.holiday' => '14',
			'update.holiday' => '14',
			'master.salary' => '14',
			'post.salary' => '14',
			'edit.salary' => '14',
			'delete.salary' => '14',
			'update.salary' => '14',
			'class.deposit' => '14',
			'post.deposit' => '14',
			'edit.deposit' => '14',
			'delete.deposit' => '14',
			'update.deposit' => '14',
			'master.notification' => '14',
			'post.notification' => '14',
			'edit.notification' => '14',
			'delete.notification' => '14',
			'update.notification' => '14',
             'masterView' => '14',/* Updated 22-9-2017 by priya */

             //transport
             'master.bus' => '15',
			'post.bus' => '15',
			'edit.bus' => '15',
			'delete.bus' => '15',
			'update.bus' => '15',
			'master.stop' => '15',
			'post.stop' => '15',
			'edit.stop' => '15',
			'delete.stop' => '15',
			'update.stop' => '15',
			'master.driver' => '15',
			'post.driver' => '15',
			'edit.driver' => '15',
			'delete.driver' => '15',
			'update.driver' => '15',
			'user.trasport' => '15',//Updated
			
			//Knowledge
			'knowledgeBank' => '16',
			'postKnowledge' => '16',
			'viewKnowledge' => '16',
			'deleteQuestion' => '16',

			//report
			'user.report' => '18',
			'attendanceReport' => '18',
			'studentsReport' =>'18',
			'libraryReport' =>'18',//updated 20-10-2017 by priya
            'analystReport' => '18',//updated 20-10-2017 by priya
            'teacherReport' => '18',//updated 20-10-2017 by priya
            'reportDownload' => '18',//updated 20-10-2017 by priya
			'feeCollectionReport' => '18'


	];


	public function handle($request, Closure $next)
	{	
		if(\Auth::user()->type == 'user_role')
		{
			$permi = \App\UserRole::where('role_id', \Auth::user()->id)->select('permission_id')->get();
			$myArr = [];
			foreach($permi as $per)
			{
				array_push($myArr, $per->permission_id);
			}

			$rules= $this->permissions[$request->route()->getName()];

			if($rules == '0')
			{
				return $next($request);
			}
			else
			{
				if(!in_array($rules, $myArr))
					dd('no');	
			}
		}

		if(\Auth::user()->type == 'teacher')
		{
			$myArr = ["3","4","5","6","8"];

			$rules= $this->permissions[$request->route()->getName()];

			if($rules == '0')
			{
				return $next($request);
			}
			else
			{
				if(!in_array($rules, $myArr))
					dd('no');	
			}
		}
		return $next($request);
	}
}	
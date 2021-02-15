<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    protected $table = 'library';

    public function doPostLibrary($input, $user)
    {
        $check = Library::where('school_id', $user->school_id)->where('book_no', $input['book_no'])->first();
        if($check)
        {
            $input['error'] = 'Book No already exists';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            Library::insert([
                'school_id' => $user->school_id,
                'book_no' => $input['book_no'],
                'subject_id' => $input['subject_id'],
                'price' => $input['price'],
                'book_name'=>$input['book_name'],
                'book_category'=>$input['category'],
                'auth_name'=>$input['auth_name'],
                'publisher_name'=>$input['pub_name'],
                'publish_year'=>$input['pub_date'],
                'purchase_date'=>$input['pdate'],
                'vendor_name'=>$input['vendor_name'],
                'no_of_books'=>$input['no_of_books'],//updated 28-9-2017 by priya
                'available'=>0

            ]);
            $input['success'] = 'Book is added successfully';
            return \Redirect::back()->withInput($input);
        }
    }
    public function doUpdateLibrary($input, $user)
    {
       // dd($input,'kkk');
      //  $check = Library::where('school_id', $user->school_id)->where('book_no', $input['book_no'])->first();
      //  if($check){
             
           // dd('nes');
               //  $input['error'] = 'Book No already exists';
             //   return \Redirect::back()->withInput($input);
            
       // } 
        //else
       // {
            
          // $updat = Library::where('school_id', $user->school_id)->where('book_no', $input['book_no'])->first();

           if($input['subject_id']){
                $new_subject_id=$input['subject_id'];

           }else{
                $new_subject_id=$input['oldsubject_id'];
           }
           if($input['category']){
                $new_category_id=$input['category'];

           }else{
                $new_category_id=$input['oldcategory_id'];
           }
       //dd('no',$input,$new_subject_id,$new_category_id);
            Library::where('book_no', $input['oldbook_no'])->update([
                'school_id' => $user->school_id,
                'book_no' => $input['book_no'],
                'subject_id' => $new_subject_id,
                'price' => $input['price'],
                'book_name'=>$input['book_name'],
                'book_category'=>$new_category_id,
                'auth_name'=>$input['auth_name'],
                'publisher_name'=>$input['pub_name'],
                'publish_year'=>$input['pub_date'],
                'purchase_date'=>$input['pdate'],
                'no_of_books'=>$input['no_of_books'],//updated 28-9-2017 by priya
                'vendor_name'=>$input['vendor_name'],
               // 'available'=>0

            ]);
            $input['success'] = 'Book is updated successfully';

        $books = \DB::table('library')->where('school_id', \Auth::user()->school_id)->where('book_category', $input['book_category'])->get();
        $categories = \DB::table('book_category')->where('school_id', \Auth::user()->school_id)->get();
        return view('users.library.booklist', compact('categories','books','input'));
       
      //  }
       
            
       
    }

    public function doIssueBookPostTeacher($request, $user)
    {
        $check = Library::where('book_no', $request['book_no'])
            ->where('school_id', $user->school_id)
            ->where('available', 0)->first();
        if(!$check)
        {
            $input['error'] = 'Book is not available';
            return \Redirect::back()->withInput($input);
        }

        $teacherCheck = \DB::table('users')->where('username', $request['user_name'])
            ->where('school_id', $user->school_id)->first();
        //dd($teacherCheck);
        if(!$teacherCheck)
        {
            $input['error'] = 'Registration No is invalid';
            return \Redirect::back()->withInput($input);
        }
        $limit = \DB::table('issue')->where('teacher_name', $teacherCheck->username)
            ->where('return_flag', 0)->count();
        if($limit>4)
        {
            $input['error'] = 'Book Issue Limit Reached';
            return \Redirect::back()->withInput($input);
        }
        $returndate=strtotime($request['return_date']);
        $curdate=strtotime(date('y-m-d'));
        if($returndate < $curdate)
        {
            $input['error'] = 'Return date is always greater than issue date';
            return \Redirect::back()->withInput($input);
        }
        $id = \DB::table('issue')->insertGetId([
            'book_id' => $check->id,
            'school_id'=>$user->school_id,
            'teacher_name' => $request['user_name'],
            'type' => 'staff',
            'issue_date' => date('d-m-Y'),
            'return_date' => date('d-m-Y', strtotime($request['return_date']))
        ]);
        /*************  Updated 28-9-2017 by priya  *************/
        /*if($id)
        {
           // Library::where('id', $check->id)->update(['available' => 1]);
            Library::where('id', $check->id)->update(['available' => 1]);
        }
        $input['success'] = 'Book issued successfully';
        return \Redirect::back()->withInput($input);*/
        if($id)
        {
            $getNoOfBooks = Library::where('book_no', $request['book_no'])
                ->where('school_id', $user->school_id)->first();
            $noOfBooks = $getNoOfBooks->no_of_books;
            /* 6-10-2017 by priya*/
            $availability = \DB::table('issue')->where('school_id',\Auth::user()->school_id)->where('book_id', $getNoOfBooks->id)
                ->where('return_flag', 0)->count();
            if($availability  < $noOfBooks)
            {
                Library::where('id',$getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available'=> 0,'issued_books' => $availability]);
            }
            else
            {
                Library::where('id', $getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available' => 1,'issued_books' => $availability]);
            }
            /* end */
            $input['success'] = 'Book issued successfully';
        }
        else
        {
            $input['error'] = 'Book not issued successfully';
        }
        /**********  End **********/
        return \Redirect::back()->withInput($input);
    }
    public function doIssueBookPost($request, $user)
    {
        $check = Library::where('book_no', $request['book_no'])
            ->where('school_id', $user->school_id)
            ->where('available', 0)->first();

        if(!$check)
        {
            $input['error'] = 'Book is not available';
            return \Redirect::back()->withInput($input);
        }
        //if($request['user_role']=='Students'){
        $studCheck = Students::where('registration_no', $request['registration_no'])
            ->where('school_id', $user->school_id)->first();
        if(!$studCheck)
        {
            $input['error'] = 'Registration No is invalid';
            return \Redirect::back()->withInput($input);
        }
        $limit = \DB::table('issue')->where('student_id', $studCheck->id)
            ->where('return_flag', 0)->count();
        if($limit>4)
        {
            $input['error'] = 'Book Issue Limit Reached';
            return \Redirect::back()->withInput($input);
        }
        $returndate=strtotime($request['return_date']);
        $curdate=strtotime(date('y-m-d'));
        if($returndate < $curdate)
        {
            $input['error'] = 'Return date is always greater than issue date';
            return \Redirect::back()->withInput($input);
        }

        $id = \DB::table('issue')->insertGetId([
            'book_id' => $check->id,
            'student_id' => $studCheck->id,
            'teacher_name' => $request['registration_no'],
            'type' => 'student',
            'school_id'=>$user->school_id,
            'issue_date' => date('d-m-Y'),
            'return_date' => date('d-m-Y', strtotime($request['return_date']))
        ]);

        /*if($id)
        {
            Library::where('id', $check->id)->update(['available' => 1]);
        }
        $input['success'] = 'Book issued successfully';
        return \Redirect::back()->withInput($input);
        */
        if($id)
        {
            $getNoOfBooks = Library::where('book_no', $request['book_no'])
                ->where('school_id', $user->school_id)->first();
            $noOfBooks = $getNoOfBooks->no_of_books;
            $availability = \DB::table('issue')->where('school_id',\Auth::user()->school_id)->where('book_id', $check->id)
                ->where('return_flag', 0)->count();
            if($availability  < $noOfBooks)
            {
                Library::where('id',$getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available'=> 0,'issued_books' => $availability]);
            }
            else
            {
                Library::where('id', $getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available' => 1,'issued_books' => $availability]);
            }
            $input['success'] = 'Book issued successfully';
        }
        else
        {
            $input['error'] = 'Book not issued successfully';
        }

        return \Redirect::back()->withInput($input);
    }


    public function doReturnBookPost($request, $user)
    {
        $getBook = Library::where('book_no', $request['book_no'])
            ->where('school_id',\Auth::user()->school_id)->first();
        if(!$getBook)
        {
            $input['error'] = 'Book No is invalid';
            return \Redirect::back()->withInput($input);
        }

        /* $check = Library::where('book_no', $request['book_no'])
                     ->where('school_id',\Auth::user()->school_id)
                     ->where('available', 0)->first();*/
        $check = \DB::table('issue')->where('school_id',\Auth::user()->school_id)->where('book_id', $getBook->id)
            ->where('return_flag',0)//14-10-2017 by priya
            ->first();
        if(!$check)
        {

            $input['error'] = "Book is not issued to any student, so it can't be reissued";
            return \Redirect::back()->withInput($input);
        }


        if($request['bookrel'] == 'submit')
        {
            //return $request['user_role'];exit;
            if($request['user_role'] == 'Student')
            {
                //return 'Student book return';exit;
                $stud_no = \DB::table('student')->where('school_id', $user->school_id)
                    ->where('registration_no',$request['registration_no'])->first();
                // dd($stud_no);
                $getReissueDetails = \DB::table('issue')->where('book_id', $getBook->id)
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('student_id',$stud_no->id)
                    ->where('return_flag',0)
                    ->update(['return_flag' => 1, 'fine' => $request['fine']]);

            }
            else
            {
                // return 'teacher book return';exit;
                $teach_user_name = \DB::table('users')->where('school_id', $user->school_id)
                    ->where('username',$request['user_name'])->first();
                //$teach_no = \DB::table('teacher')->where('user_id',$teach_user_name->id)->first();
                $getReissueDetails = \DB::table('issue')->where('book_id', $getBook->id)
                    ->where('school_id',\Auth::user()->school_id)
                    ->where('teacher_name',$teach_user_name->id)
                    ->where('return_flag',0)
                    ->update(['return_flag' => 1, 'fine' => $request['fine']]);
            }
            // dd($getReissueDetails);
            if($getReissueDetails)
            {
                $getNoOfBooks = Library::where('book_no', $request['book_no'])
                    ->where('school_id', $user->school_id)->first();
                $noOfBooks = $getNoOfBooks->no_of_books;
                $availability = \DB::table('issue')->where('school_id',\Auth::user()->school_id)->where('book_id', $getNoOfBooks->id)
                    ->where('return_flag',0)->count();
                if($availability  < $noOfBooks)
                {
                    //dd($availability);
                    // return 'book available';exit;
                    Library::where('id',$getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available'=> 0,'issued_books' => $availability]);
                    //dd($stud);exit;
                }
                else
                {
                    //return 'book not available';exit;
                    Library::where('id', $getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available' => 1,'issued_books' => $availability]);
                }
                $input['success'] = 'Book returned successfully';
            }
            else
            {
                $input['error'] = 'Book is not Returned';
            }

            /* Library::where('id', $getBook->id)
                     ->where('school_id',\Auth::user()->school_id)
                     ->update(['available' => 0]);
             \DB::table('issue')->where('book_id', $getBook->id)
                     ->update(['return_flag' => 1, 'fine' => $request['fine']]);*/

            return \Redirect::back()->withInput($input);
        }

        if($request['bookrel'] == 'reissue')
        {
            if($request['return_date'] == '')
            {
                $input['error'] = 'Return Date is required';
                return \Redirect::back()->withInput($input);
            }
            $returndate=strtotime($request['return_date']);
            $curdate=strtotime(date('y-m-d'));
            if($returndate < $curdate)
            {
                $input['error'] = 'Return date is always greater than issue date';
                return \Redirect::back()->withInput($input);
            }

            /* \DB::table('issue')->where('book_id', $getBook->id)
                     ->update(['return_flag' => 1, 'fine' => $request['fine']]);*/

            /*if($request['user_role'] == 'Student')
            {
                //return 'student reissue';exit;
                $stud_no = \DB::table('student')->where('registration_no',$request['registration_no'])->first();
                $getReissueDetails = \DB::table('issue')->where('book_id', $getBook->id)
                                    ->where('school_id',\Auth::user()->school_id)
                                    ->where('student_id',$stud_no->id)
                                    ->update(['return_flag' => 0, 'fine' => $request['fine']]);
            }
            else
            {
                //return 'teacher reissue';exit;
                $teach_user_name = \DB::table('users')->where('username',$request['user_name'])->first();
                //$teach_no = \DB::table('teacher')->where('user_id',$teach_user_name->id)->first();
                $getReissueDetails = \DB::table('issue')->where('book_id', $getBook->id)
                                    ->where('school_id',\Auth::user()->school_id)
                                    ->where('teacher_name',$teach_user_name->id)
                                    ->update(['return_flag' => 0, 'fine' => $request['fine']]);
            }*/
            $getIssue = \DB::table('issue')->where('book_id', $getBook->id)
                ->where('school_id',\Auth::user()->school_id)
                ->where('return_flag',0)
                // ->first();
                ->get();//21-10-2017 by priya
            if(!$getIssue)
            {
                $input['error'] = 'Unknown Error';
            }
            else
            {
                /*
                 * updated 21-10-2017 by priya
                 *if($getIssue->student_id != 0)
                {
                    //return $getIssue->student_id;exit;
                    if($request['user_role'] == 'Student')
                    {
                        //return 'student';exit;
                        //14-10-2017 by priya
                        $stud_no = \DB::table('student') ->where('school_id',\Auth::user()->school_id)->where('registration_no',$request['registration_no'])->first();
                        $id = \DB::table('issue')
                            ->where('book_id', $getBook->id)
                           // ->where('school_id',\Auth::user()->school_id)
                            ->where('student_id',$stud_no->id)
                            ->where('return_flag',0)
                            ->update([
                                /* 'book_id' => $getIssue->book_id,
                                 'student_id' => $getIssue->student_id,*/
                                /*'issue_date' => date('d-m-Y'),
                                'return_date' => date('d-m-Y', strtotime($request['return_date']))
                                 ]);
                     }

                }*/

                if($request['user_role'] == 'Student')
                {
                    //return 'student';exit;
                    $stud_no = \DB::table('student') ->where('school_id',\Auth::user()->school_id)->where('registration_no',$request['registration_no'])->first();
                    $id = \DB::table('issue')
                        ->where('book_id', $getBook->id)
                        // ->where('school_id',\Auth::user()->school_id)
                        ->where('student_id',$stud_no->id)
                        ->where('return_flag',0)
                        ->update([
                            /* 'book_id' => $getIssue->book_id,
                             'student_id' => $getIssue->student_id,*/
                            'issue_date' => date('d-m-Y'),
                            'return_date' => date('d-m-Y', strtotime($request['return_date']))
                        ]);
                }

                /***** end *****/

                else
                {
                    //return'teacher';exit;
                    $teach_user_name = \DB::table('users')->where('school_id',\Auth::user()->school_id)->where('username',$request['user_name'])->first();
                    $id = \DB::table('issue')
                        ->where('book_id', $getBook->id)
                        ->where('school_id',\Auth::user()->school_id)
                        ->where('teacher_name',$teach_user_name->id)
                        ->where('return_flag',0)
                        ->update([
                            /*'book_id' => $getIssue->book_id,
                            'teacher_name' => $getIssue->teacher_name,*/
                            'issue_date' => date('d-m-Y'),
                            'return_date' => date('d-m-Y', strtotime($request['return_date']))
                        ]);
                }
                /* if($id)
                 {
                     Library::where('id', $getIssue->book_id)->update(['available' => 1]);
                 }*/
                if($id)
                {
                    // return 'success';exit;
                    $getNoOfBooks = Library::where('book_no', $request['book_no'])
                        ->where('school_id', $user->school_id)->first();
                    $noOfBooks = $getNoOfBooks->no_of_books;
                    $availability = \DB::table('issue')->where('school_id',\Auth::user()->school_id)->where('book_id', $getNoOfBooks->id)
                        ->where('return_flag', 0)->count();
                    if($availability  < $noOfBooks)
                    {
                        //return 'book available';exit;
                        Library::where('id',$getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available'=> 0,'issued_books' => $availability]);
                    }
                    else
                    {
                        //return 'book not available';exit;
                        Library::where('id', $getNoOfBooks->id)->where('school_id',\Auth::user()->school_id)->update(['available' => 1,'issued_books' => $availability]);
                    }
                    $input['success'] = 'Book reissued successfully';
                }
                else
                {
                    // return 'error';exit;
                    $input['error'] = 'Book not reissued successfully';
                }
                //$input['success'] = 'Book reissued successfully';
                return \Redirect::back()->withInput($input);
            }
        }
    }


}
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Amount extends Model
{
    protected $table = 'amount';

    public function doPostDeposit($request, $user)
    {
        $amount = Amount::where('class_id', $request['class'])->where('school_id', $user->school_id)->first();
        if($amount)
        {
            $input['error'] = 'Amount already exists for this class';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            Amount::insert([
                'school_id' => $user->school_id,
                'class_id' => $request['class'],
                'amount' => $request['amount']
            ]);
            $input['success'] = 'Class Amount is added successfully';
            return \Redirect::back()->withInput($input);
        }
    }

    public function doUpdateDeposit($request, $user)
    {
        $amount = Amount::where('class_id', $request['class'])->where('school_id', $user->school_id)->where('id', '!=', $request['id'])->first();
        if($amount)
        {
            $input['error'] = 'Amount already exists for this class';
            return \Redirect::back()->withInput($input);
        }
        else
        {
            Amount::where('id', $request['id'])->update([
                'school_id' => $user->school_id,
                'class_id' => $request['class'],
                'amount' => $request['amount']
            ]);
            $input['success'] = 'Class Amount is updated successfully';
            return \Redirect::route('class.deposit')->withInput($input);
        }
    }

}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $table = 'fee_structure';

    public function doPostStructure($input, $user)
    {
    	foreach($input['structure'] as $struct)
    	{
    		$check = FeeStructure::where('school_id', $user->school_id)->where('structure', $struct)->first();
    		if($check)
    		{
    			FeeStructure::where('id', $check->id)->update(['structure' => $struct]);
    		}
    		else
    		{
    			FeeStructure::insert(['school_id' => $user->school_id, 'structure' => $struct]);
    		}
    	}
    	$input['success'] = 'Fee Structure is added successfully';
    	return \Redirect::route('list.structure')->withInput($input);
    }

    public function doUpdateStructure($input, $user)
    {
        $check = FeeStructure::where('school_id', $user->school_id)->where('structure', $input['structure'])->where('id', '!=', $input['id'])->first();
        if($check)
        {
            $input['error'] = 'Fee Structure already exists';
            return \Redirect::route('list.structure')->withInput($input);
        }
        else
        {
            FeeStructure::where('id', $input['id'])->update(['school_id' => $user->school_id, 'structure' => $input['structure']]);
            $input['success'] = 'Fee Structure is updated successfully';
            return \Redirect::route('list.structure')->withInput($input);
        }
    }
}
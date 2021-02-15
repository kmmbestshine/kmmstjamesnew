<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class SaveDistribute extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        //$input=$this->request->all();
        //dd($input);
        return [
            'category'=>'required',
            'subcategory'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'Itemname'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'quantity'=>'required|numeric',
            'comment'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'rate'=>'required|numeric',
            'class_id'=>'required',
            'section_id'=>'required',
            'Student_id'=>'required',
        ];
    }
}

<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ExpenditureCreate extends Request
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
        return [
            'expdate'=>'required|date|date_format:Y-m-d',
            'toname'=>'required|regex:/^[a-zA-Z_ ]+$/',
            'purpose'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'category'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            //'Description'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            //'quantity'=>'required|numeric',
            //'comment'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'amount'=>'required|numeric',
            'approvedby'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'givenby'=>'required|regex:/^[a-zA-Z0-9_ ]+$/'
        ];
    }
}

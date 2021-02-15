<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class AddFurniture extends Request
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
            'type'=>'required',
            'category'=>'required',
            'subcategory'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'Itemname'=>'required|regex:/^[a-zA-Z0-9_ ]+$/',
            'quantity'=>'required|numeric',
            'remarks'=>'regex:/^[a-zA-Z0-9_ ]+$/',
            'purchaserate'=>'required|numeric',
            'distributionrate'=>'numeric',
        ];
    }
}

<?php

namespace App;
use Redirect, api, DB, Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;

class Installment extends Model
{
    protected $table = 'installments';

    public $timestamps = true;
}

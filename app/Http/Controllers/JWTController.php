<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class JWTController extends Controller
{	
	protected $user;
	
	function __construct(){
		try{
			$this->user= JWTAuth::parseToken()->authenticate();
		}
		catch(\Exception$e){}
	}


}
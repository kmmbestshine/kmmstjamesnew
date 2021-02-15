<?php

Route::get('/', ['as'=>'/', 'uses'=>'FrontController@home']);

Route::get('about', ['as'=>'about', 'uses'=>'FrontController@about']);
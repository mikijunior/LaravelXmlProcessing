<?php

use Illuminate\Http\Request;

Route::group(['namespace' => 'Api'], function () {
    Route::post('send-file', 'FileController@storeFileData');
});

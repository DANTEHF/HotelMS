<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('bookroom', 'HotelmanagesystemController@showBookRoomPage');
//预定房间路由
Route::post('bookroom','HotelmanagesystemController@bookroomin');

Route::get('addroom','HotelmanagesystemController@showRoomin');
//添加房间路由
Route::post('addroom','HotelmanagesystemController@roomsave');
//获得房间信息路由
Route::get('getroom','HotelmanagesystemController@checkRoom');
//获得房间状态路由
Route::get('getroomstatus','HotelmanagesystemController@getRoomStatus');
//根据电话获得预定信息路由
Route::post('getbookms','HotelmanagesystemController@checkbook');
//办理客人入住路由
Route::post('addguestinms','HotelmanagesystemController@addguestin');

//查询订单信息路由
Route::get('getroomguest','HotelmanagesystemController@allroomguest');
//修改客房信息
Route::post('alterroomms','HotelmanagesystemController@changeRoomMes');
//修改订单信息
Route::post('alterroomguest','HotelmanagesystemController@alterroomguest');

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(['middleware' => ['web']], function () {
    //
});

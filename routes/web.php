<?php

/*
  |--------------------------------------------------------------------------
  | Web Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register web routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | contains the "web" middleware group. Now create something great!
  |
 */

/**
 * 官网
 */
Route::get('/', 'HomeController@index');

/**
 * 回调路由
 */
Route::group(['namespace' => 'Inner'], function () {
    /**
     * 有盾回调接口
     */
    Route::match(['post'], '/udcredit/notify', 'UdcreditController@notify');
});


/**
 * APP v1版本路由
 */
define('APP_V1', '/app/v1');

Route::group(['namespace' => 'App\V1'], function () {
    // Controllers Within The "App\Http\Controllers\APP\V1" Namespace
    /**
     * 首页
     */
    Route::match(['get', 'post'], APP_V1 . '/index/index', 'IndexController@index');

    /**
     * 登录接口
     */
    Route::match(['get', 'post'], APP_V1 . '/login/login', 'LoginController@login');
    /**
     * 获取短信验证码
     */
    Route::match(['get', 'post'], APP_V1 . '/login/verifycode', 'LoginController@verifycode');

    /**
     * 获取用户资料配置
     */
    Route::match(['get', 'post'], APP_V1 . '/user/uconfig', 'UserController@uconfig');

    /**
     * 地址选择器
     */
    Route::match(['get', 'post'], APP_V1 . '/area/province', 'AreaController@province');
    Route::match(['get', 'post'], APP_V1 . '/area/city', 'AreaController@city');
    Route::match(['get', 'post'], APP_V1 . '/area/area', 'AreaController@area');

    /**
     * 搜索机构
     */
    Route::match(['get', 'post'], APP_V1 . '/search/school', 'SearchController@school');

    /**
     * 银行卡相关
     */
    Route::match(['get', 'post'], APP_V1 . '/bank/sms', 'BankController@sms');      //获取签约短息
    Route::match(['get', 'post'], APP_V1 . '/bank/bind', 'BankController@bind');    //签约
    Route::match(['get', 'post'], APP_V1 . '/bank/banks', 'BankController@banks');  //拉取用户银行卡列表
    Route::match(['get', 'post'], APP_V1 . '/bank/change', 'BankController@change');  //切换用户划扣卡
});

Route::match(['get', 'post'], '/pic/{cate?}/{img?}', function($cate = '', $img = '') {
    $file = PATH_BASE . '/public/img/' . $cate . '/' . $img;
    if (!is_file($file)) {
        header("HTTP/1.1 404 Not Found");
        header("Status: 404 Not Found");
        exit;
    }
    header('Content-type: image/jpg');
    echo file_get_contents($file);
    exit;
});

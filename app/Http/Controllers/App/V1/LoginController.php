<?php

/**
 * Created by PhpStorm.
 * User: wangyi
 * Date: 2018/12/6
 * Time: 3:38 PM
 */

namespace App\Http\Controllers\App\V1;

use App\Components\CheckUtil;
use App\Components\CookieUtil;
use App\Components\OutputUtil;
use App\Components\PFException;
use App\Http\Controllers\App\AppController;
use App\Models\ActiveRecord\ARPfUsers;
use App\Models\DataBus;
use App\Models\Server\VerifyCode;
use Illuminate\Support\Facades\Input;

class LoginController extends AppController {

    public function login() {
        try {
            $phone = Input::get('phone');
            $vcode = Input::get('vcode');
            $password = Input::get('password');
            $ip = DataBus::get('ip');
            if ($vcode && $ip) {
                if (!VerifyCode::checkVerifyCode($phone, $ip, $vcode)) {
                    throw new PFException(ERR_VCODE_CHECK_CONTENT, ERR_VCODE_CHECK);
                }
                $userInfo = ARPfUsers::getUserInfoByPhone($phone);
                if (empty($userInfo)) {
                    $info = [
                        'phone' => $phone,
                        'username' => substr($phone, 0, 3) . '****' . substr($phone, -4, 4)
                    ];
                    $result = ARPfUsers::addUserInfo($info);
                    if ($result) {
                        $userInfo = ARPfUsers::getUserInfoByPhone($phone);
                    } else {
                        throw new PFException(ERR_REGISTER_CONTENT, ERR_REGISTER);
                    }
                }
            } elseif ($password) {
                $userInfo = ARPfUsers::getUserInfoByPhone($phone);
                if (!$userInfo) {
                    throw new PFException(ERR_USER_EXIST_CONTENT, ERR_USER_EXIST);
                }
                $encrypted_password = $this->getEncryptedPassword($password);
                if ($encrypted_password != $userInfo['password']) {
                    throw new PFException(ERR_LOGIN_CONTENT, ERR_LOGIN);
                }
            } else {
                throw new PFException(ERR_SYS_PARAM_CONTENT, ERR_SYS_PARAM);
            }
            $cookie = self::getCookie($userInfo);
            CookieUtil::Cookie(DataBus::COOKIE_KEY, $cookie[CookieUtil::db_cookiepre . '_' . DataBus::COOKIE_KEY]);
            OutputUtil::info(ERR_OK_CONTENT, ERR_OK, [CookieUtil::db_cookiepre . '_' . DataBus::COOKIE_KEY => $cookie['dw8zh_powerfulfin_user']]);
        } catch (PFException $exception) {
            OutputUtil::err($exception->getMessage(), $exception->getCode());
        }
    }

    public function verifycode() {
        try {
            $phone = Input::get('phone');
            if (!CheckUtil::checkPhone($phone)) {
                throw new PFException(ERR_PHONE_FORMAT_CONTENT, ERR_PHONE_FORMAT);
            }
            $ip = DataBus::get('ip');
            VerifyCode::sendVerifyCode($phone, $ip);
            OutputUtil::info(ERR_OK_CONTENT, ERR_OK);
        } catch (PFException $exception) {
            OutputUtil::err($exception->getMessage(), $exception->getCode());
        }
    }

    private function getCookie(array $userInfo) {
        if (empty($userInfo)) {
            $cookie = [];
        } else {
            $strCode = $userInfo['id'] . '|' . $userInfo['username'] . '|' . $userInfo['phone'] . '|' . CookieUtil::createSafecv();
            $cookie = [CookieUtil::db_cookiepre . '_' . DataBus::COOKIE_KEY => CookieUtil::strCode($strCode)];
        }
        return $cookie;
    }

    public function logout() {
        
    }

    public function setPassword() {
        try {
            $uid = DataBus::getUid();
            if (!$uid) {
                throw new PFException(ERR_NOLOGIN_CONTENT, ERR_NOLOGIN);
            }
            $password_old = Input::get('old_password');
            $password_new = Input::get('new_password');
            if (!$password_new) {
                throw new PFException(ERR_SYS_PARAM_CONTENT, ERR_SYS_PARAM);
            }
            if (strlen($password_new) < 8 || strlen($password_new) > 20) {
                throw new PFException(ERR_PASSWORD_FORMAT_CONTENT, ERR_PASSWORD_FORMAT);
            }
            $user = ARPfUsers::getUserInfoByID($uid);
            if ($user['password']) {
                if (!$password_old || $user['password'] != $this->getEncryptedPassword($password_old)) {
                    throw new PFException(ERR_PASSWORD_CONTENT, ERR_PASSWORD);
                }
            }
            $update = [];
            $update['password'] = $this->getEncryptedPassword($password_new);
            ARPfUsers::updateUserInfo($uid, $update);
            $cookie = self::getCookie($user);
            CookieUtil::Cookie(DataBus::COOKIE_KEY, $cookie[CookieUtil::db_cookiepre . '_' . DataBus::COOKIE_KEY]);
            OutputUtil::info(ERR_OK_CONTENT, ERR_OK, [CookieUtil::db_cookiepre . '_' . DataBus::COOKIE_KEY => $cookie['dw8zh_powerfulfin_user']]);
        } catch (PFException $exception) {
            OutputUtil::err($exception->getMessage(), $exception->getCode());
        }
    }

    public function getEncryptedPassword($password) {
        return strtolower(sha1(env('PASSWORD_SALT') . $password));
    }

}

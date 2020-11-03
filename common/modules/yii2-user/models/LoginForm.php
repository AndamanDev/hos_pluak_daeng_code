<?php
namespace que\user\models;

use Yii;
use dektrium\user\models\LoginForm as BaseLoginForm;
use dektrium\user\traits\ModuleTrait;

class LoginForm extends BaseLoginForm
{
    use ModuleTrait;

    public function getUser()
    {
        return $this->user;
    }

    public function login()
    {
        if ($this->validate() && $this->user) {
            $isLogged = Yii::$app->getUser()->login($this->user, $this->rememberMe ? $this->module->rememberFor : 0);

            if ($isLogged) {
                $this->user->updateAttributes(['last_login_at' => time()]);

                $session = Yii::$app->session;
                $this->user->generateAccessTokenAfterUpdatingClientInfo(true);
                $session->set('access_token',$this->user->access_token);
            }

            return $isLogged;
        }

        return false;
    }
}

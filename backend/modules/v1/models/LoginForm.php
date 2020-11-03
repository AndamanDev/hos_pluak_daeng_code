<?php
namespace backend\modules\v1\models;

use dektrium\user\models\LoginForm as BaseLoginForm;
use dektrium\user\traits\ModuleTrait;

class LoginForm extends BaseLoginForm
{

    use ModuleTrait;

    public function getUser()
    {
        return $this->user;
    }
}

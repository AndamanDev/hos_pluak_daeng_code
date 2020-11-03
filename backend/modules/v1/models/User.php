<?php
namespace backend\modules\v1\models;

use backend\modules\v1\traits\UserTrait;
use dektrium\user\models\User as BaseUser;

class User extends BaseUser
{
    use UserTrait;

    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['access_token_expired_at'], 'safe'];
        return $rules;
    }

    public function fields()
    {
        return [
            'id',
            'username',
            'email',
            'confirmed_at',
            'unconfirmed_email',
            'blocked_at',
            'registration_ip',
            'created_at',
            'updated_at',
            'flags',
            'last_login_at',
            'name' => function ($model) {
                return $model->profile->name;
            },
        ];
    }
}

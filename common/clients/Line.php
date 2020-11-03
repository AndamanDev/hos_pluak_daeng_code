<?php
namespace common\clients;

use yii\authclient\OAuth2;
use dektrium\user\clients\ClientInterface;

class Line extends OAuth2 implements ClientInterface
{
    public $authUrl = 'https://access.line.me/oauth2/v2.1/authorize';

    public $tokenUrl = 'https://api.line.me/oauth2/v2.1/token';

    public $apiBaseUrl = 'https://access.line.me';

    public function init()
    {
        parent::init();
        if ($this->scope === null) {
            $this->scope = implode(' ', [
                'profile',
                'openid',
                //'email',
            ]);
        }
    }

    protected function initUserAttributes()
    {
        return $this->api('v2/profile', 'GET');
    }

    protected function defaultName()
    {
        return 'line';
    }

    protected function defaultTitle()
    {
        return 'Line';
    }

    protected function defaultViewOptions()
    {
        return [
            'popupWidth' => 860,
            'popupHeight' => 480,
        ];
    }

    /** @inheritdoc */
    public function getEmail()
    {
        return isset($this->getUserAttributes()['email'])
            ? $this->getUserAttributes()['email']
            : null;
    }

    /** @inheritdoc */
    public function getUsername()
    {
        return;
    }
}
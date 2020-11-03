<?php
namespace common\components;

use Yii;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

class ApiQueComponent extends Component
{
    public $API_HOST = 'http://192.168.1.3/api/v1';

    protected function getApiClient()
    {
        if(YII_ENV_DEV){
            return new Client(['baseUrl' => 'http://quepmh-backend.com:8082/api/v1']);
        }else{
            return new Client(['baseUrl' => $this->API_HOST]);
        }
    }

    protected function getAccessToken()
    {
        $session = \Yii::$app->session;
        if ($session->has('access_token')) {
            return $session->get('access_token');
        } else {
            return '';
        }
    }

    public function callApiData($method = 'GET', $url = '', $data = null)
    {
        $client = $this->getApiClient();
        $request = $client->createRequest()
            ->setMethod($method)
            ->setUrl($url)
            ->addHeaders(['Authorization' => 'Bearer ' . $this->accessToken]);
        if (is_array($data)) {
            $request->setData($data);
        }
        return $request->send();
    }
}
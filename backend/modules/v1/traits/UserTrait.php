<?php
namespace backend\modules\v1\traits;

use backend\modules\v1\jwt\JWT;
use Yii;
use yii\db\Expression;
use yii\web\Request as WebRequest;
/**
 * Trait to handle JWT-authorization process. Should be attached to User model.
 * If there are many applications using user model in different ways - best way
 * is to use this trait only in the JWT related part.
 */
trait UserTrait
{
    public $access_token;
    /**
     * Store JWT token header items.
     * @var array
     */
    protected static $decodedToken;

    /**
     * Getter for secret key that's used for generation of JWT
     * @return string secret key used to generate JWT
     */
    protected static function getSecretKey()
    {
        return 'someSecretKey';
    }
    /**
     * Getter for "header" array that's used for generation of JWT
     * @return array JWT Header Token param, see http://jwt.io/ for details
     */
    protected static function getHeaderToken()
    {
        return [];
    }

    public function generateAccessTokenAfterUpdatingClientInfo($forceRegenerate = false)
    {
        // update client login, ip
        //$this->last_login_ip = Yii::$app->request->getUserIP();
        $this->last_login_at = new Expression('UNIX_TIMESTAMP()');
        // check time is expired or not
        if ($forceRegenerate == true
            || $this->access_token_expired_at == null
            || (time() > $this->access_token_expired_at)) {
            // generate access token
            $this->generateAccessToken();
        }
        $this->save(false);
        return true;
    }

    public function generateAccessToken()
    {
        // generate access token
        $tokens = $this->getJWT();
        $this->access_token = $tokens[0]; // Token
        $this->access_token_expired_at = date("Y-m-d H:i:s", $tokens[1]['exp']); // Expire
    }
    /**
     * Logins user by given JWT encoded string. If string is correctly decoded
     * - array (token) must contain 'jti' param - the id of existing user
     * @param  string $accessToken access token to decode
     * @return mixed|null          User model or null if there's no user
     * @throws \yii\web\ForbiddenHttpException if anything went wrong
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $secret = static::getSecretKey();
        // Decode token and transform it into array.
        // Firebase\JWT\JWT throws exception if token can not be decoded
        try {
            $decoded = JWT::decode($token, $secret, [static::getAlgo()]);
        } catch (\Exception $e) {
            return false;
        }
        static::$decodedToken = (array) $decoded;
        // If there's no jti param - exception
        if (!isset(static::$decodedToken['jti'])) {
            return false;
        }
        // JTI is unique identifier of user.
        // For more details: https://tools.ietf.org/html/rfc7519#section-4.1.7
        $id = static::$decodedToken['jti'];
        return static::findByJTI($id);
    }
    /**
     * Finds User model using static method findOne
     * Override this method in model if you need to complicate id-management
     * @param  string $id if of user to search
     * @return mixed       User model
     */
    public static function findByJTI($id)
    {
        /** @var User $user */
        $user = static::find()->where([
            '=', 'id', $id,
        ])
            ->andWhere([
                '>', 'access_token_expired_at', new Expression('NOW()'),
            ])->one();
        if ($user !== null &&
            ($user->getIsBlocked() == true || $user->getIsConfirmed() == false)) {
            return null;
        }
        return $user;
    }
    /**
     * Getter for encryption algorytm used in JWT generation and decoding
     * Override this method to set up other algorytm.
     * @return string needed algorytm
     */
    public static function getAlgo()
    {
        return 'HS256';
    }
    /**
     * Returns some 'id' to encode to token. By default is current model id.
     * If you override this method, be sure that findByJTI is updated too
     * @return integer any unique integer identifier of user
     */
    public function getJTI()
    {
        return $this->getId();
    }
    /**
     * Encodes model data to create custom JWT with model.id set in it
     * @return string encoded JWT
     */
    public function getJWT()
    {
        // Collect all the data
        $secret = static::getSecretKey();
        $currentTime = time();
        $expire = $currentTime + 43200; // 1 day //86400
        $request = Yii::$app->request;
        $hostInfo = '';
        // There is also a \yii\console\Request that doesn't have this property
        if ($request instanceof WebRequest) {
            $hostInfo = $request->hostInfo;
        }

        // Merge token with presets not to miss any params in custom
        // configuration
        $token = array_merge([
            'iat' => $currentTime, // Issued at: timestamp of token issuing.
            'iss' => $hostInfo, // Issuer: A string containing the name or identifier of the issuer application. Can be a domain name and can be used to discard tokens from other applications.
            'aud' => $hostInfo,
            'nbf' => $currentTime, // Not Before: Timestamp of when the token should start being considered valid. Should be equal to or greater than iat. In this case, the token will begin to be valid 10 seconds
            'exp' => $expire, // Expire: Timestamp of when the token should cease to be valid. Should be greater than iat and nbf. In this case, the token will expire 60 seconds after being issued.
            'data' => [
                'username' => $this->username,
                'lastLoginAt' => $this->last_login_at,
                'name' => $this->profile->name,
            ],
        ], static::getHeaderToken());
        // Set up id
        $token['jti'] = $this->getJTI(); // JSON Token ID: A unique string, could be used to validate a token, but goes against not having a centralized issuer authority.
        return [JWT::encode($token, $secret, static::getAlgo()), $token];
    }
}

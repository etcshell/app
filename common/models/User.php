<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\RateLimitInterface;
use api\models\ApiAuthToken;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use common\models\Model;


/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $allowance
 * @property integer $allowance_updated_at
 * @property string $password write-only password
 */
class User extends Model implements IdentityInterface,RateLimitInterface
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['username', 'trim'],
            ['password_hash', 'trim'],
            ['username', 'required'],
            ['email', 'required'],
            ['auth_key', 'required'],
            ['password_hash', 'required'],
            ['email', 'unique'],
            ['username', 'unique'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['password_hash', 'string', 'min' => 6],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_DELETED]],
        ];
    }

    public function getRateLimit($request, $action)
    {
        return [5, 10];
    }

    public function loadAllowance($request, $action)
    {
        return [$this->allowance, $this->allowance_updated_at];
    }

    public function saveAllowance($request, $action, $allowance, $timestamp){
      $this->allowance = $allowance;
      $this->allowance_updated_at = $timestamp;
      $this->save();
    }

    public static function getApiAuthToken($token)
    {
       return $this->hasOne(ApiAuthToken::className(), ['user_id' => 'id'])->where('token:token', [':token' => $token])->orderBy('id');
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if($type == HttpBearerAuth::className()){
              $signer = new Sha256();
              $token = Yii::$app->jwt->getParser()->parse((string) $token);
              $user_id = $token->getClaim('uid');
              $data = Yii::$app->jwt->getValidationData();
              $data->setIssuer(Yii::$app->params['api_issuer']);
              $data->setAudience(Yii::$app->params['api_audience']);
              $data->setId(Yii::$app->params['api_auth_id']);
              $data->setCurrentTime(time());
              if($token->validate($data)&&$token->verify($signer, Yii::$app->params['api_signer'])){
                  return static::findOne(['id'=>$user_id,'status'=>self::STATUS_ACTIVE]);
              }
        }else{
            return static::findOne(['status'=>self::STATUS_ACTIVE]);
        }
    }

    public static function createAuthkey($uid)
    {
          $signer = new Sha256();
          $token = Yii::$app->jwt
                      ->getBuilder()
                      ->setIssuer(Yii::$app->params['api_issuer']) // Configures the issuer (iss claim)
                      ->setAudience(Yii::$app->params['api_audience']) // Configures the audience (aud claim)
                      ->setId(Yii::$app->params['api_auth_id'], true) // Configures the id (jti claim), replicating as a header item
                      ->set('uid', $uid) // Configures a new claim, called "uid"
                      ->sign($signer, Yii::$app->params['api_signer'])
                      ->getToken();
          return (string)$token;

    }

    public function afterSave($insert,$changedAttributes)
    {
        if (parent::beforeSave($insert))
        {
            if ($insert)
            {
                $api_auth_token = new ApiAuthToken;
                $api_auth_token->user_id = $this->id;
                $api_auth_token->token = $this->createAuthkey($this->id);
                $api_auth_token->status = self::STATUS_ACTIVE;
                $api_auth_token->ip = ip2long(Yii::$app->request->getUserIP());
                if($api_auth_token->save())
                  return true;
                else
                  return false;
            }
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}

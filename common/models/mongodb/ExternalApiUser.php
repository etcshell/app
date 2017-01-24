<?php
namespace common\models\mongodb;

use Yii;
use common\models\mongodb\MongodbModel;
use yii\filters\RateLimitInterface;
use yii\web\IdentityInterface;
use Lcobucci\JWT\Signer\Hmac\Sha256;

/**
 * @property int|string authorId
 * @property string userName
 * @property string company
 * @property string avatar
 * @property authTokens object
 * @property array apiKeyInfos
 * @property string summary
 * @property string status
 * @property boolean isDeleted
 * @property int created_at
 * @property int updated_at
 */
class ExternalApiUser extends MongodbModel implements RateLimitInterface, IdentityInterface
{

  const STATUS_ACTIVE = 1;
  const STATUS_BLOCK = 0;

  /**
   * @inheritdoc
   */
  public function rules()
  {
      return [
          [['userName', 'company', 'avatar', 'summary'], 'string', 'max' => 200],
          [['userName'], 'unique'],
          [['status'], 'filter', 'filter' => function ($value) {
              return in_array($value, [self::STATUS_ACTIVE, self::STATUS_BLOCK]) ? $value : self::STATUS_ACTIVE;
          }],
          [['created_at','updated_at'], 'filter', 'filter' => function ($value) {
              if ($value == '') {
                  return time();
              } else {
                  return strtotime($value);
              }
          }],
          [['summary', 'apiKeyInfos'], 'safe'],
      ];
    }

    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'external_api_user';
    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'ID',
            'userName' => '用户名称',
            'company' => '公司名称',
            'avatar' => '用户头像',
            'status' => '用户状态',
            'summary' => '用户描述',
            'created_at' => '创建时间',
            'updated_at' => '修改时间',
        ];
    }

    public function attributes()
    {
        return ['_id', 'userName', 'company', 'avatar', 'status', 'summary','apiKeyInfos', 'created_at', 'updated_at'];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
          $signer = new Sha256();
          $token = Yii::$app->jwt->getParser()->parse((string) $token);
          $user_id = $token->getClaim('uid');
          $data = Yii::$app->jwt->getValidationData();
          $data->setIssuer(Yii::$app->params['api_issuer']);
          $data->setAudience(Yii::$app->params['api_audience']);
          $data->setId(Yii::$app->params['api_auth_id']);
          $data->setCurrentTime(time());
          if($token->validate($data)&&$token->verify($signer, Yii::$app->params['api_signer'])){
              return static::findIdentity($user_id);
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

    /**
     * @inheritdoc save external api user apiKeyInfos
     */
    public function afterSave($insert, $changedAttributes)
    {

    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['_id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
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

    public function getRateLimit($request, $action)
    {
        return \common\models\mongodb\ExternalApiSettings::getRateLimit((string)$this->_id, $action->controller->module->module->requestedRoute);
    }

    public function loadAllowance($request, $action)
    {
        return \common\models\mongodb\ExternalApiSettings::loadAllowance((string)$this->_id, $action->controller->module->module->requestedRoute);
    }

    public function saveAllowance($request, $action, $allowance, $timestamp)
    {
        return \common\models\mongodb\ExternalApiSettings::saveAllowance((string)$this->_id, $action->controller->module->module->requestedRoute, $allowance, $timestamp);
    }
}

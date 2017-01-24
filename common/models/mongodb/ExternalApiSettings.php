<?php
namespace common\models\mongodb;

use Yii;
use common\models\mongodb\MongodbModel;

class ExternalApiSettings extends MongodbModel
{
    /**
     * @inheritdoc
     */
    public static function collectionName()
    {
        return 'external_api_settings';
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            '_id' => 'id',
            'userID' => '用户ID',
            'apiURL' => 'visitUrl',
            'rateLimit' => 'rateLimit',
            'duration' => 'duration',
            'allowance' => 'allowance',
            'allowanceLastUpdateTime' => '访问时间',
        ];
    }

    public function attributes()
    {
        return ['_id', 'userID', 'apiURL', 'rateLimit', 'duration', 'allowance','allowanceLastUpdateTime'];
    }
    public static function getRateLimit($userID, $apiUrl)
    {
        if (empty($userID) || empty($apiUrl)) {
            throw  new InvalidParamException('Parameter UserID and ApiURL is required!');
        }

        $setting = self::findOne(['userID' => $userID, 'apiURL' => $apiUrl]);
        if ($setting == null) {
            $setting = new self();
            $setting->userID = $userID;
            $setting->apiURL = $apiUrl;
            $setting->rateLimit = Yii::$app->params['rateLimiting']['rateLimit'];
            $setting->duration =  Yii::$app->params['rateLimiting']['duration'];
            $setting->allowance = Yii::$app->params['rateLimiting']['allowance'];
            $setting->save();
        }

        return [$setting->rateLimit, $setting->duration];
    }

    public static function loadAllowance($userID, $apiUrl)
    {
        if (empty($userID) || empty($apiUrl)) {
            throw  new InvalidParamException('Parameter UserID and ApiURL is required!');
        }

        $setting = self::findOne(['userID' => $userID, 'apiURL' => $apiUrl]);
        if ($setting != null) {
            return [$setting->allowance, $setting->allowanceLastUpdateTime];
        }
    }

    public static function saveAllowance($userID, $apiUrl, $allowance, $allowanceLastUpdateTime)
    {
        if (empty($userID) || empty($apiUrl)) {
            throw  new InvalidParamException('Parameter UserID and ApiURL is required!');
        }

        $setting = self::findOne(['userID' => $userID, 'apiURL' => $apiUrl]);
        if ($setting != null) {
            $setting->allowance = $allowance;
            $setting->allowanceLastUpdateTime = $allowanceLastUpdateTime;
            $setting->save();
        }
    }
}

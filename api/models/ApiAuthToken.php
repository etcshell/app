<?php

namespace api\models;

use Yii;
use common\models\Model;
/**
 * This is the model class for table "api_auth_token".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $token
 * @property integer $ip
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 */
class ApiAuthToken extends Model
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'api_auth_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'token'], 'required'],
            [['user_id', 'ip', 'status'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['token'], 'string', 'max' => 255],
            [['token'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => '用户',
            'token' => 'TOKEN',
            'ip' => 'IP',
            'status' => '状态',
            'created_at' => '添加时间',
            'updated_at' => '登录时间',
        ];
    }

    /**
     * @inheritdoc
     * @return ApiAuthTokenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ApiAuthTokenQuery(get_called_class());
    }
}

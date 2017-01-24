<?php
namespace common\models\mongodb;

use Yii;
use yii\mongodb\ActiveRecord;
use yii\behaviors\TimestampBehavior;

class BaseModel extends ActiveRecord
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     *该必须要是实现
     */
    public function attributes()
    {
        return ['_id', 'status','created_at','updated_at'];
    }

}

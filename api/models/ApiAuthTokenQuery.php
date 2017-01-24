<?php

namespace api\models;

/**
 * This is the ActiveQuery class for [[ApiAuthToken]].
 *
 * @see ApiAuthToken
 */
class ApiAuthTokenQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ApiAuthToken[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ApiAuthToken|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}

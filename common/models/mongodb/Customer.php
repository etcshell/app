<?php
namespace common\models\mongodb;

use yii\mongodb\ActiveRecord;

class Customer extends ActiveRecord
{
    public static function collectionName()
    {
        return 'customer';
    }
    public function saveInfo()
    {

        $customer = new Customer ();
        $customer->name = '111';
        $customer->email = '222';

        $customer->insert ();die();
        return $customer;
    }
    public function attributes()
    {
        return [
        '_id',
        'name',
        'email',
        'address',
        'status'
        ];
    }
}

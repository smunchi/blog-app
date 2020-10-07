<?php

namespace app\components;

use Yii;

class Utils
{
    public static function encrypt($data)
    {
        return self::base64url_encode(Yii::$app->security->encryptByKey($data, Yii::$app->params['secretKey']));
    }

    public static function decrypt(&$data)
    {
        return Yii::$app->security->decryptByKey(self::base64url_decode($data), Yii::$app->params['secretKey']);
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}
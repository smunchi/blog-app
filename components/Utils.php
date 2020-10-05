<?php

namespace app\components;

use common\modules\leninhasda\options\models\Options;
use DateTime;
use DateTimeZone;
use Yii;
use yii\web\Response;

class Utils
{

    /**
     * @param $path
     */
    public static function checkDir($path)
    {
        if (is_array($path)) {
            foreach ($path as $p) {
                self::checkDir($p);
            }
        } else {
            // check if upload dir exists
            if (!file_exists($path)) {
                mkdir($path, 0755);
            }
        }
    }

    /**
     * @return mixed
     */
    public static function getMaxUploadSize()
    {
        $uploadOptions = self::get('uploadOptions');
        return $uploadOptions['maxFileSize'];
    }

    /**
     * @param $file
     * @param string $type
     * @return bool
     */
    public static function validateFile($file, $type = 'file')
    {
        if (!$file || !self::exits($file->tempName)) {
            return false;
        }

        $maxSize = self::getMaxUploadSize();
        if ($file->size > $maxSize) {
            return false;
        }

        $uploadOptions = self::get('uploadOptions');
        $key = 'allowed' . ucfirst($type) . 'Types';
        $allowedTypes = $uploadOptions[$key];

        switch ($type) {
            case "image":
                return self::validateImageFile($file, $allowedTypes);
                break;
            case "file":
                return self::validateFileTypes($file, $allowedTypes);
                break;
            default:
                return false;
                break;
        }

        // return false;
    }

    /**
     * @param $file
     * @param $allowedTypes
     * @return bool
     */
    protected static function validateImageFile($file, $allowedTypes)
    {
        //$isValid = self::validateFileTypes($file, $allowedTypes);
        $isValid = true;
        //if( $isValid ) {
        $ext = $file->extension;
        $fileInfo = getimagesize($file->tempName);
        if (!isset($fileInfo[0]) || !is_numeric($fileInfo[0]) || $fileInfo[0] <= 0
            || !isset($fileInfo[0]) || !is_numeric($fileInfo[0]) || $fileInfo[0] <= 0
            || $fileInfo['mime'] !== $allowedTypes[$ext]) {
            $isValid = false;
        }
        //}
        return $isValid;
    }

    /**
     * @param $file
     * @param $allowedTypes
     * @return bool
     */
    protected static function validateFileTypes($file, $allowedTypes)
    {
        $isValid = true;
        // $ext = end((explode('.', $file->name)));
        $ext = $file->extension;

        if ($file->error !== UPLOAD_ERR_OK
            || !isset($allowedTypes[$ext])
            || $file->type !== $allowedTypes[$ext]) {
            $isValid = false;
        }

        return $isValid;
    }

    /**
     * return sql date time
     * @param null $time
     * @return string
     */
    public static function humanDate($time = null)
    {
        return self::date('F d, Y', $time);
    }

    public static function uniqueCode($limit)
    {
        return substr(base_convert(sha1(uniqid(mt_rand())), 16, 36), 0, $limit);
    }

    public static function encrypt($data)
    {
        return self::base64url_encode(Yii::$app->security->encryptByKey($data, self::getParam('secretKey')));
    }

    public static function decrypt(&$data)
    {
        return Yii::$app->security->decryptByKey(self::base64url_decode($data), self::getParam('secretKey'));
    }

    public static function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public static function base64url_decode($data)
    {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }

    public static function getParam($key)
    {
        return Yii::$app->params[$key];
    }

   public static function generateRandomString($length = 10) {
        $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

}
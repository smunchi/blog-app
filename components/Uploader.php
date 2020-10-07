<?php

namespace app\components;

use Yii;

class Uploader
{

    /**
     * @param $file
     * @param $cdnEnable
     * @param $cdnDir
     * @return array|bool|string
     */
    public static function processImage($file, $dir, $cdnEnable, $cdnDir = 'blog')
    {
        if (!$file || !file_exists($file->tempName)) {
            return false;
        }

        if (!file_exists($dir)) {
            mkdir($dir, 0755);
        }

        $filename = Yii::$app->security->generateRandomString() . '.' . $file->extension;
        if ($file->saveAs($dir . $filename)) {
            if ($cdnEnable) {
                return self::uploadCDN($filename, $dir, $cdnDir);
            } else {
                return $dir . $filename;
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function uploadCDN($filename, $path, $cdnDir = '')
    {
        $s3 = Yii::$app->get('s3');
        $result      = $s3->upload(empty($cdnDir) ? $filename : $cdnDir . $filename, $path . $filename);
        $awsCDNLinks = $result['ObjectURL'];

        return $awsCDNLinks;
    }


    /**
     * @param $filename
     * @return mixed
     * @throws \yii\base\InvalidConfigException
     */
    public static function deleteCDN($filename)
    {
        $s3     = Yii::$app->get('s3');
        $result = $s3->delete($filename);
        return $result;
    }
} 
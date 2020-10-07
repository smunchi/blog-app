<?php

namespace app\components;

use Yii;

class Uploader
{

    /**
     * @param $file
     * @param $cdnEnable
     * @return array|bool|string
     */
    public static function processBlogImage($file, $dir, $cdnEnable)
    {
        if (Utils::validateFile($file, 'image')) {
            Utils::checkDir($dir);

            $filename = Utils::getRandomName() . '.' . $file->extension;
            if ($file->saveAs($dir . $filename)) {
                if ($cdnEnable) {
                    return self::uploadCDN($filename, $dir, 'blog');
                } else {
                    return $dir . $filename;
                }
            }
        }

        return false;
    }

    /**
     * @return array
     */
    public static function uploadCDN($filename, $path, $cdnDir = '')
    {
        //dd($cdnDir);
        $s3 = Yii::$app->get('s3');

        $result      = $s3->upload(empty($cdnDir) ? $filename : $cdnDir . $filename, $path . $filename);
        $awsCDNLinks = $result['ObjectURL'];

        //dd($awsCDNLinks);
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
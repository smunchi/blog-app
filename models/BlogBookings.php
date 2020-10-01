<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%blog_bookings}}".
 *
 * @property int $id
 * @property string $title
 * @property string $sub_title
 * @property string $img_src
 * @property string $button_text
 * @property string $link
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class BlogBookings extends \yii\db\ActiveRecord
{
    public $upload_image;
    public $existing_upload_image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_bookings}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'button_text', 'link'], 'required'],
            [['upload_image'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'sub_title', 'img_src', 'button_text', 'link'], 'string', 'max' => 255],
            ['link', 'url', 'defaultScheme' => 'http']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'title' => Yii::t('app', 'Title'),
            'sub_title' => Yii::t('app', 'Sub Title'),
            'img_src' => Yii::t('app', 'Img Src'),
            'button_text' => Yii::t('app', 'Button Text'),
            'link' => Yii::t('app', 'Link'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord) {
            $this->created_by = Yii::$app->user->id;
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }
}

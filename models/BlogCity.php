<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%blog_cities}}".
 *
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property string $title
 * @property string $sub_title
 * @property string $img_src
 * @property string $content
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class BlogCity extends \yii\db\ActiveRecord
{
    public $upload_image;
    public $existing_upload_image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_cities}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'country_id', 'title', 'content'], 'required'],
            [['name', 'country_id'], 'unique', 'targetAttribute' => ['name', 'country_id'],  'message' => 'This city and country combination has already been taken.'],
            [['country_id', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'sub_title'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'country_id' => Yii::t('app', 'Country'),
            'title' => Yii::t('app', 'Title'),
            'sub_title' => Yii::t('app', 'Sub Title'),
            'img_src' => Yii::t('app', 'Image'),
            'content'=> Yii::t('app', 'content'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getCountry()
    {
        return $this->hasOne(BlogCountry::class, ['id'=> 'country_id']);
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

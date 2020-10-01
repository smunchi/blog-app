<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%blog_countries}}".
 *
 * @property int $id
 * @property string $name
 * @property int $continent_id
 * @property string $title
 * @property string $sub_title
 * @property string $img_src
 * @property string $content
 * @property int $created_by
 * @property int $updated_by
 * @property string $created_at
 * @property string $updated_at
 */
class BlogCountry extends \yii\db\ActiveRecord
{
    public $upload_image;
    public $existing_upload_image;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_countries}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'continent_id', 'title', 'content'], 'required'],
            [['name', 'continent_id'], 'unique', 'targetAttribute' => ['name', 'continent_id'], 'message' => 'This country and continent combination has already been taken.'],
            [['continent_id', 'created_by', 'updated_by'], 'integer'],
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
            'continent_id' => Yii::t('app', 'Continent'),
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

    public function getContinent()
    {
        return $this->hasOne(BlogContinent::class, ['id'=>'continent_id']);
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

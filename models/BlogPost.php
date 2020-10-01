<?php

namespace app\models;

use app\components\Utils;
use Yii;
use yii\behaviors\SluggableBehavior;

/**
 * This is the model class for table "{{%blog_posts}}".
 *
 * @property int $id
 * @property string $code
 * @property string $title
 * @property string $slug
 * @property string $sub_title
 * @property string $content
 * @property string $author_name
 * @property string $featured_image
 * @property int $category_id
 * @property int $views
 * @property string $type
 * @property string $meta_title
 * @property string $meta_description
 * @property string $meta_keywords
 * @property int $created_by
 * @property string $created_at
 * @property int $updated_by
 * @property string $updated_at
 */
class BlogPost extends \yii\db\ActiveRecord
{
    const author_name = 'Author';
    public $upload_image;
    public $existing_upload_image;
    public $tags;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%blog_posts}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['title', 'author_name', 'content', 'category_id', 'type'], 'required'],
            ['title', 'unique', 'targetClass' => BlogPost::class, 'message' => 'This title has already been taken.'],
            [['content', 'meta_title', 'meta_description', 'meta_keywords', 'code'], 'string'],
            [['upload_image'], 'file', 'extensions' => 'png, jpg, jpeg', 'skipOnEmpty' => true],
            [['views', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            ['meta_keywords', 'match', 'pattern'=>'/^\w+(?:(?:,\s\w+)+|(?:,\w+)+|(?:\w+))$/', 'message'=>"{attribute} is Invalid."],
            [['title', 'slug', 'sub_title', 'author_name', 'featured_image'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'code' => Yii::t('app', 'Code'),
            'title' => Yii::t('app', 'Title'),
            'slug' => Yii::t('app', 'Slug'),
            'sub_title' => Yii::t('app', 'Sub Title'),
            'content' => Yii::t('app', 'Content'),
            'author_name' => Yii::t('app', 'Author Name'),
            'featured_image' => Yii::t('app', 'Featured Image'),
            'category_id' => Yii::t('app', 'Category'),
            'views' => Yii::t('app', 'Views'),
            'type' => Yii::t('app', 'Type'),
            'meta_title' => Yii::t('app', 'Meta Title'),
            'meta_description' => Yii::t('app', 'Meta Description'),
            'meta_keywords' => Yii::t('app', 'Meta Keywords'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function getCategory()
    {
        return $this->hasOne(BlogCategory::class, ['id' => 'category_id']);
    }

    public function getPostTags()
    {
        return $this->hasMany(BlogTags::class, ['id' => 'tag_id'])->viaTable(BlogPostTags::tableName(), ['post_id' => 'id']);
    }

    public function behaviors()
    {
        $post = Yii::$app->request->post();

        if(!empty($post['BlogPost']['slug'])) {
            return [
                [
                    'class' => SluggableBehavior::class,
                    'attribute' => 'slug',
                     'slugAttribute' => 'slug',
                ],
            ];
        }

        return [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'title'
            ],
        ];
    }

    public function beforeSave($insert)
    {
        if($this->isNewRecord) {
            $this->created_by = Yii::$app->user->id;
            $this->code = Utils::uniqueCode(32);
        } else {
            $this->updated_by = Yii::$app->user->id;
            $this->updated_at = date('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }
}

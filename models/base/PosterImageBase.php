<?php

namespace app\models\base;

use Yii;

use app\models\Poster;

/**
 * This is the base model class for table "poster_image".
 *
 * @property int $id
 * @property int $poster_id
 * @property string $title
 * @property string $description
 * @property string $filename
 * @property string $crop_settings
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property Poster $poster
 */
class PosterImageBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'poster_image';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['poster_id', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['title', 'description', 'filename', 'crop_settings'], 'string', 'max' => 255],
            [['poster_id'], 'exist', 'skipOnError' => true, 'targetClass' => Poster::className(), 'targetAttribute' => ['poster_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'poster_id' => Yii::t('app', 'Poster ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'filename' => Yii::t('app', 'Filename'),
            'crop_settings' => Yii::t('app', 'Crop Settings'),
            'status_id' => Yii::t('app', 'Status ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPoster()
    {
        return $this->hasOne(Poster::className(), ['id' => 'poster_id']);
    }
}

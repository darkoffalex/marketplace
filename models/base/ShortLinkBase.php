<?php

namespace app\models\base;

use Yii;

use app\models\User;

/**
 * This is the base model class for table "short_link".
 *
 * @property int $id
 * @property int $number
 * @property int $user_id
 * @property string $phone
 * @property string $original_link
 * @property int $type_id
 * @property string $message
 * @property int $status_id
 * @property string $key
 * @property int $custom_key
 * @property int $clicks
 * @property string $title
 * @property string $description
 * @property string $image_file
 * @property string $site_name
 * @property string $created_at
 * @property string $update_at
 *
 * @property User $user
 */
class ShortLinkBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'short_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'user_id', 'type_id', 'status_id', 'custom_key', 'clicks'], 'integer'],
            [['original_link', 'message'], 'string'],
            [['created_at', 'update_at'], 'safe'],
            [['phone', 'key', 'title', 'description', 'image_file', 'site_name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'number' => Yii::t('app', 'Number'),
            'user_id' => Yii::t('app', 'User ID'),
            'phone' => Yii::t('app', 'Phone'),
            'original_link' => Yii::t('app', 'Original Link'),
            'type_id' => Yii::t('app', 'Type ID'),
            'message' => Yii::t('app', 'Message'),
            'status_id' => Yii::t('app', 'Status ID'),
            'key' => Yii::t('app', 'Key'),
            'custom_key' => Yii::t('app', 'Custom Key'),
            'clicks' => Yii::t('app', 'Clicks'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'image_file' => Yii::t('app', 'Image File'),
            'site_name' => Yii::t('app', 'Site Name'),
            'created_at' => Yii::t('app', 'Created At'),
            'update_at' => Yii::t('app', 'Update At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}

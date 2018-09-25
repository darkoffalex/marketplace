<?php

namespace app\models\base;

use Yii;

use app\models\DictionaryNotification;
use app\models\MonitoredGroupPost;

/**
 * This is the base model class for table "monitored_group_post_comment".
 *
 * @property int $id
 * @property int $post_id
 * @property string $facebook_id
 * @property string $text
 * @property int $parent_id
 * @property int $attachments_count
 * @property int $reactions_count
 * @property int $comments_count
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property DictionaryNotification[] $dictionaryNotifications
 * @property MonitoredGroupPost $post
 */
class MonitoredGroupPostCommentBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monitored_group_post_comment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['post_id', 'parent_id', 'attachments_count', 'reactions_count', 'comments_count', 'created_by_id', 'updated_by_id'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['facebook_id'], 'string', 'max' => 255],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonitoredGroupPost::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'text' => Yii::t('app', 'Text'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'attachments_count' => Yii::t('app', 'Attachments Count'),
            'reactions_count' => Yii::t('app', 'Reactions Count'),
            'comments_count' => Yii::t('app', 'Comments Count'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionaryNotifications()
    {
        return $this->hasMany(DictionaryNotification::className(), ['comment_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(MonitoredGroupPost::className(), ['id' => 'post_id']);
    }
}

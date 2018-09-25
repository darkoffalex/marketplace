<?php

namespace app\models\base;

use Yii;

use app\models\DictionaryNotification;
use app\models\MonitoredGroup;
use app\models\MonitoredGroupPostComment;

/**
 * This is the base model class for table "monitored_group_post".
 *
 * @property int $id
 * @property int $group_id
 * @property string $facebook_id
 * @property string $text
 * @property int $attachments_count
 * @property int $reactions_count
 * @property int $comments_count
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property DictionaryNotification $dictionaryNotification
 * @property MonitoredGroup $group
 * @property MonitoredGroupPostComment[] $monitoredGroupPostComments
 */
class MonitoredGroupPostBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monitored_group_post';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_id', 'attachments_count', 'reactions_count', 'comments_count', 'created_by_id', 'updated_by_id'], 'integer'],
            [['text'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['facebook_id'], 'string', 'max' => 255],
            [['group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonitoredGroup::className(), 'targetAttribute' => ['group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'group_id' => Yii::t('app', 'Group ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'text' => Yii::t('app', 'Text'),
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
    public function getDictionaryNotification()
    {
        return $this->hasOne(DictionaryNotification::className(), ['post_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroup()
    {
        return $this->hasOne(MonitoredGroup::className(), ['id' => 'group_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroupPostComments()
    {
        return $this->hasMany(MonitoredGroupPostComment::className(), ['post_id' => 'id']);
    }
}

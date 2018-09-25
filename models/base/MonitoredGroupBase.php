<?php

namespace app\models\base;

use Yii;

use app\models\User;
use app\models\MonitoredGroupDictionary;
use app\models\Dictionary;
use app\models\MonitoredGroupPost;

/**
 * This is the base model class for table "monitored_group".
 *
 * @property int $id
 * @property int $user_id
 * @property string $facebook_id
 * @property string $name
 * @property string $privacy
 * @property int $status_id
 * @property string $sync_done_last_time
 * @property int $sync_in_progress
 * @property string $sync_since
 * @property string $sync_to
 * @property string $parsing_errors_log
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property User $user
 * @property MonitoredGroupDictionary[] $monitoredGroupDictionaries
 * @property Dictionary[] $dictionaries
 * @property MonitoredGroupPost[] $monitoredGroupPosts
 */
class MonitoredGroupBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monitored_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status_id', 'sync_in_progress', 'created_by_id', 'updated_by_id'], 'integer'],
            [['name'], 'required'],
            [['sync_done_last_time', 'sync_since', 'sync_to', 'created_at', 'updated_at'], 'safe'],
            [['parsing_errors_log'], 'string'],
            [['facebook_id', 'name', 'privacy'], 'string', 'max' => 255],
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
            'user_id' => Yii::t('app', 'User ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'name' => Yii::t('app', 'Name'),
            'privacy' => Yii::t('app', 'Privacy'),
            'status_id' => Yii::t('app', 'Status ID'),
            'sync_done_last_time' => Yii::t('app', 'Sync Done Last Time'),
            'sync_in_progress' => Yii::t('app', 'Sync In Progress'),
            'sync_since' => Yii::t('app', 'Sync Since'),
            'sync_to' => Yii::t('app', 'Sync To'),
            'parsing_errors_log' => Yii::t('app', 'Parsing Errors Log'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroupDictionaries()
    {
        return $this->hasMany(MonitoredGroupDictionary::className(), ['monitored_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionaries()
    {
        return $this->hasMany(Dictionary::className(), ['id' => 'dictionary_id'])->viaTable('monitored_group_dictionary', ['monitored_group_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroupPosts()
    {
        return $this->hasMany(MonitoredGroupPost::className(), ['group_id' => 'id']);
    }
}

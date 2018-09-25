<?php

namespace app\models\base;

use Yii;

use app\models\User;
use app\models\DictionaryNotification;
use app\models\DictionarySubscriber;
use app\models\MonitoredGroupDictionary;
use app\models\MonitoredGroup;

/**
 * This is the base model class for table "dictionary".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $words
 * @property string $key
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 *
 * @property User $user
 * @property DictionaryNotification[] $dictionaryNotifications
 * @property DictionarySubscriber[] $dictionarySubscribers
 * @property MonitoredGroupDictionary[] $monitoredGroupDictionaries
 * @property MonitoredGroup[] $monitoredGroups
 */
class DictionaryBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['name'], 'required'],
            [['words'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'key'], 'string', 'max' => 255],
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
            'name' => Yii::t('app', 'Name'),
            'words' => Yii::t('app', 'Words'),
            'key' => Yii::t('app', 'Key'),
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionaryNotifications()
    {
        return $this->hasMany(DictionaryNotification::className(), ['dictionary_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionarySubscribers()
    {
        return $this->hasMany(DictionarySubscriber::className(), ['dictionary_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroupDictionaries()
    {
        return $this->hasMany(MonitoredGroupDictionary::className(), ['dictionary_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroups()
    {
        return $this->hasMany(MonitoredGroup::className(), ['id' => 'monitored_group_id'])->viaTable('monitored_group_dictionary', ['dictionary_id' => 'id']);
    }
}

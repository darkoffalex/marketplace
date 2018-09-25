<?php

namespace app\models\base;

use Yii;

use app\models\DictionaryNotificationTask;
use app\models\Dictionary;

/**
 * This is the base model class for table "dictionary_subscriber".
 *
 * @property int $id
 * @property int $dictionary_id
 * @property string $facebook_id
 * @property string $name
 * @property string $avatar_url
 * @property string $excluded_groups
 * @property string $created_at
 *
 * @property DictionaryNotificationTask[] $dictionaryNotificationTasks
 * @property Dictionary $dictionary
 */
class DictionarySubscriberBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary_subscriber';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dictionary_id'], 'integer'],
            [['excluded_groups'], 'string'],
            [['created_at'], 'safe'],
            [['facebook_id', 'name', 'avatar_url'], 'string', 'max' => 255],
            [['dictionary_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['dictionary_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'dictionary_id' => Yii::t('app', 'Dictionary ID'),
            'facebook_id' => Yii::t('app', 'Facebook ID'),
            'name' => Yii::t('app', 'Name'),
            'avatar_url' => Yii::t('app', 'Avatar Url'),
            'excluded_groups' => Yii::t('app', 'Excluded Groups'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionaryNotificationTasks()
    {
        return $this->hasMany(DictionaryNotificationTask::className(), ['subscriber_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionary()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'dictionary_id']);
    }
}

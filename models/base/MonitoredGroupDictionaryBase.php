<?php

namespace app\models\base;

use Yii;

use app\models\Dictionary;
use app\models\MonitoredGroup;

/**
 * This is the base model class for table "monitored_group_dictionary".
 *
 * @property int $monitored_group_id
 * @property int $dictionary_id
 *
 * @property Dictionary $dictionary
 * @property MonitoredGroup $monitoredGroup
 */
class MonitoredGroupDictionaryBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'monitored_group_dictionary';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['monitored_group_id', 'dictionary_id'], 'required'],
            [['monitored_group_id', 'dictionary_id'], 'integer'],
            [['monitored_group_id', 'dictionary_id'], 'unique', 'targetAttribute' => ['monitored_group_id', 'dictionary_id']],
            [['dictionary_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['dictionary_id' => 'id']],
            [['monitored_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonitoredGroup::className(), 'targetAttribute' => ['monitored_group_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'monitored_group_id' => Yii::t('app', 'Monitored Group ID'),
            'dictionary_id' => Yii::t('app', 'Dictionary ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionary()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'dictionary_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMonitoredGroup()
    {
        return $this->hasOne(MonitoredGroup::className(), ['id' => 'monitored_group_id']);
    }
}

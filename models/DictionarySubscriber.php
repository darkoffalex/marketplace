<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "dictionary_subscriber".
 */
class DictionarySubscriber extends \app\models\base\DictionarySubscriberBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        return $labels;
    }

    ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Игнорируется ли группа
     * @param $groupId
     * @return bool
     */
    public function IsGroupIgnored($groupId)
    {
        $excluded = !empty($this->excluded_groups) ? explode(',',$this->excluded_groups) : [];
        return in_array($groupId,$excluded);
    }

    /**
     * Добавить в список игнорируемых
     * @param $groupId
     * @param bool $save
     */
    public function AddToIgnored($groupId, $save = false)
    {
        $excluded = !empty($this->excluded_groups) ? explode(',',$this->excluded_groups) : [];

        if(!in_array($groupId,$excluded)){
            $excluded[] = $groupId;
            $this->excluded_groups = implode(',',$excluded);

            if($save){
                $this->save();
            }
        }
    }

    /**
     * Убрать из списка игнорируемых
     * @param $groupId
     * @param bool $save
     */
    public function RemoveFromIgnored($groupId, $save = false)
    {
        $excluded = !empty($this->excluded_groups) ? explode(',',$this->excluded_groups) : [];
        ArrayHelper::removeValue($excluded,$groupId);
        $this->excluded_groups = implode(',',$excluded);

        if($save){
            $this->save();
        }
    }

    /**
     * Получить игнорируемые группы
     * @return MonitoredGroup[]|array
     */
    public function IgnoredGroups()
    {
        $excluded = !empty($this->excluded_groups) ? explode(',',$this->excluded_groups) : [];
        if(empty($excluded)){
            return [];
        }

        /* @var $groups MonitoredGroup[] */
        $groups = MonitoredGroup::find()->where(['id' => $excluded])->all();
        return $groups;
    }
}

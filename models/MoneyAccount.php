<?php

namespace app\models;

use Yii;
use app\helpers\Constants;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "money_account".
 */
class MoneyAccount extends \app\models\base\MoneyAccountBase
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

    /**
     * Получить полное наименование счета
     * @return string
     */
    public function getFullName()
    {
        $typeNames = [
            Constants::SYSTEM_INCOME_ACCOUNT => Yii::t('app','System-income'),
            Constants::SYSTEM_OUTGO_ACCOUNT => Yii::t('app','System-outgo'),
            Constants::GROUP_ADMIN_ACCOUNT => Yii::t('app','Group-admin'),
            Constants::MEMBER_ACCOUNT => Yii::t('app','Advertiser'),
            Constants::MANAGER_ACCOUNT => Yii::t('app','Manager')
        ];

        $userName = !empty($this->user) ? $this->user->name : Yii::t('app','SYSTEM');
        $type = ArrayHelper::getValue($typeNames,$this->account_type_id,null);

        return "{$this->id} ({$userName}) [$type]";
    }
}

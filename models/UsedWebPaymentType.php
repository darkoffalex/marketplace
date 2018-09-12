<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "used_web_payment_type".
 */
class UsedWebPaymentType extends \app\models\base\UsedWebPaymentTypeBase
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
     * Получить имя способа оплаты (коды яндек-кассы)
     * @return mixed
     */
    public function getYmName()
    {
        $names = [
            'PC' => 'Яндекс.Деньги',
            'AC' => 'Банковская карта',
            'MC' => 'Баланс телефона',
            'GP' => 'Наличные',
            'EP' => 'ЕРИП (Беларусь)',
            'WM' => 'WebMoney',
            'SB' => 'Сбербанк Онлайн',
            'MP' => 'Мобильный терминал (mPOS)',
            'AB' => 'Альфа-Клик',
            'MA' => 'MasterPass',
            'PB' => 'Интернет-банк Промсвязьбанка',
            'QW' => 'QIWI Wallet',
            'KV' => 'КупиВкредит',
            'CR' => 'Заплатить по частям'
        ];

        return ArrayHelper::getValue($names,$this->code,Yii::t('app','Unknown (code: {code})',['code' => $this->code]));
    }
}

<?php

namespace app\models\forms;

use app\models\MarketplaceKey;
use Yii;
use yii\base\Model;

class BindMarketplaceForm extends Model
{
    /**
     * @var null|MarketplaceKey
     */
    public $key = null;

    /**
     * @var string
     */
    public $code;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            ['code', 'string'],
            ['code', 'required'],
            ['code', 'checkCode']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'code' => Yii::t('app','Code'),
        ];
    }

    /**
     * Проверка валидности кода
     * @param $attribute
     * @param $params
     */
    public function checkCode($attribute, $params)
    {
        if (!$this->hasErrors()) {
            /* @var $key MarketplaceKey */
            $this->key = MarketplaceKey::find()->where(['code' => $this->$attribute])->one();

            if(empty($this->key)){
                $this->addError($attribute,Yii::t('app','Can\'t find marketplace'));
            }else{
                if(!empty($this->key->used_by_id)){
                    $this->addError($attribute,Yii::t('app','Key already used'));
                }

                /*
                if($this->key->marketplace->user_id == Yii::$app->id){
                    $this->addError($attribute,Yii::t('app','Can\'t add own marketplaces'));
                }
                */
            }
        }
    }
}

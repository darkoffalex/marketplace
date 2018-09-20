<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "cv".
 */
class Cv extends \app\models\base\CvBase
{
    /**
     * @var null|int
     */
    public $create_marketplace = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['name','group_name','group_popularity','group_url','email','phone'],'required'];
        $rules[] = [['create_marketplace'], 'safe'];
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
}

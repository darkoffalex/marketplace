<?php

namespace app\widgets;

use app\helpers\Constants;
use app\models\Language;
use yii\base\Widget;

class LanguageSwitchWidget extends Widget
{
    /**
     * @var array|Language[]
     */
    private $languages = [];

    public function init()
    {
        parent::init();

        $this->languages = Language::find()
            ->where(['status_id' => Constants::STATUS_ENABLED])
            ->orderBy('priority ASC')
            ->all();
    }

    public function run()
    {
        return $this->render('languages',['languages' => $this->languages]);
    }
}
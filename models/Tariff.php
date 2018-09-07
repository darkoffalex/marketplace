<?php

namespace app\models;

use app\helpers\Constants;
use Carbon\Carbon;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "tariff".
 */
class Tariff extends \app\models\base\TariffBase
{
    /**
     * @var UploadedFile
     */
    public $image;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['image'], 'file', 'extensions' => ['jpg','png','gif', 'jpeg'], 'skipOnEmpty' => true, 'maxSize' => 1024 * 1024 * 10];
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
     * Получить интекрвал от текущего момента до окончания оплаты (в секундах)
     * @return int
     */
    public function getIntervalInSeconds()
    {
        $now = time();
        $until = $now;

        switch ($this->period_unit_type){
            case Constants::PERIOD_DAYS:
                $until = Carbon::parse($now)->addDay($this->period_amount)->getTimestamp();
                break;
            case Constants::PERIOD_WEEKS:
                $until = Carbon::parse($now)->addWeek($this->period_amount)->getTimestamp();
                break;
            case Constants::PERIOD_MONTHS:
                $until = Carbon::parse($now)->addMonth((int)$this->period_amount)->getTimestamp();
                break;
        }

        return $until - $now;
    }
}

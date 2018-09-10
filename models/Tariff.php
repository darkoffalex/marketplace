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
                $until = Carbon::now()->addDay($this->period_amount)->getTimestamp();
                break;
            case Constants::PERIOD_WEEKS:
                $until = Carbon::now()->addWeek($this->period_amount)->getTimestamp();
                break;
            case Constants::PERIOD_MONTHS:
                $until = Carbon::now()->addMonth((int)$this->period_amount)->getTimestamp();
                break;
        }

        return $until - $now;
    }

    /**
     * Получить дату окончания тарифа
     * @param string $format
     * @return false|string
     */
    public function getUntilDate($format = 'd-m-Y H:i')
    {
        switch ($this->period_unit_type){
            case Constants::PERIOD_DAYS:
                return Carbon::now()->addDay($this->period_amount)->format($format);
                break;
            case Constants::PERIOD_WEEKS:
                return Carbon::now()->addWeek($this->period_amount)->format($format);
                break;
            case Constants::PERIOD_MONTHS:
                return Carbon::now()->addMonth((int)$this->period_amount)->format($format);
                break;
        }

        return date($format);
    }

    /**
     * Получить наименование интервала
     * @return string
     */
    public function getIntervalName()
    {
        $names = [
            Constants::PERIOD_DAYS => Yii::t('app','Day(s)'),
            Constants::PERIOD_WEEKS => Yii::t('app','Week(s)'),
            Constants::PERIOD_MONTHS => Yii::t('app','Month(s)'),
        ];

        return $this->period_amount.' '.(!empty($names[$this->period_unit_type]) ? $names[$this->period_unit_type] : null);
    }
}

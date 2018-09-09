<?php

namespace app\models;

use app\helpers\Constants;
use app\helpers\CropHelper;
use Carbon\Carbon;
use app\helpers\Help;
use Yii;
use yii\helpers\Url;

/**
 * This is the model class for table "poster".
 */
class Poster extends \app\models\base\PosterBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['title','description','phone'], 'required', 'on' => 'editing'];
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
     * Имеет ли объявление новые изменения
     * @return bool
     */
    public function hasNewChanges()
    {
        $editable = [
            $this->title,
            $this->description,
            $this->phone,
            $this->whats_app
        ];

        $approved = [
            $this->title_approved,
            $this->description_approved,
            $this->phone_approved,
            $this->whats_app_approved
        ];

        return $editable != $approved || $this->getPosterImages()->where(['status_id' => Constants::STATUS_TEMPORARY])->count() > 0;
    }

    /**
     * Оплачено ли на данный момент объявление
     * @return bool
     */
    public function isPaid()
    {
        if(empty($this->marketplaceTariff->tariff)){
            return false;
        }

        switch ($this->marketplaceTariff->tariff->special_type){
            case Constants::TARIFF_SUB_TYPE_REGULAR:
                return Carbon::parse($this->paid_at)->addSeconds($this->period_seconds)->getTimestamp() > time();
                break;
            default:
                return false;
                break;
        }
    }

    /**
     * Информация об оплате
     * @return string
     */
    public function getPaymentInformation()
    {
        if(!empty($this->paid_at)){
            if($this->marketplaceTariff->tariff->special_type == Constants::TARIFF_SUB_TYPE_REGULAR){
                $ends = Carbon::parse($this->paid_at)->addSeconds($this->period_seconds);
                if($ends->getTimestamp() > time()){
                    return '<span class="label label-success">'.Yii::t('app','Paid (until {date})',['date' => $ends->format('d.m.Y H:i')]).'</span>';
                }else{
                    return '<span class="label label-success">'.Yii::t('app','Not paid (until {date})',['date' => $ends->format('d.m.Y H:i')]).'</span>';
                }
            }else{
                /* TODO: Выводить информациб по иным типам тарифов */
                return 'INFORMATION OUTPUT NOT READY';
            }
        }
        return '<span class="label label-danger">'.Yii::t('app','Not paid').'</span>';
    }

    /**
     * Получить информацию о тарифе
     * @return string
     */
    public function getTariffInformation()
    {
        if($this->status_id == Constants::STATUS_TEMPORARY){
            return Yii::t('app','Not selected yet');
        }

        return !empty($this->marketplaceTariff) ? $this->marketplaceTariff->marketplace->name.' > '.$this->marketplaceTariff->tariff->name.' ('.Help::toPrice($this->marketplaceTariff->price).')' : Yii::t('app','No info');
    }

    /**
     * Получить список-массив загруженных изображений (сортировка по приоритету)
     * @param null|int|array $status
     * @param bool $json
     * @return array|string
     */
    public function getImagesListed($status = null, $json = false)
    {
        $q = PosterImage::find()->where(['poster_id' => $this->id]);
        if($status)$q->andWhere(['stats_id' => $status]);

        /* @var $images PosterImage[] */
        $images = $q->orderBy('priority ASC')->all();
        $items = [];
        foreach ($images as $posterImage){
            $items[] = [
                'name' => $posterImage->title,
                'size' => $posterImage->size,
                'url' => Url::to("@web/upload/images/{$posterImage->filename}"),
                'thumbnailUrl' => CropHelper::ThumbnailUrl($posterImage->filename,100,100),
                'deleteUrl' => Url::to(['/user/posters/delete-image', 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        if($json){
            return json_encode($items);
        }

        return $items;
    }
}

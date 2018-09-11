<?php

namespace app\models;

use app\helpers\Constants;
use app\helpers\CropHelper;
use Carbon\Carbon;
use Yii;
use yii\helpers\Url;

/**
 * Class Poster
 * @package app\models
 * @property PosterImage $mainImageActive
 * @property PosterImage $mainImage
 */
class Poster extends \app\models\base\PosterBase
{
    /**
     * @var bool|int
     */
    public $agreement;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['title','description','phone'], 'required', 'on' => ['editing','creating']];
        $rules[] = [['agreement'], 'required', 'requiredValue' => 1, 'on' => 'creating', 'message' => Yii::t('app','Please accept the terms')];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['agreement'] = Yii::t('app','I agree');
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

        if(empty($this->paid_at)){
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
     * Получить список-массив загруженных изображений (сортировка по приоритету)
     * @param null $status
     * @param bool $json
     * @param string $deleteBaseUrl
     * @return array|string
     */
    public function getImagesListed($status = null, $json = false, $deleteBaseUrl = '/user/posters/delete-image')
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
                'deleteUrl' => Url::to([$deleteBaseUrl, 'id' => $posterImage->id]),
                'deleteType' => 'GET',
            ];
        }

        if($json){
            return json_encode($items);
        }

        return $items;
    }

    /**
     * Подтверждено ли объявление и админом группы и супер-админом
     * @return bool
     */
    public function isApprovedByAll()
    {
        return $this->approved_by_sa && $this->approved_by_ga;
    }

    /**
     * Перенести данные в "раздел" подтвержденных
     * @param bool $publish
     */
    public function approveData($publish = true)
    {
        $this->title_approved = $this->title;
        $this->description_approved = $this->description;
        $this->phone_approved = $this->phone;
        $this->whats_app_approved = $this->whats_app;

        PosterImage::updateAll(['status_id' => Constants::STATUS_ENABLED],['poster_id' => $this->id]);

        if($publish){
            $this->published = (int)true;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainImageActive()
    {
        return $this->hasOne(PosterImage::class, ['poster_id' => 'id'])
            ->where(['status_id' => Constants::STATUS_ENABLED])
            ->orderBy('main_pic, priority ASC');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMainImage()
    {
        return $this->hasOne(PosterImage::class, ['poster_id' => 'id'])
            ->orderBy('main_pic, priority ASC');
    }
}

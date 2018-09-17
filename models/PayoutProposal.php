<?php

namespace app\models;

use Yii;
use app\helpers\Constants;
use yii\helpers\Url;
use app\helpers\CropHelper;

/**
 * This is the model class for table "payout_proposal".
 */
class PayoutProposal extends \app\models\base\PayoutProposalBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['amount'], 'integer', 'min' => 100, 'tooSmall' => Yii::t('app','To small amount')];
        $rules[] = [['amount'], 'checkUserAccount', 'on' => 'creating'];
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
     * Есть ли нехватка средств на счету
     * @param $attribute
     * @param $params
     */
    public function checkUserAccount($attribute, $params)
    {
        if (!$this->hasErrors()) {

            /* @var $user User */
            $user = Yii::$app->user->identity;
            /* @var $account MoneyAccount */
            $account = $user->getMoneyAccount(Constants::GROUP_ADMIN_ACCOUNT);

            if ($this->$attribute > $account->amount) {
                $this->addError($attribute, Yii::t('app','Not enough money on your account'));
            }
        }
    }

    /**
     * Получить список-массив загруженных изображений (сортировка по приоритету)
     * @param null $status
     * @param bool $json
     * @param string $deleteBaseUrl
     * @return array|string
     */
    public function getImagesListed($status = null, $json = false, $deleteBaseUrl = '/admin/payout-proposals/delete-image')
    {
        $q = PayoutProposalImage::find()->where(['proposal_id' => $this->id]);
        if($status)$q->andWhere(['stats_id' => $status]);

        /* @var $images PayoutProposalImage[] */
        $images = $q->orderBy('priority ASC')->all();
        $items = [];
        foreach ($images as $proposalImage){
            $items[] = [
                'name' => $proposalImage->title,
                'size' => $proposalImage->size,
                'url' => Url::to("@web/upload/images/{$proposalImage->filename}"),
                'thumbnailUrl' => CropHelper::ThumbnailUrl($proposalImage->filename,100,100),
                'deleteUrl' => Url::to([$deleteBaseUrl, 'id' => $proposalImage->id]),
                'deleteType' => 'GET',
            ];
        }

        if($json){
            return json_encode($items);
        }

        return $items;
    }

    /**
     * Получить массив изображения light-box виджета
     * @param int $thumbnailWidth
     * @return array
     */
    public function getImageAttachmentUrlsForLightBox($thumbnailWidth = 150)
    {
        $result = [];

        if(!empty($this->payoutProposalImages)){
            foreach ($this->payoutProposalImages as $attachment){
                $result[] = [
                    'thumb' => $attachment->getThumbnailUrl($thumbnailWidth,$thumbnailWidth),
                    'original' => $attachment->getUrl(),
                    'title' => $attachment->title,
                    'thumbOptions' => ['class' => 'img-thumbnail margin-r-5', 'style' => 'max-width:'.$thumbnailWidth.'px;']
                ];
            }
        }

        return $result;
    }
}

<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;

/**
 * This is the model class for table "dictionary_notification".
 */
class DictionaryNotification extends \app\models\base\DictionaryNotificationBase
{
    const TYPE_POST = 1;
    const TYPE_COMMENT = 2;

    /**
     * @var int
     */
    public $type_id = null;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
        $rules[] = ['type_id', 'safe'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['type_id'] = Yii::t('app','Type');
        return $labels;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Сформировать поисковой запрос и вернуть DataProvider
     * @param $params
     * @param null $userId
     * @return ActiveDataProvider
     */
    public function search($params, $userId = null)
    {
        $q = parent::find()->alias('dn')->joinWith('dictionary as d');

        if(!empty($userId)){
            $q->andWhere(['d.user_id' => $userId]);
        }

        if($this->load($params)){
            if($this->validate()){

                if(!empty($this->id)){
                    $q->andWhere(['dn.id' => $this->id]);
                }

                if(!empty($this->excerpt)){
                    $q->andWhere(['like','excerpt',$this->excerpt]);
                }

                if(!empty($this->word)){
                    $q->andWhere(['like','word',$this->word]);
                }

                if(!empty($this->type_id)){
                    if($this->type_id == self::TYPE_POST){
                        $q->andWhere('dn.comment_id IS NULL');
                    }elseif($this->type_id == self::TYPE_COMMENT){
                        $q->andWhere('dn.comment_id IS NOT NULL');
                    }
                }
            }
        }

        $q->orderBy('created_at DESC');

        $q->distinct();

        return new ActiveDataProvider([
            'query' => $q,
            'pagination' => [
                'pageSize' => !empty($this->per_page) ? (int)$this->per_page : 20,
            ],
        ]);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Получить сообщение
     * @param bool $html
     * @return string
     */
    public function GetMessage($html = false)
    {
        $subjectLink = !empty($this->comment_id) ? "https://www.facebook.com/{$this->comment->facebook_id}" : "https://www.facebook.com/{$this->post->facebook_id}";

        if($html){
            $subjectLink = Html::a($subjectLink,$subjectLink,['target' => '_blank']);
        }

        $message = Yii::t('app',"Found keyword \"{keyword}\" in {subjectType} {subjectLink}\n\nExcerpt: {excerpt}", [
            'keyword' => $this->word,
            'subjectType' => !empty($this->comment_id) ? Yii::t('app','comment') : Yii::t('app','post'),
            'subjectLink' => $subjectLink,
            'excerpt' => $this->excerpt
        ]);

        return $html ? nl2br($message) : $message;
    }
}

<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "dictionary".
 */
class Dictionary extends \app\models\base\DictionaryBase
{
    /**
     * @var array Группы которые относятся к словарю
     */
    public $groups_arr = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();

        foreach ($rules as $index => $originalRule){
            if(($originalRule[0] == 'name' || $originalRule[0] == ['name']) && $originalRule[1] == 'required'){
                $rules[$index]['on'] = 'editing';
            }
        }

        $rules[] = ['groups_arr', 'safe'];
        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['groups_arr'] = Yii::t('app','Used in groups');
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
        $q = parent::find()->alias('d')->joinWith('monitoredGroupDictionaries as mgd');

        if(!empty($userId)) {
            $q->andWhere(['d.user_id' => $userId]);
        }

        if($this->load($params)){
            if($this->validate()){

                if(!empty($this->id)){
                    $q->andWhere(['d.id' => $this->id]);
                }

                if(!empty($this->name)){
                    $q->andWhere(['like','d.name', $this->name]);
                }

                if(!empty($this->words)){
                    $q->andWhere(['like','d.words', $this->words]);
                }

                if(!empty($this->groups_arr)){
                    $q->andWhere(['mgd.monitored_group_id' => $this->groups_arr]);
                }
            }
        }

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
     * Подготовить regex паттерн
     * @param $word
     * @return mixed|string
     */
    private function PrepareRegexPattern($word)
    {
        //Превратить слово в массив символов
        $wordArr = str_split($word);

        //Если перед началом не стоит "*" - добавить (\s|\b) в начало паттерна
        if($wordArr[0] != '*'){
            $word = '(\s|\b)'.$word;
        }
        //Если стоит "*" - не учитывать этот символ
        else{
            $word = substr($word,1,strlen($word)-1);
        }

        //Если в конце не стоит "*" - добавить (\s|\b) в конец паттерна
        if($wordArr[count($wordArr)-1] != '*' ){
            $word.= '(\s|\b)';
        }
        //Если стоит "*" - не учитывать этот символ
        else{
            $word = substr($word,0,strlen($word)-1);
        }

        //Заменить все "*" на [\s\S]
        $word = str_replace('*','[\s\S]',$word);
        //$word = preg_quote($word, '~');

        return "/{$word}/iu";
    }

    /**
     * Получить выдержку из текста содержащую найденное слово
     * @param $text
     * @param $match
     * @param $position
     * @param bool $highlight
     * @param int $sideLetters
     * @return mixed|string
     */
    private function GetExcerpt($text, $match, $position, $highlight = true, $sideLetters = 100)
    {
        $textRaw = strip_tags($text);
        $match = trim($match);

        $excerptStarts = max(0,($position - $sideLetters));
        $excerptEnds = min(mb_strlen($textRaw,'UTF-8'),($position + mb_strlen($match,'UTF-8') + $sideLetters));
        $excerpt = mb_substr($textRaw,$excerptStarts,($excerptEnds - $excerptStarts),'UTF-8');

        if($highlight){
            $excerpt = str_replace($match,"[{$match}]",$excerpt);
        }

        return $excerpt;
    }

    /**
     * Уведомить о стоп-словах
     * @param $groupId
     * @param $text
     * @param $postId
     * @param null $commentId
     */
    public function NotifyAboutWords($groupId, $text, $postId, $commentId = null)
    {
        //Получить массив стоп-слов
        $stopWords = explode("\n",$this->words);

        //Удалить пробелы в начале и конце текста
        $string = trim($text);

        //Пройтись по всем стоп-словам
        if(!empty($stopWords)){
            foreach ($stopWords as $word){

                //Удалить пробелы в начале и конце слова
                $word = trim($word);

                //Если это не пустое слово
                if(!empty($word) && $word != ''){

                    //Получить regex шаблон по слову
                    $pattern = $this->PrepareRegexPattern($word);

                    //Если найдно слово
                    if(preg_match($pattern, $string, $matches, PREG_OFFSET_CAPTURE)) {

                        //Получить выдежку
                        $fragment = $matches[0][0];
                        $position = $matches[0][1];
                        $excerpt = $this->GetExcerpt($string,$fragment,$position);

                        //Создать уведомление
                        $notification = new DictionaryNotification();
                        $notification -> dictionary_id = $this->id;
                        $notification -> post_id = $postId;
                        $notification -> comment_id = $commentId;
                        $notification -> word = $word;
                        $notification -> pattern = $pattern;
                        $notification -> excerpt = $excerpt;
                        $notification -> seen = (int)false;
                        $notification -> created_at = date('Y-m-d H:i:s');

                        //Если уведомление создано - создать задачи на отправку подписчикам словаря
                        if($notification->save()){
                            if(!empty($this->dictionarySubscribers)){
                                foreach ($this->dictionarySubscribers as $subscriber){
                                    if(!$subscriber->IsGroupIgnored($groupId)){
                                        $notificationTask = new DictionaryNotificationTask();
                                        $notificationTask -> notification_id = $notification->id;
                                        $notificationTask -> subscriber_id = $subscriber->id;
                                        $notificationTask -> save();
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}

<?php

namespace app\widgets;

use yii\base\Widget;
use Yii;

class MessagesWidget extends Widget
{
    public $title = 'You have {n} messages';
    public $translate = true;

    private $messages = [];
    private $count = 0;

    public function init()
    {
        parent::init();

        /*TODO: получить кол-во*/
        /*TODO: получить сами сообщения*/

        $this->title = $this->translate ? Yii::t('app',$this->title,['n' => $this->count]) : str_replace('{n}',$this->count,$this->title);
    }

    public function run()
    {
        return $this->render('messages',['title' => $this->title, 'messages' => $this->messages, 'count' => $this->count]);
    }
}
<?php
namespace app\components;

use yii\helpers\ArrayHelper;

/**
 * Class FacebookBotMessage
 * @package app\components
 */
class FacebookBotMessage
{
    /**
     * @var integer|null
     */
    protected $recipient = null;

    /**
     * @var string
     */
    protected $text = null;

    /**
     * @var null|array
     */
    protected $buttons = null;

    /**
     * Message constructor.
     *
     * @param $recipient
     * @param $text
     * @param $buttonItems
     */
    public function __construct($recipient, $text, $buttonItems = null)
    {
        $this->recipient = $recipient;
        $this->text = $text;

        if(!empty($buttonItems)){
            foreach ($buttonItems as $button){
                $this->buttons[] = [
                    'type' => ArrayHelper::getValue($button,'type','web_url'),
                    'url' => ArrayHelper::getValue($button,'url'),
                    'payload' => ArrayHelper::getValue($button,'command'),
                    'title' => ArrayHelper::getValue($button,'title')
                ];
            }
        }
    }

    /**
     * Get message data
     *
     * @return array
     */
    public function getData()
    {

        if(empty($this->buttons)){
            return [
                'recipient' =>  [
                    'id' => $this->recipient
                ],
                'message' => [
                    'text' => $this->text,
                ]
            ];
        }

        return [
            'recipient' =>  [
                'id' => $this->recipient
            ],
            'message' => [
                'attachment' => [
                    'type' => 'template',
                    'payload' => [
                        'template_type' => 'button',
                        'text' => $this->text,
                        'buttons' => $this->buttons,
                    ]
                ],
            ]
        ];
    }
}
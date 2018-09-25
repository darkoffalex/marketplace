<?php

namespace app\models\base;

use Yii;

use app\models\MonitoredGroupPostComment;
use app\models\Dictionary;
use app\models\MonitoredGroupPost;
use app\models\DictionaryNotificationTask;

/**
 * This is the base model class for table "dictionary_notification".
 *
 * @property int $id
 * @property int $dictionary_id
 * @property int $post_id
 * @property int $comment_id
 * @property string $word
 * @property string $pattern
 * @property string $excerpt
 * @property int $seen
 * @property string $created_at
 *
 * @property MonitoredGroupPostComment $comment
 * @property Dictionary $dictionary
 * @property MonitoredGroupPost $post
 * @property DictionaryNotificationTask[] $dictionaryNotificationTasks
 */
class DictionaryNotificationBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dictionary_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dictionary_id', 'post_id', 'comment_id', 'seen'], 'integer'],
            [['excerpt'], 'string'],
            [['created_at'], 'safe'],
            [['word', 'pattern'], 'string', 'max' => 255],
            [['post_id'], 'unique'],
            [['comment_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonitoredGroupPostComment::className(), 'targetAttribute' => ['comment_id' => 'id']],
            [['dictionary_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dictionary::className(), 'targetAttribute' => ['dictionary_id' => 'id']],
            [['post_id'], 'exist', 'skipOnError' => true, 'targetClass' => MonitoredGroupPost::className(), 'targetAttribute' => ['post_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'dictionary_id' => Yii::t('app', 'Dictionary ID'),
            'post_id' => Yii::t('app', 'Post ID'),
            'comment_id' => Yii::t('app', 'Comment ID'),
            'word' => Yii::t('app', 'Word'),
            'pattern' => Yii::t('app', 'Pattern'),
            'excerpt' => Yii::t('app', 'Excerpt'),
            'seen' => Yii::t('app', 'Seen'),
            'created_at' => Yii::t('app', 'Created At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComment()
    {
        return $this->hasOne(MonitoredGroupPostComment::className(), ['id' => 'comment_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionary()
    {
        return $this->hasOne(Dictionary::className(), ['id' => 'dictionary_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPost()
    {
        return $this->hasOne(MonitoredGroupPost::className(), ['id' => 'post_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDictionaryNotificationTasks()
    {
        return $this->hasMany(DictionaryNotificationTask::className(), ['notification_id' => 'id']);
    }
}

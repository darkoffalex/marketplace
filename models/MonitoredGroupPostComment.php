<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "monitored_group_post_comment".
 */
class MonitoredGroupPostComment extends \app\models\base\MonitoredGroupPostCommentBase
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = parent::rules();
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
}

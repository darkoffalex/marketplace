<?php

namespace app\models\base;

use Yii;


/**
 * This is the base model class for table "settings".
 *
 * @property int $id
 * @property string $name
 * @property string $value_text
 * @property int $value_numeric
 */
class SettingsBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['value_text'], 'string'],
            [['value_numeric'], 'integer'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'value_text' => Yii::t('app', 'Value Text'),
            'value_numeric' => Yii::t('app', 'Value Numeric'),
        ];
    }
}

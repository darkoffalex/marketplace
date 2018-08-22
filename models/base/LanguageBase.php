<?php

namespace app\models\base;

use Yii;


/**
 * This is the base model class for table "language".
 *
 * @property int $id
 * @property string $name
 * @property string $self_name
 * @property string $prefix
 * @property int $is_default
 * @property int $priority
 * @property int $status_id
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 */
class LanguageBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'self_name', 'prefix'], 'required'],
            [['is_default', 'priority', 'status_id', 'created_by_id', 'updated_by_id'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'self_name'], 'string', 'max' => 255],
            [['prefix'], 'string', 'max' => 3],
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
            'self_name' => Yii::t('app', 'Self Name'),
            'prefix' => Yii::t('app', 'Prefix'),
            'is_default' => Yii::t('app', 'Is Default'),
            'priority' => Yii::t('app', 'Priority'),
            'status_id' => Yii::t('app', 'Status ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }
}

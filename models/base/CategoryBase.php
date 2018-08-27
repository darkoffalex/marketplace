<?php

namespace app\models\base;

use Yii;


/**
 * This is the base model class for table "category".
 *
 * @property int $id
 * @property int $parent_category_id
 * @property string $name
 * @property int $status_id
 * @property int $priority
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 */
class CategoryBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parent_category_id', 'status_id', 'priority', 'created_by_id', 'updated_by_id'], 'integer'],
            [['name'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
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
            'parent_category_id' => Yii::t('app', 'Parent Category ID'),
            'name' => Yii::t('app', 'Name'),
            'status_id' => Yii::t('app', 'Status ID'),
            'priority' => Yii::t('app', 'Priority'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }
}

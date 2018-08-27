<?php

namespace app\models\base;

use Yii;


/**
 * This is the base model class for table "country".
 *
 * @property int $id
 * @property string $name
 * @property string $domain_alias
 * @property int $status_id
 * @property int $priority
 * @property string $flag_filename
 * @property int $clicks
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 */
class CountryBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'country';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'domain_alias'], 'required'],
            [['status_id', 'priority', 'clicks', 'created_by_id', 'updated_by_id'], 'integer'],
            [['description'], 'string'],
            [['created_at', 'updated_at'], 'safe'],
            [['name', 'domain_alias', 'flag_filename'], 'string', 'max' => 255],
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
            'domain_alias' => Yii::t('app', 'Domain Alias'),
            'status_id' => Yii::t('app', 'Status ID'),
            'priority' => Yii::t('app', 'Priority'),
            'flag_filename' => Yii::t('app', 'Flag Filename'),
            'clicks' => Yii::t('app', 'Clicks'),
            'description' => Yii::t('app', 'Description'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
        ];
    }
}

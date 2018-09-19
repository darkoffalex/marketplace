<?php

namespace app\models\base;

use Yii;

use app\models\Category;
use app\models\Marketplace;
use app\models\MarketplaceTariffPrice;
use app\models\User;
use app\models\PosterImage;

/**
 * This is the base model class for table "poster".
 *
 * @property int $id
 * @property int $marketplace_id
 * @property int $category_id
 * @property int $user_id
 * @property int $country_id
 * @property int $status_id
 * @property string $title
 * @property string $description
 * @property string $phone
 * @property string $whats_app
 * @property string $title_approved
 * @property string $description_approved
 * @property string $phone_approved
 * @property string $whats_app_approved
 * @property string $admin_post_text
 * @property string $admin_post_image_filename
 * @property string $paid_at
 * @property int $period_seconds
 * @property int $period_free_seconds
 * @property string $created_at
 * @property string $updated_at
 * @property int $created_by_id
 * @property int $updated_by_id
 * @property int $marketplace_tariff_id
 * @property int $approved_by_ga
 * @property int $approved_by_sa
 * @property int $published
 * @property string $refuse_reason
 * @property string $admin_post_time
 * @property int $admin_post_time_approve_status
 * @property string $admin_post_disapprove_reason
 *
 * @property Category $category
 * @property Marketplace $marketplace
 * @property MarketplaceTariffPrice $marketplaceTariff
 * @property User $user
 * @property PosterImage[] $posterImages
 */
class PosterBase extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'poster';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['marketplace_id', 'category_id', 'user_id', 'country_id', 'status_id', 'period_seconds', 'period_free_seconds', 'created_by_id', 'updated_by_id', 'marketplace_tariff_id', 'approved_by_ga', 'approved_by_sa', 'published', 'admin_post_time_approve_status'], 'integer'],
            [['description', 'description_approved', 'admin_post_text', 'refuse_reason', 'admin_post_disapprove_reason'], 'string'],
            [['paid_at', 'created_at', 'updated_at', 'admin_post_time'], 'safe'],
            [['title', 'phone', 'whats_app', 'title_approved', 'phone_approved', 'whats_app_approved', 'admin_post_image_filename'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
            [['marketplace_id'], 'exist', 'skipOnError' => true, 'targetClass' => Marketplace::className(), 'targetAttribute' => ['marketplace_id' => 'id']],
            [['marketplace_tariff_id'], 'exist', 'skipOnError' => true, 'targetClass' => MarketplaceTariffPrice::className(), 'targetAttribute' => ['marketplace_tariff_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'marketplace_id' => Yii::t('app', 'Marketplace ID'),
            'category_id' => Yii::t('app', 'Category ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'country_id' => Yii::t('app', 'Country ID'),
            'status_id' => Yii::t('app', 'Status ID'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'phone' => Yii::t('app', 'Phone'),
            'whats_app' => Yii::t('app', 'Whats App'),
            'title_approved' => Yii::t('app', 'Title Approved'),
            'description_approved' => Yii::t('app', 'Description Approved'),
            'phone_approved' => Yii::t('app', 'Phone Approved'),
            'whats_app_approved' => Yii::t('app', 'Whats App Approved'),
            'admin_post_text' => Yii::t('app', 'Admin Post Text'),
            'admin_post_image_filename' => Yii::t('app', 'Admin Post Image Filename'),
            'paid_at' => Yii::t('app', 'Paid At'),
            'period_seconds' => Yii::t('app', 'Period Seconds'),
            'period_free_seconds' => Yii::t('app', 'Period Free Seconds'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_by_id' => Yii::t('app', 'Created By ID'),
            'updated_by_id' => Yii::t('app', 'Updated By ID'),
            'marketplace_tariff_id' => Yii::t('app', 'Marketplace Tariff ID'),
            'approved_by_ga' => Yii::t('app', 'Approved By Ga'),
            'approved_by_sa' => Yii::t('app', 'Approved By Sa'),
            'published' => Yii::t('app', 'Published'),
            'refuse_reason' => Yii::t('app', 'Refuse Reason'),
            'admin_post_time' => Yii::t('app', 'Admin Post Time'),
            'admin_post_time_approve_status' => Yii::t('app', 'Admin Post Time Approve Status'),
            'admin_post_disapprove_reason' => Yii::t('app', 'Admin Post Disapprove Reason'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplace()
    {
        return $this->hasOne(Marketplace::className(), ['id' => 'marketplace_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMarketplaceTariff()
    {
        return $this->hasOne(MarketplaceTariffPrice::className(), ['id' => 'marketplace_tariff_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPosterImages()
    {
        return $this->hasMany(PosterImage::className(), ['poster_id' => 'id']);
    }
}

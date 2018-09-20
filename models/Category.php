<?php

namespace app\models;

use app\helpers\Help;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\helpers\Constants;
/**
 * This is the model class for table "category".
 * @property Category $parent
 * @property Category[] $children
 * @property Category[] $childrenActive
 */
class Category extends \app\models\base\CategoryBase
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

    /**
     * Связь с категорией-родителем
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class,['id' => 'parent_category_id']);
    }

    /**
     * Связь с категориями-детьми
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Category::class,['parent_category_id' => 'id'])->orderBy('priority ASC');
    }

    /**
     * Связь с категориями-детьми (активными)
     * @return \yii\db\ActiveQuery
     */
    public function getChildrenActive()
    {
        return $this->hasMany(Category::class,['parent_category_id' => 'id'])->where(['status_id' => Constants::STATUS_ENABLED])->orderBy('priority ASC');
    }

    /**
     * Рекурсивно удаляет все под-категории и саму категорию
     * @throws \Exception
     */
    public function recursiveDelete()
    {
        if(count($this->children) > 0){
            foreach($this->children as $child){
                $child->recursiveDelete();
            }
        }

        $this->delete();
    }

    /**
     * Получить уровень вложенности категории
     * @return int
     */
    public function getDepth()
    {
        $depthLevel = 0;
        $checkCategory = $this;

        while (!empty($checkCategory->parent)){
            $depthLevel++;
            $checkCategory = $checkCategory->parent;
        }

        return $depthLevel;
    }

    /**
     * Получить список категорий осортированных в порядке рекурсивной вложенности
     * @param null $rootCat
     * @param bool $activeOnly
     * @return array
     */
    public static function getRecursiveCats(&$rootCat = null, $activeOnly = false)
    {
        $result = [];

        if(empty($rootCat) || is_numeric($rootCat)){
            $q = self::find()->orderBy('priority ASC')->where(['parent_category_id' => (int)$rootCat]);
            if($activeOnly) $q->where(['status_id' => Constants::STATUS_ENABLED]);
            if($activeOnly) $q->with(['childrenActive.childrenActive.parent','childrenActive.parent']);
            else $q->with(['children.children.parent','children.parent']);
            $items = $q->all();
        }else{
            /* @var $rootCat self */
            $items = $activeOnly ? $rootCat->childrenActive : $rootCat->children;
        }

        foreach($items as $category){
            $result[] = $category;

            if(!empty($category->children)){
                $result = array_merge($result,self::getRecursiveCats($category, $activeOnly));
            }
        }

        return $result;
    }

    /**
     * Получить ссылку на страницу категории
     * @param null $country
     * @param bool $scheme
     * @return string
     */
    public function getUrl($country = null, $scheme = false)
    {
        $country = !empty($country) ? $country : Yii::$app->request->get('subSubDomain');
        return Url::to(['country/category','subSubDomain' => $country, 'id' => $this->id, 'title' => Help::slug(Yii::t('app',$this->name))],$scheme);
    }

    /**
     * Получить массив для breadcrumbs
     * @param null|Country $country
     * @param null|Marketplace $marketplace
     * @return array
     */
    public function getBreadCrumbs($country = null, $marketplace = null)
    {
        $result = [];

        /* @var $country Country */
        if(empty($marketplace)){
            if(!empty($country)){
                $result[] = ['label' => Yii::t('app',$country->name), 'url' => $country->getUrl()];
            }
        }else{
            $result[] = ['label' => Yii::t('app',$marketplace->name), 'url' => $marketplace->getLink()];
        }

        $items = [];
        $cat = $this;
        while (!empty($cat->parent)){
            $cat = $cat->parent;
            $items[] = ['label' => Yii::t('app',$cat->name), 'url' => !empty($marketplace) ? $marketplace->getCategoryLink($cat) : $cat->getUrl()];
        }

        $items = array_reverse($items);
        $items[] = Yii::t('app',$this->name);

        return ArrayHelper::merge($result,$items);
    }

    /**
     * Сформировать запрос на получение опубликованных объявлений для конкретного маркетплейса (и текущей категории)
     * @param $marketplaceId
     * @return \yii\db\ActiveQuery
     */
    public function getPublishedPostersForMarketplace($marketplaceId)
    {
        $q = Poster::find()->alias('p')->joinWith('marketplaceTariff.tariff t')->where([
            'p.marketplace_id' => $marketplaceId,
            'p.category_id' => $this->id,
            'p.status_id' => Constants::STATUS_ENABLED,
            'p.published' => (int)true,
        ])->andFilterWhere([
            'or',
            '(p.paid_at + INTERVAL period_seconds SECOND) > NOW()',
            'p.paid_at < NOW() and t.special_type = '.Constants::TARIFF_SUB_TYPE_ADMIN_POST
        ])->distinct();
        return $q;
    }
}

<?php

namespace app\models;

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
}

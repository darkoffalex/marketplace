<?php
namespace app\helpers;

use yii\db\ActiveRecord;

/**
 * Хелпер. Предназначен для упрощения смены приортитетов (порядка следования) разлчиных элементов в таблицах.
 *
 * @copyright 	2014 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\helpers
 */
class Sort
{
    /**
     * Находит  в базе два элемента по первичному ключу (обычно ID) и меняет их местами (обменивает значение их priority между собой)
     * @param $id1
     * @param $id2
     * @param $className
     * @param bool $update
     * @return  array | bool | ActiveRecord[]
     */
    public static function SwapById($id1,$id2,$className,$update = true)
    {
        /* @var $className ActiveRecord */
        /* @var $objItem1 ActiveRecord*/
        /* @var $objItem2 ActiveRecord*/

        $objItem1 = $className::findOne($id1);
        $objItem2 = $className::findOne($id2);

        if($objItem1 != null && $objItem2 != null)
        {
            $p1 = $objItem1->priority;
            $objItem1->priority = $objItem2->priority;
            $objItem2->priority = $p1;

            if($update)
            {
                $objItem1->update();
                $objItem2->update();
            }

            return array($objItem1,$objItem2);
        }

        return false;
    }


    /**
     * Обменивает значение priority двух элементов в базе между собой
     * @param ActiveRecord $object1
     * @param ActiveRecord $object2
     * @param bool $update
     * @return array | bool | ActiveRecord[]
     */
    public  static function Swap($object1, $object2, $update = true)
    {
        /* @var $object1 ActiveRecord*/
        /* @var $object2 ActiveRecord*/

        //if objects not null
        if($object1 != null && $object2 != null)
        {
            //store first object's priority
            $pr1 = $object1->priority;
            //assign to first object priority pf second
            $object1->priority = $object2->priority;
            //assign to second object stored first object's priority
            $object2->priority = $pr1;

            if($update)
            {
                //update both
                $object1->update();
                $object2->update();
            }

            return array($object1,$object2);
        }

        return false;
    }


    /**
     * Вычисляет каким должен быть приоритет у нового элемента в сортируемом списке (если в таблице по очереди идет 3,5,9 то будет возвращено 10)
     * @param $className
     * @param array $condition
     * @param string $field
     * @return int
     */
    public static function GetNextPriority($className,$condition = array(),$field = 'priority')
    {
        /*TODO: Идиотский алгоритм, нужно переписать*/

        /* @var $className ActiveRecord */
        /* @var $itemsAll ActiveRecord[] */

        if(!empty($condition))
        {
            $itemsAll = $className::find()->where($condition)->all();
        }
        else
        {
            $itemsAll = $className::find()->all();
        }

        $max = 0;
        foreach($itemsAll as $item)
        {
            if($item->$field > $max)
            {
                $max = $item->$field;
            }
        }

        return $max + 1;
    }


    /**
     * Сдвигает элемент вверху или вниз относительно остальных (меняя priority текущего и рядом стоящих)
     * @param $movingObject ActiveRecord
     * @param string $direction
     * @param string $className
     * @param array $condition
     * @param string $order_by
     */
    public static function Move($movingObject,$direction,$className,$condition = array(),$order_by = 'priority ASC')
    {
        /* @var $className ActiveRecord */
        if(!empty($condition))
        {
            $all = $className::find()->where($condition)->orderBy($order_by)->all();
        }
        else
        {
            $all = $className::find()->orderBy($order_by)->all();
        }

        foreach($all as $index => $obj)
        {
            if($obj == $movingObject)
            {
                if($direction == 'up' && isset($all[$index - 1]))
                {
                    self::Swap($all[$index-1],$obj);
                }

                if($direction == 'down' && isset($all[$index + 1]))
                {
                    self::Swap($all[$index+1],$obj);
                }
            }
        }
    }
}
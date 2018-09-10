<?php
namespace app\commands;

use app\helpers\Constants;
use app\models\Poster;
use yii\console\Controller;
use app\helpers\ConsoleHelper;

/**
 * Controls system functions (like cleaning cache and temporary items, and etc.)
 *
 * @copyright 	2018 Alex Nem
 * @link 		https://github.com/darkoffalex
 * @author 		Alex Nem
 *
 * @package app\commands
 */
class SysController extends Controller
{
    public $eld = 10;
    public $cc = true;
    public $ct = true;
    public function options($actionID)
    {
        return ['cc', 'ct', 'eld'];
    }

    /**
     * Переопределение before-action метода, для смены временной зоны на UTC (фейсбук работает с датами UTC)
     * @param \yii\base\Action $action
     * @return bool
     */
    public function beforeAction($action)
    {
        //Установка врменной зоны и синхронизация её с БД
        //ConsoleHelper::setTimezone('UTC');

        //Вызов родительского метода
        return parent::beforeAction($action);
    }

    /**
     * Cleaning cache or temporary items
     */
    public function actionClean()
    {
        $pid = ConsoleHelper::processStart();

        if($this->cc){
            echo "Cleaning the cache... ";
            //TODO:очистка кеша и runtime директории
            echo "DONE\n";
        }

        if($this->ct){
            echo "Cleaning the trash... \n";

            $trashPostersQnt = Poster::find()
                ->where(['status_id' => Constants::STATUS_TEMPORARY])
                ->andWhere('created_at < (NOW() - INTERVAL :days DAY)',['days' => (int)$this->eld])
                ->count();

            echo "Found {$trashPostersQnt} trash posters... ";

            if(!empty($trashPostersQnt)){
                Poster::deleteAll(['and','status_id' => Constants::STATUS_TEMPORARY, 'created_at < (NOW() - INTERVAL :days DAY)'],['days' => (int)$this->eld]);
                echo "DELETED\n";
            }
            else{
                echo "NOTHING TO DELETE\n";
            }

        }

        ConsoleHelper::processEnd($pid);
    }
}

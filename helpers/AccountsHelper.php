<?php
namespace app\helpers;

use app\models\MoneyAccount;
use app\models\MoneyTransaction;
use Yii;

class AccountsHelper
{
    /**
     * Системный счет для поступления средств, сюда поступают все опоаты
     * @var MoneyAccount
     */
    private static $incomeSysAccount_;

    /**
     * Системный счет для вывода средств, на этот псевдосчет происходит вывод денег (когда выводятся реальные деньги)
     * @var MoneyAccount
     */
    private static $outgoSysAccount_;

    /**
     * Получить системный счет для поступлений
     * @return MoneyAccount|array|null|\yii\db\ActiveRecord
     */
    public static function getSysIncomeAccount()
    {
        self::$incomeSysAccount_ = MoneyAccount::find()
            ->where('user_id IS NULL')
            ->andWhere(['account_type_id' => Constants::SYSTEM_INCOME_ACCOUNT])
            ->one();

        if(empty(self::$incomeSysAccount_)){
            self::$incomeSysAccount_ = new MoneyAccount();
            self::$incomeSysAccount_ -> user_id = null;
            self::$incomeSysAccount_ -> account_type_id = Constants::SYSTEM_INCOME_ACCOUNT;
            self::$incomeSysAccount_ -> amount = 0;
            self::$incomeSysAccount_ -> created_at = date('Y-m-d H:i:s');
            self::$incomeSysAccount_ -> updated_at = date('Y-m-d H:i:s');
            self::$incomeSysAccount_ -> created_by_id = 0;
            self::$incomeSysAccount_ -> updated_by_id = 0;
            self::$incomeSysAccount_ -> save();
        }

        return self::$incomeSysAccount_;
    }

    /**
     * Получить системный счет для вывода
     * @return MoneyAccount
     */
    public static function getSysOutgoAccount()
    {
        self::$outgoSysAccount_ = MoneyAccount::find()
            ->where('user_id IS NULL')
            ->andWhere(['account_type_id' => Constants::SYSTEM_OUTGO_ACCOUNT])
            ->one();

        if(empty(self::$outgoSysAccount_)){
            self::$outgoSysAccount_ = new MoneyAccount();
            self::$outgoSysAccount_ -> user_id = null;
            self::$outgoSysAccount_ -> account_type_id = Constants::SYSTEM_OUTGO_ACCOUNT;
            self::$outgoSysAccount_ -> amount = 0;
            self::$outgoSysAccount_ -> created_at = date('Y-m-d H:i:s');
            self::$outgoSysAccount_ -> updated_at = date('Y-m-d H:i:s');
            self::$outgoSysAccount_ -> created_by_id = 0;
            self::$outgoSysAccount_ -> updated_by_id = 0;
            self::$outgoSysAccount_ -> save();
        }

        return self::$outgoSysAccount_;
    }

    /**
     * Совершение операции
     * @param $srcAccountId
     * @param $dstAccountId
     * @param $amount
     * @param int $operationStatus
     * @param int $type
     * @param null|int $webPaymentTypeId
     * @param null|string $description
     * @return MoneyTransaction|null
     */
    public static function makeOperation(
        $srcAccountId,
        $dstAccountId,
        $amount,
        $operationStatus = Constants::PAYMENT_STATUS_NEW,
        $type = Constants::PAYMENT_INTERNAL_INITIATED,
        $webPaymentTypeId = null,
        $description = null)
    {
        //TODO: использовать транзакции БД

        $transaction = new MoneyTransaction();
        $transaction -> from_account_id = $srcAccountId;
        $transaction -> to_account_id = $dstAccountId;
        $transaction -> amount = $amount;
        $transaction -> description = $description;
        $transaction -> status_id = $operationStatus;
        $transaction -> web_payment_type_id = $webPaymentTypeId;
        $transaction -> type_id = $type;
        $transaction -> created_by_id = Yii::$app->user->id;
        $transaction -> updated_by_id = Yii::$app->user->id;
        $transaction -> created_at = date('Y-m-d H:i:s',time());
        $transaction -> updated_at = date('Y-m-d H:i:s',time());

        if($transaction->save()){
            if($operationStatus != Constants::PAYMENT_STATUS_CANCELED) {
                self::moveMoney($transaction->from_account_id,$transaction->to_account_id,$amount);
            }
            return $transaction;
        }

        return null;
    }

    /**
     * Перевести деньги со счета на счет (без создания транзакций)
     * @param $srcAccountId
     * @param $dstAccountId
     * @param $amountInCents
     */
    public static function moveMoney($srcAccountId,$dstAccountId,$amountInCents)
    {
        /* @var $srcAccount MoneyAccount */
        /* @var $dstAccount MoneyAccount */
        $srcAccount = MoneyAccount::findOne((int)$srcAccountId);
        $dstAccount = MoneyAccount::findOne((int)$dstAccountId);

        if(!empty($srcAccount) && !empty($dstAccount)){

            $srcAccount->amount -= $amountInCents;
            $srcAccount->updated_at = date('Y-m-d H:i:s',time());
            $srcAccount->updated_by_id = Yii::$app->user->id;
            $srcAccount->save();

            $dstAccount->amount += $amountInCents;
            $dstAccount->updated_at = date('Y-m-d H:i:s',time());
            $dstAccount->updated_by_id = Yii::$app->user->id;
            $dstAccount->save();

            //Если это зачисление на аккаунт админа группы - добавить к общему доходу
            if($dstAccount->account_type_id == Constants::GROUP_ADMIN_ACCOUNT){
                $dstAccount->user->total_agr_income += $amountInCents;
                $dstAccount->user->update();
            }

            //Если это списание с аккаунта админа группы - вычесть из общего дохода
            if($srcAccount->account_type_id == Constants::GROUP_ADMIN_ACCOUNT && $dstAccount->account_type_id != Constants::SYSTEM_OUTGO_ACCOUNT){
                $srcAccount->user->total_agr_income -= $amountInCents;
                $srcAccount->user->update();
            }
        }
    }
}
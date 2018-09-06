<?php

use yii\db\Migration;

/**
 * Handles the creation of table `marketplace_tariff_price`.
 * Has foreign keys to the tables:
 *
 * - `tariff`
 * - `marketplace`
 */
class m180906_145049_create_marketplace_tariff_price_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('marketplace_tariff_price', [
            'id' => $this->primaryKey(),
            'tariff_id' => $this->integer(),
            'marketplace_id' => $this->integer(),
            'price' => $this->integer()->defaultValue(0),
        ]);

        // creates index for column `tariff_id`
        $this->createIndex(
            'idx-marketplace_tariff_price-tariff_id',
            'marketplace_tariff_price',
            'tariff_id'
        );

        // add foreign key for table `tariff`
        $this->addForeignKey(
            'fk-marketplace_tariff_price-tariff_id',
            'marketplace_tariff_price',
            'tariff_id',
            'tariff',
            'id',
            'CASCADE',
            'NO ACTION'
        );

        // creates index for column `marketplace_id`
        $this->createIndex(
            'idx-marketplace_tariff_price-marketplace_id',
            'marketplace_tariff_price',
            'marketplace_id'
        );

        // add foreign key for table `marketplace`
        $this->addForeignKey(
            'fk-marketplace_tariff_price-marketplace_id',
            'marketplace_tariff_price',
            'marketplace_id',
            'marketplace',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `tariff`
        $this->dropForeignKey(
            'fk-marketplace_tariff_price-tariff_id',
            'marketplace_tariff_price'
        );

        // drops index for column `tariff_id`
        $this->dropIndex(
            'idx-marketplace_tariff_price-tariff_id',
            'marketplace_tariff_price'
        );

        // drops foreign key for table `marketplace`
        $this->dropForeignKey(
            'fk-marketplace_tariff_price-marketplace_id',
            'marketplace_tariff_price'
        );

        // drops index for column `marketplace_id`
        $this->dropIndex(
            'idx-marketplace_tariff_price-marketplace_id',
            'marketplace_tariff_price'
        );

        $this->dropTable('marketplace_tariff_price');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles adding tariff to table `poster`.
 * Has foreign keys to the tables:
 *
 * - `marketplace_tariff_price`
 */
class m180906_153126_add_tariff_column_to_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('poster', 'marketplace_tariff_id', $this->integer());

        // creates index for column `marketplace_tariff_id`
        $this->createIndex(
            'idx-poster-marketplace_tariff_id',
            'poster',
            'marketplace_tariff_id'
        );

        // add foreign key for table `marketplace_tariff_price`
        $this->addForeignKey(
            'fk-poster-marketplace_tariff_id',
            'poster',
            'marketplace_tariff_id',
            'marketplace_tariff_price',
            'id',
            'SET NULL',
            'NO ACTION'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `marketplace_tariff_price`
        $this->dropForeignKey(
            'fk-poster-marketplace_tariff_id',
            'poster'
        );

        // drops index for column `marketplace_tariff_id`
        $this->dropIndex(
            'idx-poster-marketplace_tariff_id',
            'poster'
        );

        $this->dropColumn('poster', 'marketplace_tariff_id');
    }
}

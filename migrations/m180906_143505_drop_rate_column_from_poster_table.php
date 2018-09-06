<?php

use yii\db\Migration;

/**
 * Handles dropping rate from table `poster`.
 * Has foreign keys to the tables:
 *
 * - `rate`
 */
class m180906_143505_drop_rate_column_from_poster_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // drops foreign key for table `rate`
        $this->dropForeignKey(
            'fk-poster-rate_id',
            'poster'
        );

        // drops index for column `rate_id`
        $this->dropIndex(
            'idx-poster-rate_id',
            'poster'
        );

        $this->dropColumn('poster', 'rate_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('poster', 'rate_id', $this->integer());

        // creates index for column `rate_id`
        $this->createIndex(
            'idx-poster-rate_id',
            'poster',
            'rate_id'
        );

        // add foreign key for table `rate`
        $this->addForeignKey(
            'fk-poster-rate_id',
            'poster',
            'rate_id',
            'rate',
            'id',
            'CASCADE',
            'NO ACTION'
        );
    }
}

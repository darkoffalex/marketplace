<?php

use yii\db\Migration;

/**
 * Handles adding cv_id to table `marketplace`.
 * Has foreign keys to the tables:
 *
 * - `cv`
 */
class m180920_135912_add_cv_id_column_to_marketplace_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('marketplace', 'cv_id', $this->integer());

        // creates index for column `cv_id`
        $this->createIndex(
            'idx-marketplace-cv_id',
            'marketplace',
            'cv_id'
        );

        // add foreign key for table `cv`
        $this->addForeignKey(
            'fk-marketplace-cv_id',
            'marketplace',
            'cv_id',
            'cv',
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
        // drops foreign key for table `cv`
        $this->dropForeignKey(
            'fk-marketplace-cv_id',
            'marketplace'
        );

        // drops index for column `cv_id`
        $this->dropIndex(
            'idx-marketplace-cv_id',
            'marketplace'
        );

        $this->dropColumn('marketplace', 'cv_id');
    }
}

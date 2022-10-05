<?php

use yii\db\Migration;

/**
 * Class m200131_030251_alter_part_packing_table
 */
class m200131_030251_alter_part_packing_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('{{%part_packing}}', 'expandable', 'returnable');
        $this->renameColumn('{{%part_packing}}', 'level1_pack_id', 'pack_id');

        $this->dropColumn('{{%part_packing}}','net_weight');
        $this->dropColumn('{{%part_packing}}','gross_weight');

        $this->dropColumn('{{%part_packing}}','level2_pack_id');
        $this->dropColumn('{{%part_packing}}','pack_pack_qty');
        $this->dropColumn('{{%part_packing}}','full_gross_weight');

        $this->alterColumn('{{%part_packing}}','pack_id', $this->integer(11)->notNull());
        $this->createIndex('idx_unique_part_id_pack_id', '{{%part_packing}}', ['part_id','pack_id'], true);
        $this->addForeignKey('fk_part_packing_pack_id',
            '{{%part_packing}}', 'pack_id',
            '{{%pack}}', 'id',
            'RESTRICT', 'RESTRICT'
        );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_part_packing_pack_id', '{{%part_packing}}');
        $this->dropIndex('idx_unique_part_id_pack_id', '{{%part_packing}}');


        $this->addColumn('{{%part_packing}}','level2_pack_id', $this->integer(11));
        $this->addColumn('{{%part_packing}}','pack_pack_qty', $this->integer(11)->unsigned());
        $this->addColumn('{{%part_packing}}','full_gross_weight', $this->decimal(20, 5)->null()->defaultValue('1.00000'));

        $this->addColumn('{{%part_packing}}','gross_weight', $this->decimal(20, 5)->null()->defaultValue('1.00000'));
        $this->addColumn('{{%part_packing}}','net_weight', $this->decimal(20, 5)->null()->defaultValue('1.00000'));

        $this->renameColumn('{{%part_packing}}', 'pack_id', 'level1_pack_id');
        $this->renameColumn('{{%part_packing}}', 'returnable', 'expandable');
    }
}

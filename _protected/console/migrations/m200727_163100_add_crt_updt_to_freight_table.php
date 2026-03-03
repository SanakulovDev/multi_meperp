<?php
use yii\db\Expression;
use yii\db\Migration;

class m200727_163100_add_crt_updt_to_freight_table extends Migration {

  public function safeUp() {
    $this->addColumn('freight_invoice', 'updated_by', $this->integer(11)->defaultValue(null)->after('delivery_term_id'));
    $this->addColumn('freight_invoice', 'updated_at', $this->integer(11)->defaultValue(null)->after('delivery_term_id'));
    $this->addColumn('freight_invoice', 'created_by', $this->integer(11)->notNull()->defaultValue(null)->after('delivery_term_id'));
    $this->addColumn('freight_invoice', 'created_at', $this->integer(11)->notNull()->defaultValue(time())->after('delivery_term_id'));
    $this->addForeignKey('fk_freight_invoice_created_by',
      '{{%freight_invoice}}', 'created_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
    $this->addForeignKey('fk_freight_invoice_updated_by',
      '{{%freight_invoice}}', 'updated_by',
      '{{%user}}', 'id',
      'RESTRICT', 'RESTRICT'
    );
  }

  public function safeDown() {
    $this->dropForeignKey('fk_freight_invoice_created_by', '{{%freight_invoice}}');
    $this->dropForeignKey('fk_freight_invoice_updated_by', '{{%freight_invoice}}');
    $this->dropColumn('freight_invoice', 'created_at');
    $this->dropColumn('freight_invoice', 'created_by');
    $this->dropColumn('freight_invoice', 'updated_at');
    $this->dropColumn('freight_invoice', 'updated_by');
  }

}


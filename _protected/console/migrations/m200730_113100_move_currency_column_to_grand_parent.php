<?php
use yii\db\Expression;
use yii\db\Migration;

class m200730_113100_move_currency_column_to_grand_parent extends Migration {

  public function safeUp() {
    $this->execute("SET foreign_key_checks = 0;");
    $this->execute("START TRANSACTION;");

    $this->dropForeignKey('frk-freight_invoice_detail_cost-currency_id', 'freight_invoice_detail_cost');
    $this->dropColumn('freight_invoice_detail_cost', 'currency_id');

    $this->addColumn('freight_invoice', 'currency_id', $this->integer(11)->notNull()->after('delivery_term_id'));
    $this->addForeignKey('frk-freight_invoice-currency_id', 'freight_invoice', 'currency_id', 'currency', 'id');

    $this->execute("COMMIT;");
    $this->execute("SET foreign_key_checks = 1;");
  }

  public function safeDown() {
    $this->execute("SET foreign_key_checks = 0;");
    $this->execute("START TRANSACTION;");

    $this->addColumn('freight_invoice_detail_cost', 'currency_id', $this->integer(11)->notNull());
    $this->addForeignKey('frk-freight_invoice_detail_cost-currency_id', 'freight_invoice_detail_cost', 'currency_id', 'currency', 'id');

    $this->dropForeignKey('frk-freight_invoice-currency_id', 'freight_invoice');
    $this->dropColumn('freight_invoice', 'currency_id');

    $this->execute("COMMIT;");
    $this->execute("SET foreign_key_checks = 1;");
  }

}


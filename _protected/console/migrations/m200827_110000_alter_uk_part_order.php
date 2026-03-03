<?php
use yii\db\Migration;

class m200827_110000_alter_uk_part_order extends Migration {

  public function safeUp() {
    $this->dropIndex('part_order-order_no-unique', '{{%part_order}}');
    $this->createIndex(
      'uk-part_order-order_no-order_dt-contract',
      '{{%part_order}}',
      ['order_no', 'iss_dt', 'contract_id'],
      true
    );
  }

  public function safeDown() {
    $this->dropIndex('uk-part_order-order_no-order_dt-contract', '{{%part_order}}');
    $this->createIndex(
      'part_order-order_no-unique',
      '{{%part_order}}',
      ['order_no'],
      true
    );
  }

}

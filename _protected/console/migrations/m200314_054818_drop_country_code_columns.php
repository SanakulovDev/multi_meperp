<?php

use yii\db\Migration;

/**
 * Class m200314_054818_drop_country_code_columns
 */
class m200314_054818_drop_country_code_columns extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->dropColumn('{{%supplier}}', 'country');
      $this->dropColumn('{{%supplier}}', 'country_code');
      $this->execute($this->updateQuery('supplier'));

      $this->dropColumn('{{%customer}}', 'country');
      $this->dropColumn('{{%customer}}', 'country_code');
      $this->execute($this->updateQuery('customer'));
    }

    public function updateQuery($table) {
      return "UPDATE `$table` JOIN country_code ON country_code.alpha_2='UZ'
                SET `country_code_id` = country_code.id 
                WHERE country_code_id IS NULL";
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
      $this->addColumn('{{%customer}}', 'country', $this->string(100)->null()->after('postal'));
      $this->addColumn('{{%customer}}', 'country_code', $this->string(10)->null()->after('country'));

      $this->addColumn('{{%supplier}}', 'country', $this->string(100)->null()->after('postal'));
      $this->addColumn('{{%supplier}}', 'country_code', $this->string(30)->null()->after('country'));
    }
}

<?php
	use yii\db\Migration;

	class m200410_103500_add_deleted_column_to_part_part_version_table extends Migration{

	  public function safeUp(){
			$this->addColumn(
			  '{{%part_part_version}}',
        'deleted_by',
        $this->integer(11)->null()->defaultValue(null)
      );
			$this->addColumn(
			  '{{%part_part_version}}',
        'deleted_at',
        $this->integer(11)->null()->defaultValue(null)
      );
		}
	}

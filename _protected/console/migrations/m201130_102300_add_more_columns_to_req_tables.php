<?php
	use yii\db\Migration;

	class m201130_102300_add_more_columns_to_req_tables extends Migration{
		/**
			* {@inheritdoc}
			*/
		public function safeUp(){

			for($i = 72; $i <= 111; $i++){
				$this->addColumn('{{%req_detail_wide_t}}', 'col'.$i, $this->decimal(20, 5)->null()->defaultValue(null));
				$this->addColumn('{{%req_detail_plan_t}}', 'col'.$i, $this->decimal(20, 5)->null()->defaultValue(null));
				$this->addColumn('{{%req_detail_wide}}', 'col'.$i, $this->decimal(20, 5)->null()->defaultValue(null));
				$this->addColumn('{{%req_detail_plan}}', 'col'.$i, $this->decimal(20, 5)->null()->defaultValue(null));
			}

		}

		/**
			* {@inheritdoc}
			*/
		public function safeDown(){

			for($i = 72; $i <= 111; $i++){
				$this->dropColumn('{{%req_detail_wide_t}}', 'col'.$i);
				$this->dropColumn('{{%req_detail_plan_t}}', 'col'.$i);
				$this->dropColumn('{{%req_detail_wide}}', 'col'.$i);
				$this->dropColumn('{{%req_detail_plan}}', 'col'.$i);
			}
			
		}
	}

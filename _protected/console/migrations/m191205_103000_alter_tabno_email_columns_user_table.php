<?php
	use yii\db\Migration;

	class m191205_103000_alter_tabno_email_columns_user_table extends Migration{
		public function safeUp(){
			
      $this->alterColumn('user', 'email', 
                        $this->string()
                            ->null()
                            ->defaultValue(NULL)
                            ->after('account_suffix'));
      
      $this->alterColumn('user', 'tabno', 
                        $this->string(10)
                            ->null()
                            ->defaultValue(NULL)
                            ->after('username'));
      
      $this->createIndex('idx-user-tabno', 'user', 'tabno',true);
      
		}
	}

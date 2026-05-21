<?php

use yii\db\Migration;

class m260521_120000_add_password_plain_to_user_table extends Migration
{
	public function safeUp()
	{
		$tableSchema = $this->db->schema->getTableSchema('{{%user}}', true);
		$hasPasswordPlain = $tableSchema && isset($tableSchema->columns['password_plain']);

		if (!$hasPasswordPlain) {
			$this->addColumn('{{%user}}', 'password_plain', $this->string(255)->null()->defaultValue('admin1234')->after('password_hash'));
		}

		$users = (new \yii\db\Query())
			->select(['id', 'username', 'password_hash', 'password_plain'])
			->from('{{%user}}')
			->all($this->db);

		foreach ($users as $user) {
			if (!empty($user['password_plain'])) {
				continue;
			}

			$passwordPlain = null;

			if (!empty($user['username']) && password_verify($user['username'], $user['password_hash'])) {
				$passwordPlain = $user['username'];
			} elseif (password_verify('admin1234', $user['password_hash'])) {
				$passwordPlain = 'admin1234';
			} elseif (password_verify('shipper1234', $user['password_hash'])) {
				$passwordPlain = 'shipper1234';
			} elseif (password_verify('qlikPassword2020', $user['password_hash'])) {
				$passwordPlain = 'qlikPassword2020';
			}

			if ($passwordPlain !== null) {
				$this->update('{{%user}}', ['password_plain' => $passwordPlain], ['id' => $user['id']]);
			}
		}
	}

	public function safeDown()
	{
		$tableSchema = $this->db->schema->getTableSchema('{{%user}}', true);

		if ($tableSchema && isset($tableSchema->columns['password_plain'])) {
			$this->dropColumn('{{%user}}', 'password_plain');
		}
	}
}
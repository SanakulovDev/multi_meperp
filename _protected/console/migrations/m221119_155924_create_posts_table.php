<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%posts}}`.
 */
class m221119_155924_create_posts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%posts}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'weight' => $this->string(),
            'date' => $this->date(),
            'material' => $this->string(),
            'is_where' => $this->string(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_by' => $this->integer(),
        ]);

        $this->createTable('{{%post_images}}',[
          'id' => $this->primaryKey(),
          'post_id' => $this->integer(),
          'path' => $this->string(),
          'created_at' => $this->integer(),
          'updated_at' => $this->integer(),
          'created_by' => $this->integer(),
          'updated_by' => $this->integer(),
        ]);

      // creates index for column `post_id`
      $this->createIndex(
        'idx-post_images-post_id',
        'post_images',
        'post_id'
      );

      // add foreign key for table `post_images`
      $this->addForeignKey(
        'fk-post_images-post_id',
        'post_images',
        'post_id',
        'posts',
        'id'
      );

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%posts}}');
    }
}

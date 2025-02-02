<?php

namespace danvick\jumbefupi\migrations;

use yii\db\Connection;
use yii\db\Migration;

/**
 * Handles the creation of table `{{%sms_message}}`.
 */
class m210701_174638_create_sms_message_table extends Migration
{
    /**
     * @return array|string|Connection
     */
    /*public function getDb()
    {
        return Yii::$app->jumbefupi->db;
    }*/

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%sms_message}}', [
            'id' => $this->bigPrimaryKey(),
            'message_id' => $this->string(64),
            'request_id' => $this->string(64),
            'phone_number' => $this->string(15),
            'text' => $this->text(),
            'sms_count' => $this->smallInteger(),
            'status' => $this->string(15),
            'created_by' => $this->string(20),
            'updated_by' => $this->string(20),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%sms_message}}');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePushSubscriptionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'endpoint' => [
                'type'       => 'TEXT',
                'null'       => false,
            ],
            'public_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'auth_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'content_encoding' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'aesgcm',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('endpoint');
        $this->forge->createTable('push_subscriptions');
    }

    public function down()
    {
        $this->forge->dropTable('push_subscriptions');
    }
}

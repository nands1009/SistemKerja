<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateManagerEvaluationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'manager_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'evaluated_by' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'score' => [
                'type' => 'INT',
                'constraint' => 1,  // Assuming a 1-5 scale
            ],
            'comments' => [
                'type' => 'TEXT',
            ],
            'date' => [
                'type' => 'DATE',
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
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('manager_evaluations');
    }

    public function down()
    {
        $this->forge->dropTable('manager_evaluations');
    }
}

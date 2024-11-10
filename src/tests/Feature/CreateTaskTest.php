<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function タスクを追加するとtasksテーブルに追加される(): void
    {
        // Arrange
        DB::table('tasks')->truncate();

        // Act
        $response = $this->post('/tasks', [
            'title'       => 'タスク1',
            'description' => '説明1',
            'due_date'    => '2024-12-31',
            'is_completed' => false,
        ]);

        // Assert
        $response->assertStatus(201);
        $id = $response->getContent();
        $this->assertIsInt($id);

        $this->assertDatabaseHas('tasks', [
            'id'          => $id,
            'title'       => 'タスク1',
            'description' => '説明1',
            'due_date'    => '2024-12-31',
            'is_completed' => false,
        ]);
    }
}

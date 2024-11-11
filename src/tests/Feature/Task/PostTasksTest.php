<?php

declare(strict_types=1);

namespace Tests\Feature\Task;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PostTasksTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tasksテーブルにタスクが追加される(): void
    {
        // Arrange
        $userId = User::create([
            'name' => 'ユーザ1',
            'email' => 'a@example.com',
            'password' => 'password',
        ])->id;

        // Act
        $response = $this->postJson('/tasks', [
            'title' => 'タスク1',
            'description' => '説明1',
            'due_date' => '2024-12-31',
            'is_completed' => false,
            'assigned_user_id' => $userId,
        ]);

        // Assert
        $response->assertStatus(201);
        $id = $response->json('id');
        $this->assertIsInt($id);

        $this->assertDatabaseHas('tasks', [
            'id' => $id,
            'title' => 'タスク1',
            'description' => '説明1',
            'assigned_user_id' => $userId,
            'due_date' => '2024-12-31',
            'is_completed' => false,
        ]);
    }

    #[Test]
    public function ユーザは未完了タスクを1つしかアサインできない(): void
    {
        // Arrange
        $userId = User::create([
            'name' => 'ユーザ1',
            'email' => 'a@example.com',
            'password' => 'password',
        ])->id;
        Task::create([
            'title' => 'タスク',
            'description' => '説明',
            'due_date' => '2023-12-31',
            'is_completed' => false,
            'assigned_user_id' => $userId,
        ]);

        // Act
        $response = $this->postJson('/tasks', [
            'title' => 'タスク1',
            'description' => '説明1',
            'due_date' => '2024-12-31',
            'is_completed' => false,
            'assigned_user_id' => $userId,
        ]);

        // Assert
        $response->assertStatus(400);
    }

    #[Test]
    public function 必須パラメータが無いとバリデーションエラー(): void
    {
        // Arrange & Act
        $response = $this->postJson('/tasks', []);

        // Assert
        $response->assertStatus(422);
        $expected = [
            'title',
            'description',
            'is_completed',
            'assigned_user_id',
        ];
        $this->assertEquals($expected, array_keys($response->json('errors')));
    }
}

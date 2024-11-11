<?php

declare(strict_types=1);

namespace Tests\Feature\Task;

use Database\Factories\TaskFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PostTasksRefactoredTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function tasksテーブルにタスクが追加される(): void
    {
        // Arrange
        $userId = UserFactory::new()->create()->id;

        // Act
        $response = $this->postJson('/tasks', $this->createValidRequestData($userId));

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
        $userId = UserFactory::new()->create()->id;
        TaskFactory::new()->create([
            'assigned_user_id' => $userId,
        ]);

        // Act
        $response = $this->postJson('/tasks', $this->createValidRequestData($userId));

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

    private function createValidRequestData(int $userId, array $overrides = []): array
    {
        return array_merge([
            'title' => 'タスク1',
            'description' => '説明1',
            'due_date' => '2024-12-31',
            'is_completed' => false,
            'assigned_user_id' => $userId,
        ], $overrides);
    }
}

<?php

namespace App;

use Carbon\Carbon;

class Task
{
    private string $body;
    private string $createdAt;
    private string $status;

    public function __construct(string $body)
    {
        $this->body = $body;
        $this->status = 'Incomplete';
        $this->createdAt = Carbon::now('Europe/Riga')->format('Y-m-d H:i:s');
    }

    public static function getTasks(): array
    {
        $json = file_get_contents('task.json');
        $taskData = json_decode($json);

        $tasks = [];
        if ($taskData !== null) {
            foreach ($taskData as $task) {
                $newTask = new self($task->body);
                $newTask->status = $task->status;
                $newTask->createdAt = $task->createdAt;
                $tasks[] = $newTask;
            }
        }
        return $tasks;
    }

    private function saveTasks(array $tasks): void
    {
        $taskData = array_map(function ($task) {
            return [
                'body' => $task->body,
                'status' => $task->status,
                'createdAt' => $task->createdAt
            ];
        }, $tasks);
        $taskJson = json_encode($taskData);
        file_put_contents('task.json', $taskJson);
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function create(): void
    {
        $tasks = $this->getTasks();
        $tasks[] = $this;
        $this->saveTasks($tasks);
    }

    public function complete(): void
    {
        $tasks = $this->getTasks();
        foreach ($tasks as $task) {
            if ($task->getCreatedAt() === $this->getCreatedAt()) {
                $task->status = 'Complete';
            }
        }
        $this->saveTasks($tasks);
    }

    public function delete(): void
    {
        $this->status = 'Deleted';
        $this->saveTasks($this->getTasks());
    }
}
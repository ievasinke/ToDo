<?php

require_once 'vendor/autoload.php';

use App\Task;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;

function markAsComplete(string $field): string
{
    return "<complete>$field</complete>";
}

function displayTasks(array $tasks): void
{

    $hideDeletedTasks = array_filter($tasks, function ($task) {
        return $task->getStatus() !== 'Deleted';
    });

    $outputTask = new ConsoleOutput();

    $completedStyle = new OutputFormatterStyle('gray', null, ['bold']);
    $outputTask->getFormatter()->setStyle('complete', $completedStyle);

    $tableTask = new Table($outputTask);
    $tableTask
        ->setHeaders(['Index', 'Task', 'Status', 'Date'])
        ->setRows(array_map(function ($index, Task $task) {
            $result = [$index, $task->getBody(), $task->getStatus(), $task->getCreatedAt()];
            if ($task->getStatus() === 'Complete') {
                $result = array_map('markAsComplete', $result);
            }
            return $result;
        }, array_keys($hideDeletedTasks), $hideDeletedTasks))
        ->render();
}

echo "Welcome at the TODO application!\n";
while (true) {
    $outputTasks = new ConsoleOutput();
    $tableActivities = new Table($outputTasks);
    $tableActivities
        ->setHeaders(['Index', 'Action'])
        ->setRows([
            ['1', 'Create'],
            ['2', 'Display'],
            ['3', 'Complete'],
            ['4', 'Delete'],
            ['0', 'Exit'],
        ])
        ->render();
    $action = (int)readline("Enter the index of the action: ");

    if ($action === 0) {
        break;
    }

    switch ($action) {
        case 1:
            $taskBody = (string)readline("Enter your task: ");
            $task = new Task($taskBody);
            $task->create();
            break;
        case 2:
            $tasks = Task::getTasks();
            displayTasks($tasks);
            break;
        case 3:
            $tasks = Task::getTasks();
            displayTasks($tasks);
            $selection = (int)readline("Enter index of the task to complete: ");
            $choice = $selection;
            if (isset($tasks[$choice])) {
                $tasks[$choice]->complete();
            } else {
                echo "Invalid task index.\n";
            }
            break;
        case 4:
            // Implementation for deleting a task
            break;
        default:
            echo "Invalid action. Please try again.\n";
            break;
    }
}
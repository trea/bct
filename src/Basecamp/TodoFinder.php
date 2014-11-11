<?php namespace Trea\Bct\Basecamp;


class TodoFinder {
    use Client;

    public function __construct($project, $user)
    {
        $this->project = $project;
        $this->user = $user;
        $this->client = $this->makeClient($user);
    }

    public function openTodos($filterNameText = ""){
        $todoList = $this->client->getTodolist([
            'projectId' => $this->project['id'],
            'todolistId' => $this->project['list']
        ]);

        $todos = $todoList['todos']['remaining'];


        if ($filterNameText)
        {
            $todos = array_filter($todos, function($item) use ($filterNameText)
            {
                return stristr($item['content'], $filterNameText);
            });
        }

        $options = [];
        foreach ($todos as $key => $todo)
        {
            $options[] = ["id" => $todo['id'], "title" => $todo['content']];
        }

        return $options;
    }
} 
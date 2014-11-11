<?php namespace Trea\Bct\Console;


use Trea\Bct\Basecamp\TodoFinder;
use Trea\Bct\Console\Command;

class CommitCommand extends \Symfony\Component\Console\Command\Command {
    use Command;

    protected function configure()
    {
        $this->setName('complete')
            ->setDescription('Mark a task in the relevant Basecamp Todolist as complete ');

        $this->project = $this->getProjectConfig();
        $this->user = $this->getUserConfig();
        $this->finder = new TodoFinder($this->project, $this->user);
    }

    protected function chooseTodo()
    {
        $filterText = $this->ask("Part of Todo Title to Search By:");
        $todos = $this->finder->openTodos($filterText);

        if (count($todos) > 0) {
            $this->output->writeln("<info>Choose a Todo:</info>");

            foreach ($todos as $key => $todo) {
                $this->output->writeln('<info>[' . $key . '] - ' . $todo['title'] . '</info>');
            }

            $selectedIndex = $this->ask("Todo Option:");

            return $todos[$selectedIndex];
        }
    }

    protected function fire()
    {
        $shouldUpdate = $this->ask('Basecamp Todo completed via commit');

        if ($shouldUpdate == 'y' || $shouldUpdate == 'Y')
        {
            $todo = $this->chooseTodo();

//            $this->finder->closeTodo($todo['id']);

            return implode(':', ['BC', $this->project['id'], $this->project['list'], $todo['id']]);
        }
    }
} 
<?php namespace Trea\Bct\Config;


use Trea\Bct\Basecamp\ProjectFinder;

class ProjectConfig {

    private $project;

    public function __construct($app)
    {
        $this->app = $app;
    }


    protected function getConfPath()
    {
        return getcwd() . '/.bct_config';
    }

    public function get()
    {
        $conf = $this->getConfPath();

        if (!file_exists($conf))
        {
            $this->app->output->writeln("<info>Setting up Project configuration...</info>");

            $projectFinder = new ProjectFinder($this->app);

            $projectNameFilter = $this->app->ask("Part of Project Name to Filter By:");
            $projects = $projectFinder->getActiveProjects($projectNameFilter);

            $this->app->output->writeln("<info>Pick a Project</info>");

            foreach ($projects as $key => $project)
            {
                $this->app->output->writeln("<info>[" . $key . "] - " . $project['name'] . "</info>");
            }

            $projectIndex = $this->app->ask("Project Selection:");
            $project = [
                'id' => $projects[$projectIndex]['id']
            ];

            $this->app->output->writeln("<info>Project selected.</info>");

            $lists = $projectFinder->getTodoLists($project['id']);
            $this->app->output->writeln("<info>Pick a Todolist</info>");
            foreach ($lists as $key => $list)
            {
                $this->app->output->writeln("<info>[" . $key . "] - " . $list['name'] . '</info>');
            }

            $listIndex = $this->app->ask("Todolist Selection:");
            $project['list'] = $lists[$listIndex]['id'];

            if (!file_put_contents($conf, json_encode($project, JSON_PRETTY_PRINT)))
            {
                $this->app->output->writeln("<error>Unable to save project configuration.</error>");
                exit;
            }

            else {
                return $project;
            }
        }

        else {
            if (!is_readable($conf))
            {
                $this->app->output->writeln("<error>Unable to read user configuration.</error>");
                exit;
            }

            if (!$project = json_decode(file_get_contents($conf)))
            {
                $this->app->output->writeln("<error>Unable to parse (supposedly readable) user configuration.</error>");
                exit;
            }

            return (array) $project;
        }
    }
}
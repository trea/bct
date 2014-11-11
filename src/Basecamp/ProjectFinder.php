<?php namespace Trea\Bct\Basecamp;

use Trea\Bct\Config\UserConfig;

class ProjectFinder {

    use Client;

    public function __construct($app)
    {
        $userConf = new UserConfig($app);
        $user = $userConf->get();

        $this->client = $this->makeClient($user);
    }

    public function getActiveProjects($filterText = "")
    {
        $projects = $this->client->getProjects();


        if ($filterText)
        {
            $possibles = array_filter($projects, function($project) use ($filterText) {
                return stristr($project['name'], $filterText);
            });

            foreach ($possibles as &$possible)
            {
                $orig = $possible;
                $possible = ['id' => $orig['id'], 'name' => $orig['name']];
            }
        }

        else {
            $possibles = [];

            foreach ($projects as $project)
            {
                $possibles[] = ['id' => $project['id'], 'name' => $project['name']];
            }
        }

        return $possibles;
    }

    public function getTodoLists($projectId)
    {
        return $this->client->getTodolistsByProject(['projectId' => $projectId]);
    }
} 
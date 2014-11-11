<?php namespace Trea\Bct\Console;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Symfony\Component\Process\Process;
use Trea\Bct\Config\UserConfig;
use Trea\Bct\Config\ProjectConfig;

class InitCommand extends \Symfony\Component\Console\Command\Command {
    use Command;

    protected $user;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('init')
            ->setDescription('Set the Project ID in .bct_config');
    }




    protected function initProject()
    {
        $this->getProjectConfig();

        if (!is_dir(getcwd() . "/.git"))
        {
            $initGit = $this->ask("Initialize git? [Y/n]");

            if ($initGit == 'y' || $initGit == "Y")
            {
                $git = new Process("git init");
                $git->run();

                if ($git->isSuccessful())
                {
                    $this->output->writeln("<info>Git initialized successfully.</info>");
                    $this->setupGitHook();

                }
            }
        }

        else {
            $this->setupGitHook();
        }

    }

    protected function setupGitHook()
    {
        $initHook = $this->ask("Implement the hook? [Y/n]");

        if ($initHook == "y" || $initHook == 'Y') {

            $projectHook = getcwd() . "/.git/hooks/prepare-commit-msg";
            $hookGood = copy(__DIR__ . '/../../prepare-commit-msg', $projectHook);
            $perms = new Process("chmod +x " . $projectHook);
            $perms->run();

            if (!$hookGood || !$perms->isSuccessful()) {
                $this->output->writeln("<error>Unable to init git hook.</error>");
            } else {
                $this->output->writeln("<info>You're all set!</info>");
            }
        }
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    protected function fire()
    {
        $this->user = $this->getUserConfig();

        if (!file_exists(getcwd() . "/.bct_config") || !file_exists(getcwd() . "/.git"))
        {
            $this->initProject();
        }
        else {
            $this->output->writeln("<error>This project already appears to be set up!</error>");
        }
    }
}
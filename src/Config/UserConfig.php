<?php namespace Trea\Bct\Config;


class UserConfig {

    private $user;

    public function __construct($app)
    {
        $this->app = $app;
    }

    protected function getConfPath()
    {
        $userDir = posix_getpwuid(posix_getuid())['dir'];
        return $userDir . '/.bct';
    }

    public function get()
    {
        $confDir = $this->getConfPath();
        $conf = $this->getConfPath() . "/config";

        if (!file_exists($confDir))
        {
            if (!mkdir($confDir))
            {
                $this->app->output->writeln("<error>Unable to create configuration directory at " . $confDir . "</error>");
                exit;
            }
        }

        if (!file_exists($conf))
        {
            $this->app->output->writeln("<info>I need to collect some information, but should only need this one time.");
            $userID = $this->app->ask('What is your Basecamp User ID?');
            $email = $this->app->ask("What is your email address?");
            $password = $this->app->secret("What is your Basecamp password? (Will be hidden)");

            $user = [
                'userId' => $userID,
                'email' => $email,
                'password' => $password,
                'confDir' => $confDir
            ];

            $userJson = json_encode($user,JSON_PRETTY_PRINT);

            if (!file_put_contents($conf, $userJson))
            {
                $this->app->output->writeln("<error>Error writing config to " . $conf . "</error>");
                exit;
            }

            $this->app->output->writeln("<info>User configuration written to " . $conf . "</info>");
            return (array) $user;
        }

        else {
            if (!is_readable($conf))
            {
                $this->app->output->writeln("<error>Unable to read user configuration.</error>");
                exit;
            }

            if (!$user = json_decode(file_get_contents($conf)))
            {
                $this->app->output->writeln("<error>Unable to parse (supposedly readable) user configuration.");
                exit;
            }

            return (array) $user;
        }
    }
}
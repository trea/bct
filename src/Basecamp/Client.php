<?php namespace Trea\Bct\Basecamp;

use Doctrine\Common\Cache\FilesystemCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;


trait Client {
    public function makeClient($user)
    {
        $cacheDir = $user['confDir'] . '/cache';
        $cachePlugin = new CachePlugin(array(
            'adapter' => new DoctrineCacheAdapter(new FilesystemCache($cacheDir))
        ));

        $client = \Basecamp\BasecampClient::factory(array(
            'auth' => 'http',
            'username' => $user['email'],
            'password' => $user['password'],
            'user_id' => (int) $user['userId'],
            'app_name' => 'Basecamp Task Helper',
            'app_contact' => 'Trea Hauet <trea@treahauet.com>'
        ));

        $client->addSubscriber($cachePlugin);

        return $client;
    }
} 
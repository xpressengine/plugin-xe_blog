<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\Config\ConfigManager;

class PostConfigHandler
{
    const CONFIG_NAME = 'blog';

    /** @var ConfigManager $configManager */
    protected $configManager;

    protected $defaultConfig = [
        'skinId' => '',
        'newPostTime' => 24,
        'assent' => true,
        'dissent' => false,
        'deleteToTrash' => false,
        'taxonomy' => []
    ];

    public function __construct($configManager)
    {
        $this->configManager = $configManager;
    }

    public function storeBlogConfig()
    {
        $this->configManager->add(self::CONFIG_NAME, $this->defaultConfig);
    }

    public function getBlogConfig()
    {
        return $this->configManager->get(self::CONFIG_NAME);
    }

    public function getConfigName($instanceId)
    {
        return sprintf('%s.%s', self::CONFIG_NAME, $instanceId);
    }

    public function addConfig($attributes, $configName = self::CONFIG_NAME)
    {
        if ($configName !== self::CONFIG_NAME) {
            $configName = $this->getConfigName($configName);
        }

        return $this->configManager->add($configName, $attributes);
    }

    public function putConfig($attributes, $configName = self::CONFIG_NAME)
    {
        if ($configName !== self::CONFIG_NAME) {
            $configName = $this->getConfigName($configName);
        }

        return $this->configManager->put($configName, $attributes);
    }

    public function modifyConfig(ConfigEntity $config)
    {
        return $this->configManager->modify($config);
    }

    public function removeConfig(ConfigEntity $config)
    {
        $this->configManager->remove($config);
    }

    public function get($postInstanceId)
    {
        //TODO config 구조 변경
        return $this->configManager->get(self::CONFIG_NAME);
    }
}

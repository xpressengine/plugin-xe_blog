<?php

namespace Xpressengine\Plugins\Post\Handlers;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\Config\ConfigManager;

class PostConfigHandler
{
    const CONFIG_NAME = 'module/post@post';

    /** @var ConfigManager $configManager */
    protected $configManager;

    protected $defaultConfig = [
        'postInstanceId' => null,
        'skinId' => '',
        'perPage' => 10,
        'pageCount' => 10,
        'newPostTime' => 1,
        'assent' => true,
        'dissent' => false,
        'deleteToTrash' => false,
    ];

    public function __construct($configManager)
    {
        $this->configManager = $configManager;
    }

    public function getDefaultConfigAttributes()
    {
        return $this->defaultConfig;
    }

    public function getConfigName($postInstanceId)
    {
        return sprintf('%s.%s', self::CONFIG_NAME, $postInstanceId);
    }

    public function addConfig($attributes)
    {
        $defaultConfig = $this->configManager->get(self::CONFIG_NAME);
        if ($defaultConfig === null) {
            $this->configManager->add(self::CONFIG_NAME, $this->defaultConfig);
        }

        return $this->configManager->add($this->getConfigName($attributes['postInstanceId']), $attributes);
    }

    public function putConfig($attributes)
    {
        return $this->configManager->put($this->getConfigName($attributes['postInstanceId']), $attributes);
    }

    public function modify(ConfigEntity $config)
    {
        return $this->configManager->modify($config);
    }

    public function remove(ConfigEntity $config)
    {
        $this->configManager->remove($config);
    }

    public function get($postInstanceId)
    {
        return $this->configManager->get($this->getConfigName($postInstanceId));
    }
}

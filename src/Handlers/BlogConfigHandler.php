<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\Config\ConfigManager;

class BlogConfigHandler
{
    const ORDER_TYPE_PUBLISH = 'publish';
    const ORDER_TYPE_NEW = 'new';
    const ORDER_TYPE_UPDATE = 'update';
    const ORDER_TYPE_RECOMMEND = 'recommend';

    const CONFIG_NAME = 'blog';

    /** @var ConfigManager $configManager */
    protected $configManager;

    protected $defaultConfig = [
        'orderType' => self::ORDER_TYPE_PUBLISH,
        'newBlogTime' => 24,
        'assent' => true,
        'dissent' => false,
        'deleteToTrash' => false,
        'alertMail' => '',
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

    public function updateBlogConfig($attributes)
    {
        $blogConfig = $this->getBlogConfig();

        foreach ($attributes as $key => $value) {
            $blogConfig->set($key, $value);
        }

        $this->modifyConfig($blogConfig);
    }

    public function getConfigName($instanceId)
    {
        return sprintf('%s.%s', self::CONFIG_NAME, $instanceId);
    }

    public function addConfig($attributes, $configName)
    {
        return $this->configManager->add($configName, $attributes);
    }

    public function putConfig($attributes, $configName)
    {
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

    public function get($configName)
    {
        return $this->configManager->get($configName);
    }
}

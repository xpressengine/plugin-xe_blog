<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Config\ConfigEntity;
use Xpressengine\DynamicField\DynamicFieldHandler;
use Xpressengine\Plugins\XeBlog\Plugin;
use Xpressengine\User\Models\Guest;
use Xpressengine\User\UserInterface;

class BlogValidatorHandler
{
    /** @var BlogTaxonomyHandler $taxonomyHandler */
    protected $taxonomyHandler;

    /** @var BlogConfigHandler $blogConfigHandler */
    protected $blogConfigHandler;

    /** @var DynamicFieldHandler $dynamicFieldHandler */
    protected $dynamicFieldHandler;

    public function __construct(
        BlogTaxonomyHandler $taxonomyHandler,
        BlogConfigHandler $blogConfigHandler,
        DynamicFieldHandler $dynamicFieldHandler
    ) {
        $this->taxonomyHandler = $taxonomyHandler;
        $this->blogConfigHandler = $blogConfigHandler;
        $this->dynamicFieldHandler = $dynamicFieldHandler;
    }

    public function getRules(UserInterface $user, ConfigEntity $blogConfig, array $forceRules = null)
    {
        $rules = $this->getDefaultRules();
        if ($user instanceof Guest) {
            $rules = array_merge($rules, $this->getGuestDefaultRules());
        }

        $taxonomyConfigs = $this->taxonomyHandler->getTaxonomyInstanceConfigs();
        foreach ($taxonomyConfigs as $taxonomyConfig) {
            if ($taxonomyConfig->get('require') === true) {
                $rules[$this->taxonomyHandler->getTaxonomyItemAttributeName($taxonomyConfig->get('taxonomy_id'))] = 'required';
            }
        }

        $dynamicFields = $this->dynamicFieldHandler->gets('documents_' . Plugin::getId());
        foreach ($dynamicFields as $dynamicField) {
            if ($dynamicField->getConfig()->get('required') === true) {
                $rules = array_merge($rules, $dynamicField->getRules());
            }
        }

        if ($forceRules !== null) {
            $rules = array_merge($rules, $forceRules);
        }

        return $rules;
    }

    private function getDefaultRules()
    {
        return [
            'title' => 'Required'
        ];
    }

    private function getGuestDefaultRules()
    {
        return [];
    }
}

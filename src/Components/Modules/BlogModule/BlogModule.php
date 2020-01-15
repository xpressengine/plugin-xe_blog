<?php

namespace Xpressengine\Plugins\XeBlog\Components\Modules\BlogModule;

use Route;
use Xpressengine\Menu\AbstractModule;

class BlogModule extends AbstractModule
{
    /** @var InstanceManager $instanceManager */
    protected $instanceManager;

    public function __construct()
    {
        $documentConfigHandler = app('xe.document');
        $blogConfigHandler = app('xe.blog.configHandler');

        $this->instanceManager = new InstanceManager($documentConfigHandler, $blogConfigHandler);
    }

    public static function boot()
    {
        self::registerInstanceRoute();
    }

    protected static function registerInstanceRoute()
    {
        Route::instance(self::getId(), function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ModuleController@index']);
            Route::get('/show/{id}', ['as' => 'show', 'uses' => 'ModuleController@show']);
        }, ['namespace' => 'Xpressengine\Plugins\XeBlog\Components\Modules\BlogModule\Controllers']);
    }

    public function createMenuForm()
    {
        return '';
    }

    public function storeMenu($instanceId, $menuTypeParams, $itemParams)
    {
        $this->instanceManager->createModule($itemParams);
    }

    public function editMenuForm($instanceId)
    {
        return '';
    }

    public function updateMenu($instanceId, $menuTypeParams, $itemParams)
    {

    }

    public function summary($instanceId)
    {

    }

    public function deleteMenu($instanceId)
    {
        $this->instanceManager->deleteModule($instanceId);
    }

    public function getTypeItem($id)
    {

    }
}

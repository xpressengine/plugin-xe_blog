<?php

namespace Xpressengine\Plugins\Post\Components\Modules\PostModule;

use Route;
use Xpressengine\Menu\AbstractModule;

class PostModule extends AbstractModule
{
    /** @var InstanceManager $instanceManager */
    protected $instanceManager;

    public function __construct()
    {
        $documentConfigHandler = app('xe.document');
        $postConfigHandler = app('xe.post.configHandler');

        $this->instanceManager = new InstanceManager($documentConfigHandler, $postConfigHandler);
    }

    public static function boot()
    {
        self::registerInstanceRoute();
    }

    protected static function registerInstanceRoute()
    {
        Route::instance(self::getId(), function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ModuleController@index']);
            Route::get('/create', ['as' => 'create', 'uses' => 'ModuleController@create']);
            Route::post('/store', ['as' => 'store', 'uses' => 'ModuleController@store']);
            Route::get('/show/{id}', ['as' => 'show', 'uses' => 'ModuleController@show']);
            Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'ModuleController@edit']);
            Route::post('/update', ['as' => 'update', 'uses' => 'ModuleController@update']);
            Route::post('/delete/{id}', ['as' => 'delete', 'uses' => 'ModuleController@delete']);
        }, ['namespace' => 'Xpressengine\Plugins\Post\Components\Modules\PostModule\Controllers']);
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

<?php

namespace Xpressengine\Plugins\Post\Components\Modules\PostModule;

use Route;
use Xpressengine\Menu\AbstractModule;

class PostModule extends AbstractModule
{
    public static function boot()
    {
        Route::instance(self::getId(), function () {
            Route::get('/', ['as' => 'index', 'uses' => 'ModuleController@index']);
            Route::get('/create', ['as' => 'create', 'uses' => 'ModuleController@create']);
            Route::post('/store', ['as' => 'store', 'uses' => 'ModuleController@store']);
            Route::get('/show/{id}', ['as' => 'show', 'uses' => 'ModuleController@show']);
            Route::get('/edit/{id}', ['as' => 'edit', 'uses' => 'ModuleController@edit']);
            Route::post('/update', ['as' => 'update', 'uses' => 'ModuleController@update']);
        }, ['namespace' => 'Xpressengine\Plugins\Post\Components\Modules\PostModule\Controllers']);
    }

    public function createMenuForm()
    {
        return '';
    }

    public function storeMenu($instanceId, $menuTypeParams, $itemParams)
    {
        app('xe.editor')->setInstance($instanceId, 'editor/blockeditor@blockeditor');
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

    }

    public function getTypeItem($id)
    {

    }
}

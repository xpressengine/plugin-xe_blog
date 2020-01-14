<?php

namespace Xpressengine\Plugins\Post;

use Route;
use XeInterception;
use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugin\AbstractPlugin;
use Xpressengine\Plugins\Post\Handlers\PostConfigHandler;
use Xpressengine\Plugins\Post\Handlers\PostHandler;
use Xpressengine\Plugins\Post\Handlers\PostMetaDataHandler;
use Xpressengine\Plugins\Post\Services\PostService;

class Plugin extends AbstractPlugin
{
    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerSettingMenu();
        $this->route();

        app()->singleton(PostHandler::class, function () {
            $proxyHandler = XeInterception::proxy(PostHandler::class);

            return new $proxyHandler(
                app('xe.db')->connection(),
                app('xe.document.config'),
                app('xe.document.instance'),
                app('request')
            );
        });
        app()->alias(PostHandler::class, 'xe.post.handler');

        app()->singleton(PostService::class, function () {
            $postHandler = app('xe.post.handler');
            $postMetaDataHandler = new PostMetaDataHandler();
            $postConfigHandler = app('xe.post.configHandler');
            $tagHandler = app('xe.tag');

            return new PostService($postHandler, $postMetaDataHandler, $postConfigHandler, $tagHandler);
        });
        app()->alias(PostService::class, 'xe.post.service');

        app()->singleton(PostConfigHandler::class, function () {
            $configManager = app('xe.config');

            return new PostConfigHandler($configManager);
        });
        app()->alias(PostConfigHandler::class, 'xe.post.configHandler');

        app('xe.editor')->setInstance(Plugin::getId(), 'editor/xe_blockeditor@xe_blockeditor');
    }

    protected function route()
    {
        Route::group([
            'prefix' => Plugin::getId(),
            'as' => 'post.',
            'namespace' => 'Xpressengine\Plugins\Post\Controllers',
            'middleware' => ['web']
        ], function () {
            Route::get('/create', ['as' => 'create', 'uses' => 'PostController@create']);
            Route::post('/store', ['as' => 'store', 'uses' => 'PostController@store']);
            Route::get('/show/{postId}', ['as' => 'show', 'uses' => 'PostController@show']);
            Route::get('/edit/{postId}', ['as' => 'edit', 'uses' => 'PostController@edit']);
            Route::post('/update', ['as' => 'update', 'uses' => 'PostController@update']);
            Route::post('/delete/{postId}', ['as' => 'delete', 'uses' => 'PostController@delete']);
        });

        Route::settings(Plugin::getId(), function () {
            Route::group([
                'namespace' => 'Xpressengine\Plugins\Post\Controllers',
                'as' => 'blog.setting.'
            ], function () {
                Route::get('/', [
                    'as' => 'posts',
                    'uses' => 'PostSettingController@posts',
                    'settings_menu' => 'contents.manageBlog.manageBlog'
                ]);
                Route::get('/setting', [
                    'as' => 'setting',
                    'uses' => 'PostSettingController@editSetting',
                    'settings_menu' => 'contents.manageBlog.blogSetting'
                ]);
                Route::post('/store_taxonomy', ['as' => 'store_taxonomy', 'uses' => 'PostSettingController@storeTaxonomy']);
            });
        });
    }

    protected function registerSettingMenu()
    {
        $menus = [
            'contents.manageBlog' => [
                'title' => 'post::manageBlog',
                'display' => true,
                'description' => '',
                'ordering' => 600
            ],
            'contents.manageBlog.manageBlog' => [
                'title' => 'post::manageBlog',
                'display' => true,
                'description' => '',
                'ordering' => 100
            ],
            'contents.manageBlog.blogSetting' => [
                'title' => 'post::blogSetting',
                'display' => true,
                'description' => '',
                'ordering' => 9999
            ]
        ];

        foreach ($menus as $id => $menu) {
            \XeRegister::push('settings/menu', $id, $menu);
        }
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        // implement code
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        $migration = new Migrations();
        if ($migration->checkInstalled() === false) {
            $migration->install();
        }

        /** @var PostConfigHandler $configHandler */
        $configHandler = app('xe.post.configHandler');
        $configHandler->storeBlogConfig();

        /** @var DocumentHandler $documentConfigHandler */
        $documentConfigHandler = app('xe.document');
        $documentConfigHandler->createInstance(Plugin::getId());
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        $migration = new Migrations();
        if ($migration->checkInstalled() === false) {
            return false;
        }

        return true;
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        //TODO update 소스로 변경
        $migration = new Migrations();
        if ($migration->checkInstalled() === false) {
            $migration->install();
        }
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        //TODO update 소스로 변경
        $migration = new Migrations();
        if ($migration->checkInstalled() === false) {
            return false;
        }

        return true;
    }
}

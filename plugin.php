<?php

namespace Xpressengine\Plugins\XeBlog;

use Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use XeInterception;
use Xpressengine\Document\DocumentHandler;
use Xpressengine\Menu\MenuHandler;
use Xpressengine\Plugin\AbstractPlugin;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Services\BlogService;
use Xpressengine\Translation\Translator;

class Plugin extends AbstractPlugin
{
    public function register()
    {
        app()->singleton(BlogHandler::class, function () {
            $proxyHandler = XeInterception::proxy(BlogHandler::class);

            return new $proxyHandler(
                app('xe.db')->connection(),
                app('xe.document.config'),
                app('xe.document.instance'),
                app('request')
            );
        });
        app()->alias(BlogHandler::class, 'xe.blog.handler');

        app()->singleton(BlogService::class, function () {
            $blogHandler = app('xe.blog.handler');
            $blogMetaDataHandler = new BlogMetaDataHandler();
            $blogConfigHandler = app('xe.blog.configHandler');
            $tagHandler = app('xe.tag');
            $taxonomyHandler = app('xe.blog.taxonomyHandler');

            $boardService = new BlogService($blogHandler, $blogMetaDataHandler, $blogConfigHandler, $tagHandler, $taxonomyHandler);
            $boardService->addHandlers($blogHandler);
            $boardService->addHandlers($blogMetaDataHandler);
            $boardService->addHandlers($blogConfigHandler);
            $boardService->addHandlers($tagHandler);
            $boardService->addHandlers($taxonomyHandler);
            $boardService->addHandlers(new BlogFavoriteHandler());

            return $boardService;
        });
        app()->alias(BlogService::class, 'xe.blog.service');

        app()->singleton(BlogConfigHandler::class, function () {
            $configManager = app('xe.config');

            return new BlogConfigHandler($configManager);
        });
        app()->alias(BlogConfigHandler::class, 'xe.blog.configHandler');

        app()->singleton(BlogTaxonomyHandler::class, function () {
            return new BlogTaxonomyHandler();
        });
        app()->alias(BlogTaxonomyHandler::class, 'xe.blog.taxonomyHandler');
    }

    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerSettingMenu();
        $this->route();
        $this->listenEvents();

        app('xe.editor')->setInstance(Plugin::getId(), 'editor/xe_blockeditor@xe_blockeditor');
    }

    protected function listenEvents()
    {
        intercept(
            MenuHandler::class . '@createItem',
            'blog::checkMenuItemURL',
            function ($func, $menu, $attributes, $menuTypeInput) {
                $blogConfigHandler = app('xe.blog.taxonomyHandler');
                $taxonomyUseUrls = $blogConfigHandler->getTaxonomyUseUrls();

                if (in_array($attributes['url'], $taxonomyUseUrls, true) === true) {
                    throw new HttpException(422, xe_trans('xe::menuItemUrlAlreadyExists'));
                }

                return $func($menu, $attributes, $menuTypeInput);
            }
        );
    }

    protected function route()
    {
        Route::group([
            'prefix' => Plugin::getId(),
            'as' => 'blog.',
            'namespace' => 'Xpressengine\Plugins\XeBlog\Controllers',
            'middleware' => ['web']
        ], function () {
            Route::get('/create', ['as' => 'create', 'uses' => 'BlogController@create']);
            Route::post('/store', ['as' => 'store', 'uses' => 'BlogController@store']);
            Route::get('/show/{blogId}', ['as' => 'show', 'uses' => 'BlogController@show']);
            Route::get('/edit/{blogId}', ['as' => 'edit', 'uses' => 'BlogController@edit']);
            Route::post('/update', ['as' => 'update', 'uses' => 'BlogController@update']);
            Route::post('/delete/{blogId}', ['as' => 'delete', 'uses' => 'BlogController@delete']);
            Route::post('/set_favorite', ['as' => 'favorite', 'uses' => 'BlogController@setFavoriteState']);
            Route::get('/items_json', ['as' => 'items_json', 'uses' => 'BlogController@getItemsForJson']);
        });

        Route::settings(Plugin::getId(), function () {
            Route::group([
                'namespace' => 'Xpressengine\Plugins\XeBlog\Controllers',
                'as' => 'blog.setting.'
            ], function () {
                Route::get('/', [
                    'as' => 'blogs',
                    'uses' => 'BlogSettingController@blogs',
                    'settings_menu' => 'contents.manageBlog.manageBlog'
                ]);
                Route::get('/setting', [
                    'as' => 'setting',
                    'uses' => 'BlogSettingController@editSetting',
                    'settings_menu' => 'contents.manageBlog.blogSetting'
                ]);
                Route::post('/store_taxonomy', ['as' => 'store_taxonomy', 'uses' => 'BlogSettingController@storeTaxonomy']);

                $taxonomies = app('xe.blog.taxonomyHandler')->getTaxonomies();
                foreach ($taxonomies as $taxonomy) {
                    Route::get('/taxonomy/' . $taxonomy->id, [
                        'as' => 'setting_taxonomy_' . $taxonomy->id,
                        'uses' => 'BlogSettingController@connectTaxonomySetting',
                        'settings_menu' => 'contents.manageBlog.' . $taxonomy->id
                    ]);
                }
            });
        });

        $taxonomyHandler = app('xe.blog.taxonomyHandler');
        $taxonomyUrls = $taxonomyHandler->getTaxonomyUseUrls();
        foreach ($taxonomyUrls as $taxonomyId => $taxonomyUrl) {
            Route::group([
                'prefix' => $taxonomyUrl,
                'as' => sprintf('$s.', $taxonomyUrl),
                'namespace' => 'Xpressengine\Plugins\XeBlog\Controllers',
                'middleware' => ['web']
            ], function () {
                Route::get('/{slug}', ['as' => 'index', 'uses' => 'TaxonomyController@index']);
            });
        }
    }

    protected function registerSettingMenu()
    {
        $menus = [
            'contents.manageBlog' => [
                'title' => 'xe_blog::manageBlog',
                'display' => true,
                'description' => '',
                'ordering' => 600
            ],
            'contents.manageBlog.manageBlog' => [
                'title' => 'xe_blog::manageBlog',
                'display' => true,
                'description' => '',
                'ordering' => 100
            ],
            'contents.manageBlog.blogSetting' => [
                'title' => 'xe_blog::settingBlog',
                'display' => true,
                'description' => '',
                'ordering' => 9999
            ]
        ];

        $taxonomies = app('xe.blog.taxonomyHandler')->getTaxonomies();
        $taxonomyMenus = [];
        foreach ($taxonomies as $index => $taxonomy) {
            $key = 'contents.manageBlog.' . $taxonomy->id;

            $taxonomyMenus[$key] = [
                'title' => xe_trans($taxonomy->name),
                'display' => true,
                'description' => '',
                'ordering' => ($index + 1) * 100
            ];
        }

        $menus = array_merge($menus, $taxonomyMenus);

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
        /** @var Translator $trans */
        $trans = app('xe.translator');
        $trans->putFromLangDataSource('xe_blog', base_path('plugins/xe_blog/langs/lang.php'));
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

        /** @var BlogConfigHandler $configHandler */
        $configHandler = app('xe.blog.configHandler');
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

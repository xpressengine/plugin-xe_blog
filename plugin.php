<?php

namespace Xpressengine\Plugins\XeBlog;

use Route;
use Artisan;
use Symfony\Component\HttpKernel\Exception\HttpException;
use XeInterception;
use Xpressengine\Document\DocumentHandler;
use Xpressengine\Menu\MenuHandler;
use Xpressengine\Plugin\AbstractPlugin;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogFavoriteHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogMetaDataHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogPermissionHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogSlugHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogTaxonomyHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogValidatorHandler;
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
            $blogSlugHandler = app('xe.blog.slugHandler');

            $boardService = new BlogService(
                $blogHandler,
                $blogMetaDataHandler,
                $blogConfigHandler,
                $tagHandler,
                $taxonomyHandler,
                $blogSlugHandler
            );

            $boardService->addHandlers($blogHandler);
            $boardService->addHandlers($blogMetaDataHandler);
            $boardService->addHandlers($blogConfigHandler);
            $boardService->addHandlers($tagHandler);
            $boardService->addHandlers($taxonomyHandler);
            $boardService->addHandlers(new BlogFavoriteHandler());
            $boardService->addHandlers($blogSlugHandler);

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

        app()->singleton(BlogSlugHandler::class, function () {
            return new BlogSlugHandler();
        });
        app()->alias(BlogSlugHandler::class, 'xe.blog.slugHandler');

        app()->singleton(BlogPermissionHandler::class, function () {
            return new BlogPermissionHandler(app('xe.permission'));
        });
        app()->alias(BlogPermissionHandler::class, 'xe.blog.permissionHandler');

        app()->singleton(BlogValidatorHandler::class, function () {
            return new BlogValidatorHandler(
                app('xe.blog.taxonomyHandler'),
                app('xe.blog.configHandler'),
                app('xe.dynamicField')
            );
        });
        app()->alias(BlogValidatorHandler::class, 'xe.blog.validatorHandler');
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
        $this->reserveSlugUrl();

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
            Route::get('/show/{blogId}', ['as' => 'show', 'uses' => 'BlogController@showId']);
            Route::get('/edit/{blogId}', ['as' => 'edit', 'uses' => 'BlogController@edit']);
            Route::post('/update', ['as' => 'update', 'uses' => 'BlogController@update']);
            Route::post('/delete/{blogId}', ['as' => 'delete', 'uses' => 'BlogController@delete']);
            Route::post('/set_favorite', ['as' => 'favorite', 'uses' => 'BlogController@setFavoriteState']);
            Route::get('/items_json', ['as' => 'items_json', 'uses' => 'BlogController@getItemsForJson']);
            Route::get('/{slug}', ['as' => 'show_slug', 'uses' => 'BlogController@showSlug']);
        });

        Route::group([
            'prefix' => Plugin::getId() . '/template',
            'as' => 'blog.template.',
            'namespace' => 'Xpressengine\Plugins\XeBlog\Controllers',
            'middleware' => ['web']
        ], function () {
            Route::get('/get_items', ['as' => 'get_items', 'uses' => 'BlogTemplateController@index']);
            Route::post('/store', ['as' => 'store', 'uses' => 'BlogTemplateController@store']);
            Route::post('/delete/{templateId}', ['as' => 'delete_item', 'uses' => 'BlogTemplateController@delete']);
            Route::get('/{templateId}', ['as' => 'get_item', 'uses' => 'BlogTemplateController@getItem']);
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
                Route::post('/set_blog_state', ['as' => 'set_blog_state', 'uses' => 'BlogSettingController@setBlogState']);
                Route::post('/trash_clear', ['as' => 'trash_clear', 'uses' => 'BlogSettingController@trashClear']);

                Route::get('/setting/{type?}', [
                    'as' => 'setting',
                    'uses' => 'BlogSettingController@editSetting',
                    'settings_menu' => 'contents.manageBlog.blogSetting'
                ]);
                Route::post('/setting', ['as' => 'store_setting', 'uses' => 'BlogSettingController@updateSetting']);

                Route::get('/edit_taxonomy_config/{taxonomyId}', ['as' => 'edit_taxonomy_config', 'uses' => 'BlogSettingController@editTaxonomyConfig']);
                Route::post('/update_taxonomy_config', ['as' => 'update_taxonomy_config', 'uses' => 'BlogSettingController@updateTaxonomyConfig']);
                Route::post('/delete_taxonomy', ['as' => 'delete_taxonomy', 'uses' => 'BlogSettingController@deleteTaxonomy']);

                Route::post('/store_taxonomy', ['as' => 'store_taxonomy', 'uses' => 'BlogSettingController@storeTaxonomy']);
                Route::post('/update_permission', ['as' => 'update_permission', 'uses' => 'BlogSettingController@updatePermission']);
            });
        });

        $taxonomyHandler = app('xe.blog.taxonomyHandler');
        $taxonomyUrls = $taxonomyHandler->getTaxonomyUseUrls();
        foreach ($taxonomyUrls as $taxonomyId => $taxonomyUrl) {
            Route::group([
                'prefix' => $taxonomyUrl,
                'as' => sprintf('blog.%s.', $taxonomyUrl),
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

        foreach ($menus as $id => $menu) {
            \XeRegister::push('settings/menu', $id, $menu);
        }
    }

    protected function reserveSlugUrl()
    {
        $slugHandler = app('xe.blog.slugHandler');

        $slugHandler->setReserved([
            'create',
            'store',
            'show',
            'edit',
            'update',
            'delete',
            'set_favorite',
            'items_json',
        ]);
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
        
        // active required plugins
        Artisan::call('plugin:activate', ['plugin' => 'xe_blockeditor']);
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

        /** @var BlogPermissionHandler $blogPermissionHandler */
        $blogPermissionHandler = app('xe.blog.permissionHandler');
        $blogPermissionHandler->storeDefaultPermission();

        /** @var BlogConfigHandler $configHandler */
        $configHandler = app('xe.blog.configHandler');
        try {
            $configHandler->storeBlogConfig();
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
        }

        /** @var DocumentHandler $documentConfigHandler */
        $documentConfigHandler = app('xe.document');
        try {
            $documentConfigHandler->createInstance(Plugin::getId(), ['instanceId' => Plugin::getId(), 'group' => Plugin::getId()]);
        } catch (\Exception $e) {
            \Log::debug($e->getMessage());
        }
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

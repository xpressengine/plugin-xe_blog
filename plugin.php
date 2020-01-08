<?php

namespace Xpressengine\Plugins\Post;

use Route;
use XeInterception;
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
    }

    protected function route()
    {
        // implement code

        Route::fixed(
            $this->getId(),
            function () {
                Route::get('/', [
                    'as' => 'post::index','uses' => 'Xpressengine\\Plugins\\Post\\Controllers\\Controller@index'
                ]);
            }
        );
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

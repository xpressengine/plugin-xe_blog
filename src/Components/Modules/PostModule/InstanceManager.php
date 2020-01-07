<?php

namespace Xpressengine\Plugins\Post\Components\Modules\PostModule;

use XeDB;
use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\Post\Handlers\PostConfigHandler;

class InstanceManager
{
    /** @var DocumentHandler $documentHandler */
    protected $documentHandler;

    /** @var PostConfigHandler $postConfigHandler */
    protected $postConfigHandler;

    public function __construct($documentHandler, $postConfigHandler)
    {
        $this->documentHandler = $documentHandler;
        $this->postConfigHandler = $postConfigHandler;
    }

    public function createModule($itemParams)
    {
        $postModuleInstanceId = $itemParams['id'];

        XeDB::beginTransaction();
        try {
            app('xe.editor')->setInstance($postModuleInstanceId, 'editor/blockeditor@blockeditor');

            $this->documentHandler->createInstance($postModuleInstanceId, $itemParams);

            $defaultPostConfig = $this->postConfigHandler->getDefaultConfigAttributes();
            $defaultPostConfig['postInstanceId'] = $postModuleInstanceId;
            $this->postConfigHandler->addConfig($defaultPostConfig);
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }

    public function deleteModule($postInstanceId)
    {
        XeDB::beginTransaction();
        try {
            $this->documentHandler->destroyInstance($postInstanceId);

            $postConfigEntity = $this->postConfigHandler->get($postInstanceId);
            $this->postConfigHandler->remove($postConfigEntity);
        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }
}

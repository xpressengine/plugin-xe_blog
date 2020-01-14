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
        XeDB::beginTransaction();
        try {

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

        } catch (\Exception $e) {
            XeDB::rollback();

            throw $e;
        }
        XeDB::commit();
    }
}

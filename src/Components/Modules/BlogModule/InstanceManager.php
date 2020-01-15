<?php

namespace Xpressengine\Plugins\XeBlog\Components\Modules\BlogModule;

use XeDB;
use Xpressengine\Document\DocumentHandler;
use Xpressengine\Plugins\XeBlog\Handlers\BlogConfigHandler;

class InstanceManager
{
    /** @var DocumentHandler $documentHandler */
    protected $documentHandler;

    /** @var BlogConfigHandler $blogConfigHandler */
    protected $blogConfigHandler;

    public function __construct($documentHandler, $blogConfigHandler)
    {
        $this->documentHandler = $documentHandler;
        $this->blogConfigHandler = $blogConfigHandler;
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

    public function deleteModule($blogInstanceId)
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

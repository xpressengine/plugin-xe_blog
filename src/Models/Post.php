<?php

namespace Xpressengine\Plugins\Post\Models;

use Xpressengine\Document\Models\Document;

class Post extends Document
{
    public function content()
    {
        return compile($this->instance_id, $this->content, $this->format === static::FORMAT_HTML);
    }
}

<?php

namespace Xpressengine\Plugins\XeBlog\Interfaces;

use Xpressengine\Plugins\XeBlog\Models\Blog;

interface Jsonable
{
    /**
     * @return string
     */
    public function getTypeName();

    /**
     * @param Blog $blog
     *
     * @return array
     */
    public function getJsonData(Blog $blog);
}

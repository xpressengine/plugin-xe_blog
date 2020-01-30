<?php

namespace Xpressengine\Plugins\XeBlog\Interfaces;

interface Orderable
{
    public function getOrder($query, $attributes);
}

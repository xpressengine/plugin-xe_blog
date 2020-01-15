<?php

namespace Xpressengine\Plugins\XeBlog\Interfaces;

use Illuminate\Database\Query\Builder;

interface Searchable
{
    public function getItems($query, array $attributes);
}

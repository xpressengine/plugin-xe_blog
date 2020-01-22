<?php

namespace Xpressengine\Plugins\XeBlog\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Xpressengine\Support\Exceptions\HttpXpressengineException;

class NotFoundBlogException extends HttpXpressengineException
{
    protected $statusCode = Response::HTTP_GONE;
    protected $message = 'xe_blog::notFoundBlog';
}

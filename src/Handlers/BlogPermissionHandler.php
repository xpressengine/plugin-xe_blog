<?php

namespace Xpressengine\Plugins\XeBlog\Handlers;

use Xpressengine\Http\Request;
use Xpressengine\Permission\Grant;
use Xpressengine\Permission\PermissionHandler;
use Xpressengine\Permission\PermissionSupport;
use Xpressengine\User\Rating;

class BlogPermissionHandler
{
    use PermissionSupport;

    const ACTION_CREATE = 'create';
    const ACTION_READ = 'read';
    const ACTION_LIST = 'list';
    const ACTION_MANAGE = 'manage';

    protected $permissionName = 'xe_blog';

    protected $actions = [
        self::ACTION_CREATE,
        self::ACTION_READ,
        self::ACTION_LIST,
        self::ACTION_MANAGE
    ];

    protected $permissionHandler;

    public function __construct(PermissionHandler $permissionHandler)
    {
        $this->permissionHandler = $permissionHandler;
    }

    public function getPerms()
    {
        return $this->getPermArguments($this->getPermissionName(), $this->actions);
    }

    public function getPermissionName()
    {
        return $this->permissionName;
    }

    public function getPermission()
    {
        return $this->permissionHandler->get($this->getPermissionName());
    }

    public function storeDefaultPermission()
    {
        $grant = new Grant();

        foreach ($this->actions as $action) {
            if ($action === self::ACTION_MANAGE || $action === self::ACTION_CREATE) {
                $perm = [
                    Grant::RATING_TYPE => Rating::MANAGER,
                    Grant::GROUP_TYPE => [],
                    Grant::USER_TYPE => [],
                    Grant::EXCEPT_TYPE => []
                ];
            } elseif ($action === self::ACTION_LIST || $action === self::ACTION_READ) {
                $perm = [
                    Grant::RATING_TYPE => Rating::GUEST,
                    Grant::GROUP_TYPE => [],
                    Grant::USER_TYPE => [],
                    Grant::EXCEPT_TYPE => []
                ];
            } else {
                $perm = [
                    Grant::RATING_TYPE => Rating::USER,
                    Grant::GROUP_TYPE => [],
                    Grant::USER_TYPE => [],
                    Grant::EXCEPT_TYPE => []
                ];
            }

            $grant = $this->addGrant($grant, $action, $perm);
        }

        $this->permissionHandler->register($this->getPermissionName(), $grant);

        return $grant;
    }

    public function updatePermission(Request $request)
    {
        $this->permissionRegister($request, $this->getPermissionName(), $this->actions);
    }

    public function addGrant(Grant $grant, $action, $permissions)
    {
        foreach ($permissions as $type => $value) {
            $grant->add($action, $type, $value);
        }

        return $grant;
    }
}

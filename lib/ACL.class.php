<?php
use Admin\User;

class ACL
{

    /**
     * 绑定权限
     * 
     * @param unknown $permission
     *            权限名
     * @throws PermissionException 无权限时抛出
     */
    public static function requirePermission($permission)
    {
        $has_permission = self::checkPermission($permission);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, $permission);
        }
    }

    /**
     * 检查是否有某权限
     * 
     * @param unknown $permission
     *            权限名
     * @return boolean 是否有权限
     */
    public static function checkPermission($permission)
    {
        return User::checkPermission($permission);
    }

    /**
     * 绑定ORM权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @param unknown $permission
     *            权限名
     * @throws PermissionException 无权限时抛出
     */
    public static function requireDBObjectPermission(\DBModel $dbmodel, $permission)
    {
        $has_permission = self::checkDBObjectPermission($dbmodel, $permission);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, User::getDBObjectPermission($dbmodel, $permission));
        }
    }

    /**
     * 检查是否有某ORM对象权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @param unknown $permission
     *            权限名
     * @return boolean 是否有权限
     */
    public static function checkDBObjectPermission(\DBModel $dbmodel, $permission)
    {
        return User::checkDBObjectPermission($dbmodel, $permission);
    }

    /**
     * 绑定ORM select权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @throws PermissionException 无权限时抛出
     */
    public static function requireDBObjectPermissionSelect(\DBModel $dbmodel)
    {
        $has_permission = self::checkDBObjectPermissionSelect($dbmodel);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, User::getDBObjectPermission($dbmodel, "select"));
        }
    }

    /**
     * 检查是否有ORM对象select权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @return boolean 是否有权限
     */
    public static function checkDBObjectPermissionSelect(\DBModel $dbmodel)
    {
        return User::checkDBObjectPermissionSelect($dbmodel);
    }

    /**
     * 绑定ORM insert权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @throws PermissionException 无权限时抛出
     */
    public static function requireDBObjectPermissionInsert(\DBModel $dbmodel)
    {
        $has_permission = self::checkDBObjectPermissionInsert($dbmodel);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, User::getDBObjectPermission($dbmodel, "insert"));
        }
    }

    /**
     * 检查是否有ORM对象的insert权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @return boolean 是否有权限
     */
    public static function checkDBObjectPermissionInsert(\DBModel $dbmodel)
    {
        return User::checkDBObjectPermissionInsert($dbmodel);
    }

    /**
     * 绑定ORM update权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @throws PermissionException 无权限时抛出
     */
    public static function requireDBObjectPermissionUpdate(\DBModel $dbmodel)
    {
        $has_permission = self::checkDBObjectPermissionUpdate($dbmodel);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, User::getDBObjectPermission($dbmodel, "update"));
        }
    }

    /**
     * 检查是否有ORM对象的update权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @return boolean 是否有权限
     */
    public static function checkDBObjectPermissionUpdate(\DBModel $dbmodel)
    {
        return User::checkDBObjectPermissionUpdate($dbmodel);
    }

    /**
     * 绑定ORM delete权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @throws PermissionException 无权限时抛出
     */
    public static function requireDBObjectPermissionDelete(\DBModel $dbmodel)
    {
        $has_permission = self::checkDBObjectPermissionDelete($dbmodel);
        if (! $has_permission) {
            throw new PermissionException('permission deny', - 1, User::getDBObjectPermission($dbmodel, "delete"));
        }
    }

    /**
     * 检查是否有ORM对象的delete权限
     * 
     * @param \DBModel $dbmodel
     *            orm对象
     * @return boolean 是否有权限
     */
    public static function checkDBObjectPermissionDelete(\DBModel $dbmodel)
    {
        return User::checkDBObjectPermissionDelete($dbmodel);
    }
}
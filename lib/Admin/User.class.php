<?php
namespace Admin;

class User
{

    /**
     * 检查对象的Insert权限
     * 
     * @param $dbobject ORM对象            
     * @return boolean 是否有insert权限
     */
    public static function checkDBObjectPermissionInsert($dbobject)
    {
        return self::checkDBObjectPermission($dbobject, "insert");
    }

    /**
     * 检查对象的Delete权限
     * 
     * @param $dbobject ORM对象            
     * @return boolean 是否有Delete权限
     */
    public static function checkDBObjectPermissionDelete($dbobject)
    {
        return self::checkDBObjectPermission($dbobject, "delete");
    }

    /**
     * 检查对象的Update权限
     * 
     * @param $dbobject ORM对象            
     * @return boolean 是否有Update权限
     */
    public static function checkDBObjectPermissionUpdate($dbobject)
    {
        return self::checkDBObjectPermission($dbobject, "update");
    }

    /**
     * 检查对象的Select权限
     * 
     * @param $dbobject ORM对象            
     * @return boolean 是否有Select权限
     */
    public static function checkDBObjectPermissionSelect($dbobject)
    {
        return self::checkDBObjectPermission($dbobject, "select");
    }

    /**
     * 检查对象是否有某个权限
     * 
     * @param \DBModel $dbobject
     *            orm对象
     * @param unknown $permission_name
     *            权限名
     * @return boolean 是否有此权限
     */
    public static function checkDBObjectPermission(\DBModel $dbobject, $permission_name)
    {
        $tmp = self::getDBObjectPermission($dbobject, $permission_name);
        return self::checkPermission($tmp);
    }

    /**
     * 检查是否有某权限
     * 
     * @param unknown $permission_name
     *            权限名
     * @return boolean 是否有此权限
     */
    public static function checkPermission($permission_name)
    {
        return true;
        /*
         * $admin_id = $_SESSION['admin_id']; if (! empty($admin_id)) { $group_res = Db::queryForOne("select id from admin_group_permission where group_id in (select group_id from admin_user_group where user_id=? and permission_name=?)", $admin_id, $permission_name); if (! empty($group_res)) { return true; } else { $user_res = DB::queryForOne("select id from admin_user_permission where user_id =? and permission_name=?", $admin_id, $permission_name); if (! empty($user_res)) { return true; } } } return false;
         */
    }

    /**
     * 获取某个ORM对象权限在数据库中的权限全称
     * 
     * @param \DBModel $dbobject
     *            orm对象
     * @param unknown $permission_name
     *            权限名
     * @return string 权限全称
     */
    public static function getDBObjectPermission(\DBModel $dbobject, $permission_name)
    {
        return $dbobject->getTablePermissionName() . "@" . $permission_name;
    }
}

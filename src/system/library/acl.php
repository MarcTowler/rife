<?php
/**
 * ACL Class
 *
 * This is what defines who everybody is and where they will go
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
class acl {

    public  $permissions = array();
    public  $userID      = 0;
    public  $roles       = array();
    private $db;


    /**
     * Constructor
     * 
     * Set's the user's ID for the rest of the class
     *
     * @access public
     * @param integer   Passed User ID
     *
     */
    public function __construct($uid = 0)
    {
    	
        global $loader;

        $this->db = $loader->loaded['database'];

        //If no uid is passed or they are marked as guest.... Change the 2 to a config value when singleton is implimented
        if($uid <= 0/* || $uid == $loader->config['user']['guest_id']*/)
        {
            $this->userID = (isset($_SESSION['userID'])) ? floatval($_SESSION['userID']) : 0;
        } else {
            $this->userID = floatval($uid);
        }

        $this->roles = $this->getUserRoles();
        $this->roles = array();
        $this->buildACL();
    }

    /**
     * getUserRoles
     * 
     * pulls all of the available roles for the current user
     *
     * @access public
     * 
     * @return array
     */
    public function getUserRoles()
    {
        $tmp = array();

        while($row = $this->db->select('user_roles', 'userID = ' . floatval($this->userID)))
        {
            $tmp[] = $row['roleID'];
        }

        return $tmp;
    }

    /**
     * getAllRoles
     * 
     * returns all the roles that are present in the system
     *
     * @access public
     * @param  string    Specifies the output format
     *
     * @return array
     */
    private function getAllRoles($format = 'id')
    {
        $format = strtolower($format);
        $this->db->select('*', 'roles', '', 'rolename ASC');

        $tmp = array();
        while($row = $this->db->select('roles', '', '', '', '*', 'rolename ASC'))
        {
            if($format == 'full')
            {
                $tmp[] = array("ID" => $row['id'], "Name" => $row['roleName']);
            } else {
                $tmp[] = $row['id'];
            }
        }

        return $tmp;
    }

    /**
     * buildACL
     * 
     * The function that builds the main ACL
     *
     * @access public
     * @param  string    Specifies the output format
     *
     * @return array
     */
    private function buildACL()
    {
        //we need to get the user's roles....
        if(count($this->roles) > 0)
        {
            $this->permissions = array_merge($this->permissions, 
                                 $this->getRolePerms($this->roles));
        }

        //what can the individual user do???
        $this->permissions = array_merge($this->permissions, 
                             $this->getUserPerms($this->userID));
    }

    /**
     * getPermKeyFromID
     * 
     * returns the permission key based on the passed ID
     *
     * @access public
     * @param  string    The Permission ID number
     *
     * @return string
     */
    public function getPermKeyFromID($permID)
    {
        $return = $this->db->select('permission', 'id = ' . floatval($permID), '', '', 'permKey');

        return $return[0];
    }

    /**
     * getPermNameFromID
     * 
     * returns the permission name based on the passed ID
     *
     * @access public
     * @param  string    The Permission ID number
     *
     * @return string
     */
    private function getPermNameFromID($permID)
    {
        $row = $this->db->select('permissions', 'id = ' . floatval($permID), '', '', 'permName');

        return $row[0];
    }

    /**
     * getRoleNameFromID
     * 
     * returns the user's role name based on the passed ID
     *
     * @access public
     * @param  string    The Role ID number
     *
     * @return string
     */
    private function getRoleNameFromID($roleID)
    {
        $row = $this->db->select('roles', 'id = ' . floatval($roleID), '', '', 'roleName');

        return $row[0];
    }

    /**
     * getUsername
     * 
     * returns the username for the current visitor
     *
     * @access public
     * @param  string    The User's ID number
     *
     * @return string
     */
    private function getUsername($userID)
    {
        $row = $this->db->select('users', 'id = ' . floatval($userID), '', '', 'username');

        return $row[0];
    }

    private function getRolePerms($roles)
    {
        $tmp;

        if(is_array($role))
        {
            $tmp = $this->db->select('*', 'role_perms', 'roleID IN (' . implode(',', $role) . ')');
        } else {
            $tmp = $this->db->select('*', 'role_perms', 'roleID = ' . floatval($role));
        }

        $perms = array();

        //while($row = $this->db->fetch('array'))
        while($row = $tmp)
        {
            $key = strtolower($this->getPermKeyFromID($row['permID']));

            if($key === '')
            {
                continue;
            }

            $hp = ($row['value'] === '1') ? true : false;

            $perms[$key] = array (
                'perm'       => $key,
                'inheritted' => true,
                'value'      => $hp,
                'Name'       => $this->getPermNameFromID($row['permID']),
                'ID'         => $row['permID'],
            );
        }

        return $perms;
    }

    private function getUserPerms($userID)
    {
        $perms = array();

        while($row = $this->db->select('user_perms', 'userID = ' . 
              floatval($userID)))
        {
            $key = strtolower($this->getPermKeyFromID($row['permID']));

            if($key === '')
            {
                continue;
            }

            $hp = ($row['value'] === '1') ? true : false;

            $perms[$key] = array (
                'perm'       => $key,
                'inheritted' => false,
                'value'      => $hp,
                'Name'       => $this->getPermNameFromID($row['permID']),
                'ID'         => $row['permID'],
            );
        }

        return $perms;
    }

    private function getAllPerms($format = 'ids')
    {
        $format = strtolower($format);

        $response = array();
        while($row = $this->db->select('permissions'))
        {
            if($format == 'full')
            {
                $resp[$row['permKey']] = array (
                    'ID'   => $row['id'],
                    'Name' => $row['permName'],
                    'Key'  => $row['permKey'],
                );
                    
            } else {
                $resp[] = $row['ID'];
            }
        }

        return $resp;
    }

    public function userHasRole($roleID)
    {
        foreach($this->roles as $key => $value)
        {
            if(floatval($value) === floatval($roleID))
            {
                return true;
            }
        }
        return false;
    }

    public function hasPermission($permKey)
    {
        $permKey = strtolower($permKey);

        if(array_key_exists($permKey, $this->permissions))
        {
            if($this->permissions[$permKey]['value'] === '1' 
               || $this->permissions[$permKey]['value'] === true)
            {
                return true;
            } else {
                return false;
            }
        }
    }
}

<?php
/**
 * Database Base Class
 *
 * Abstract class to setup the required functions when adding new supported DB types
 *
 * @package		Framework
 * @author		Marc Towler <marc.towler@designdeveloprealize.com>
 * @copyright	Copyright (c) 2008 - 2011 Design Develop Realize
 * @license		http://www.designdeveloprealize.com/products/framework/license.html
 * @link		http://www.designdeveloprealize.com
 * @since		Version 0.1
 * @filesource
 */
abstract class Database {
    abstract protected function connect();
    abstract protected function disconnect();
    abstract protected function prep($query, /*$type,*/ $param);
    abstract protected function query();
    abstract protected function fetch($type = 'object');
    abstract protected function backup_tables($tables);

    //Selection is either * or coloumn names, store it in an array and split
    abstract protected function select($selection = array(), $table, $where, $order = '', $join = '');
    abstract protected function insert($table, $keys = array(), $values = array());
    abstract protected function update($table, $values, $where, $orderby = array(), $limit = false);
    abstract protected function delete($table, $where = array(), $like = array(), $limit = false);
}

<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011-2012, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\plugin\cache;

/*
 *  The 'Memcached' class is used to facilitate storage and retrieval of
 *  data from a Memcached server.
 *
 *  @package plugin
 */
class Memcached extends \nx\core\Object {

   /**
    *  The Memcached object.
    *
    *  @var object
    *  @access protected
    */
    protected $_cache;

   /**
    *  Loads the configuration settings for Memcached.
    *
    *  @param array $config    The configuration settings, which can take two
    *                          options:
    *                          'host' - The hostname of the memcached server.
    *  @access public
    *  @return void
    */
    public function __construct(array $config = array()) {
        $defaults = array(
            'host' => 'localhost'
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Creates a new instance of Memcached and adds a server if one does
    *  not exist using the provided host.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        $this->_cache = new \Memcached();
        $this->add_server($this->_config['host']);
    }

   /**
    *  Adds a server to the server pool.
    *
    *  @param string $host    The hostname of the memcached server.
    *  @param int $weight     The weight of the server relative to the total
    *                         weight of all the servers in the pool.  This
    *                         controls the probability of the server being
    *                         selected for operations, and usually corresponds
    *                         to the amount of memory available to memcached
    *                         on that server.
    *  @param int $port       The port on which memcached is running.
    *  @access public
    *  @return bool
    */
    public function add_server($host, $weight = 0, $port = 11211) {
        return $this->_cache->addServer($host, $port, $weight);
    }

   /**
    *  Deletes an item.
    *
    *  @param string $key           The key to be deleted.
    *  @param string $server_key    The key identifying the server to delete
    *                               the value from.
    *  @param int $time             The amount of time the server will wait to
    *                               delete the item.
    *  @access public
    *  @return bool
    */
    public function delete($key, $server_key = '', $time = 0) {
        return $this->_cache->deleteByKey($server_key, $key, $time);
    }

   /**
    *  Invalidates all items in the cache.
    *
    *  @param int $delay    Number of seconds to wait before invalidating the
    *                       items.
    *  @access public
    *  @return bool
    */
    public function flush($delay = 0) {
        return $this->_cache->flush($delay);
    }

   /**
    *  Retrieves an item.  Returns false if the key is not found.
    *
    *  @param string $key           The key of the item to retrieve.
    *  @param string $server_key    The key identifying the server
    *                               to retrieve the value from.
    *  @access public
    *  @return mixed
    */
    public function retrieve($key, $server_key = '') {
        return $this->_cache->getByKey($server_key, $key);
    }

   /**
    *  Stores an item.
    *
    *  @param string $key           The key under which to store the value.
    *  @param mixed $value          The value to be stored.
    *  @param string $server_key    The key identifying the server to store
    *                               the value on.
    *  @param int $expiration       The expiration time.  Can be number of
    *                               seconds from now.  If this value exceeds
    *                               60*60*24*30 (number of seconds in 30 days),
    *                               the value will be interpreted as a UNIX
    *                               timestamp.
    *  @access public
    *  @return bool
    */
    public function store($key, $value, $server_key = '', $expiration = 0) {
        return $this->_cache->setByKey($server_key, $key, $value, $expiration);
    }

}
?>

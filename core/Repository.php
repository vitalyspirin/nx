<?
namespace core;

class Repository implements PluginInterfaceDB, PluginInterfaceCache
{
    private $_db;
    private $_cache;
    
    public function __construct(PluginInterfaceDB $db, PluginInterfaceCache $cache)
    {
        $this->_db = $db;
        $this->_cache = $cache;
    }
    
    public function get_database()
    {
        return $this->_db;
    }
    
    public function get_cache()
    {
        return $this->_cache;
    }
    
    //-------------------------------------------------
    // by contract to PluginInterfaceCache
    //-------------------------------------------------
    public function add_to_cache($key, $value, $expiration=0, $server_key='')
    {
        return $this->_cache->add_to_cache($key, $value, $expiration, $server_key);    
    }
    
    public function delete_from_cache($key, $time=0, $server_key='')
    {
        return $this->_cache->delete_from_cache($key, $time, $server_key);
    }
    
    public function flush_cache($delay=0)
    {
        return $this->_cache->flush_cache($delay);
    }
    
    public function get_from_cache($key, $cache_callback=null, &$cas_token=null, $server_key='')
    {
        return $this->_cache->get_from_cache($key, $cache_callback, $cas_token, $server_key);
    }
    
    public function replace_in_cache($key, $value, $expiration=0, $server_key='')
    {
        return $this->_cache->replace_in_cache($key, $value, $expiration, $server_key);
    }
    
    public function set_in_cache($key, $value, $expiration=0, $server_key='')
    {
        return $this->_cache->set_in_cache($key, $value, $expiration, $server_key);
    }
    
    //-------------------------------------------------
    // by contract to PluginInterfaceDB:
    //-------------------------------------------------
    public function affected_rows()
    {
        return $this->_db->affected_rows();
    }

     public function close()
     {
        return $this->_db->close();
     }

     public function delete_object($obj)
     {
        return $this->_db->delete_object($obj);
     }

     public function fetch()
     {
        return $this->_db->fetch();
     }

     public function fetch_all()
     {
        return $this->_db->fetch_all();
     }

     public function insert($obj)
     {
        return $this->_db->insert($obj);
     }

     public function insert_id()
     {
        return $this->_db->insert_id();
     }

     public function load_object($obj, $id)
     {
        return $this->_db->load_object($obj, $id);
     }

     public function num_rows()
     {
        return $this->_db->num_rows();
     }

     public function query($sql, $parameters=null)
     {
        return $this->_db->query($sql, $parameters);
     }

     public function query_first($sql, $parameters=null)
     {
        return $this->_db->query_first($sql, $parameters);
     }

     public function update($obj, $where = '1')
     {
        return $this->_db->update($obj, $where);
     }

     public function upsert($obj)
     {
        return $this->_db->upsert($obj);
     }
    
}

?>

<?php

namespace nx\test\plugin\cache;

use nx\plugin\cache\Memcached;

class MemcachedTest extends \PHPUnit_Framework_TestCase {

    protected $_cache;

    public function setUp() {
        $host = 'localhost';
        $this->_cache = new Memcached(compact('host'));
        $this->_cache->flush();
    }

    public function test_SetInAndGetFromCache_ReturnsOriginalValue() {
        $original_key = 'test';
        $original_value = 'value';
        $this->_cache->store($original_key, $original_value);
        $retrieved_value = $this->_cache->retrieve($original_key);
        $this->assertEquals($original_value, $retrieved_value);
    }

    public function test_DeleteKeyAndGetFromCache_ReturnsFalse() {
        $original_key = 'test';
        $original_value = 'value';
        $this->_cache->store($original_key, $original_value);
        $this->_cache->delete($original_key);
        $retrieved_value = $this->_cache->retrieve($original_key);
        $this->assertFalse($retrieved_value);
    }

    public function test_FlushCacheAndGetFromCache_ReturnsFalse() {
        $original_key = 'test';
        $original_value = 'value';
        $this->_cache->store($original_key, $original_value);
        $this->_cache->flush();
        $retrieved_value = $this->_cache->retrieve($original_key);
        $this->assertFalse($retrieved_value);
    }

}
?>

<?php

/**
 * NX
 *
 * @author    Nick Sinopoli <NSinopoli@gmail.com>
 * @copyright Copyright (c) 2011, Nick Sinopoli
 * @license   http://opensource.org/licenses/bsd-license.php The BSD License
 */

namespace nx\core;

/*
 *  The `Request` class is used to handle all data
 *  pertaining to an incoming HTTP request.
 *
 *  @package core
 */
class Request extends Object {

   /**
    *  The POST/PUT/DELETE data.
    *
    *  @var array
    *  @access public
    */
    public $data = array();

   /**
    *  The environment variables.
    *
    *  @var array
    *  @access protected
    */
    protected $_env = array();

   /**
    *  The GET data.
    *
    *  @var array
    *  @access public
    */
    public $query = array();

   /**
    *  The url of the request.
    *
    *  @var string
    *  @access public
    */
    public $url;

    /**
     *  Sets the configuration options.
     *
     *  @param array $config        The configuration options.
     *  @access public
     *  @return void
     */
    public function __construct(array $config = array()) {
        $defaults = array(
            'data'       => array(),
            'query'      => array()
        );
        parent::__construct($config + $defaults);
    }

   /**
    *  Organizes the data pertinent to the incoming request.
    *
    *  @access protected
    *  @return void
    */
    protected function _init() {
        parent::_init();

        $defaults = array(
            'CONTENT_TYPE'   => 'text/html',
            'REQUEST_METHOD' => 'GET'
        );
        $this->_env = $_SERVER + $_ENV + $defaults;

        if ( isset($this->_env['SCRIPT_URI']) ) {
            $this->_env['HTTPS'] =
                ( strpos($this->_env['SCRIPT_URI'], 'https://') === 0 );
        } elseif ( isset($this->_env['HTTPS']) ) {
            $this->_env['HTTPS'] =
                ( !empty($this->_env['HTTPS']) && $this->_env['HTTPS'] !== 'off' );
        } else {
            $this->_env['HTTPS'] = false;
        }

        $this->_env['PHP_SELF'] = str_replace('\\', '/', str_replace(
            $this->_env['DOCUMENT_ROOT'], '', $this->_env['SCRIPT_FILENAME']
        ));

        if ( !empty($_GET['url']) ) {
            $this->url = rtrim($_GET['url'], '/');
        } elseif ( $uri = $this->_env['REQUEST_URI'] ) {
            $this->url = parse_url($uri, PHP_URL_PATH);
        } else {
            $this->url = '/';
        }

        $this->query = $this->_config['query'];
        if ( isset($_GET) ) {
            $this->query += $_GET;
        }

        $this->data = $this->_config['data'];
        if ( isset($_POST) ) {
            $this->data += $_POST;
        }

        $override ='HTTP_X_HTTP_METHOD_OVERRIDE';
        if ( isset($this->data['_method']) ) {
            $this->_env[$override] = strtoupper($this->data['_method']);
            unset($this->data['_method']);
        }
        if ( !empty($this->_env[$override]) ) {
            $this->_env['REQUEST_METHOD'] = $this->_env[$override];
        }

        $method = strtoupper($this->_env['REQUEST_METHOD']);

        if ( $method == 'PUT' || $method == 'DELETE' ) {
            $stream = fopen('php://input', 'r');
            parse_str(stream_get_contents($stream), $this->data);
            fclose($stream);
        }

        $this->data = $this->_extract(
            $this->data, $this->_config['namespaces']['model']
        );

    }

   /**
    *  Returns an environment variable.
    *
    *  @param string $key           The environment variable.
    *  @access public
    *  @return mixed
    */
    public function env($key) {
        $key = strtoupper($key);
        return ( isset($this->_env[strtoupper($key)]) )
            ? $this->_env[strtoupper($key)]
            : null;
    }

   /**
    *  Extracts request data and returns it as a collection of objects (if an
    *  object was bound to it via the form) and `key` => `value` pairs (if no
    *  object is bound).
    *
    *  @param array $data               The request data.
    *  @param string $model_namespace   The namespace of the application models.
    *  @access protected
    *  @return mixed
    */
    protected function _extract($data, $model_namespace) {
        $collection = array();
        foreach ( $data as $child_key => $child ) {
            if ( !is_array($child) ) { // name = 'username'
                $collection[$child_key] = $child;
                continue;
            }

            $loc = strrpos($child_key, '|');
            if ( $loc !== false ) { // name = 'User|id[username]'
                $id = substr($child_key, $loc + 1);
                $class_name = substr($child_key, 0, $loc);
                $class = $model_namespace . $class_name;
                $obj = new $class(array('id' => $id));
                foreach ( $child as $key => $value ) {
                    $obj->$key = $value;
                }
                $collection[$class_name][] = $obj;
                continue;
            }

            // name = 'User[][username]'
            foreach ( $child as $grandchild_array ) {
                $obj_name = $model_namespace . $child_key;
                $obj = new $obj_name();
                foreach ( $grandchild_array as $key => $value ) {
                    $obj->$key = $value;
                }
                $collection[$child_key][] = $obj;
            }
        }

        return $collection;
    }

   /**
    *  Checks for request characteristics.
    *
    *  @param string $characteristic     The characteristic.
    *  @access public
    *  @return bool
    */
    public function is($characteristic) {
        switch ( $characteristic ) {
            case 'ajax':
                return ( $this->env('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' );
            case 'delete':
                return ( $this->env('REQUEST_METHOD') == 'DELETE' );
            case 'flash':
                return ( $this->env('HTTP_USER_AGENT') == 'Shockwave Flash' );
            case 'get':
                return ( $this->env('REQUEST_METHOD') == 'GET' );
            case 'head':
                return ( $this->env('REQUEST_METHOD') == 'HEAD' );
            case 'mobile':
                $mobile_user_agents = array(
                    'Android', 'AvantGo', 'Blackberry', 'DoCoMo', 'iPod',
                    'iPhone', 'J2ME', 'NetFront', 'Nokia', 'MIDP', 'Opera Mini',
                    'PalmOS', 'PalmSource', 'Plucker', 'portalmmm',
                    'ReqwirelessWeb', 'SonyEricsson', 'Symbian', 'UP\.Browser',
                    'Windows CE', 'Xiino'
                );
                $pattern = '/' . implode('|', $mobile_user_agents) . '/i';
                return (boolean) preg_match(
                    $pattern, $this->env('HTTP_USER_AGENT')
                );
            case 'options':
                return ( $this->env('REQUEST_METHOD') == 'OPTIONS' );
            case 'post':
                return ( $this->env('REQUEST_METHOD') == 'POST' );
            case 'put':
                return ( $this->env('REQUEST_METHOD') == 'PUT' );
            case 'ssl':
                return $this->env('HTTPS');
            default:
                return false;
        }
    }

}

?>

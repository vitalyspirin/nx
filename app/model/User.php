<?php

namespace app\model;

class User extends \nx\core\Model {
    public $id;

    public $username;
    public $password;
    public $email;
    public $ip;
    public $join_date;
    public $last_login;

    protected $_validators = array(
        'email' => array(
            array('email', 'message' => 'Email is invalid.')
        ),
        'username' => array(
            array('not_empty', 'message' => 'Username cannot be blank.'),
            array(
                'alphanumeric',
                'message' => 'Username must contain only alphanumeric characters.'
            ),
            array(
                'length_between',
                'options' => array(
                    'min' => '5',
                    'max' => 16
                ),
                'message' => 'Username must be between 5 and 16 characters.'
            ),
        ),
        'ip' => array(
            array('ip', 'message' => 'ip is invalid.')
        )
    );

}

?>

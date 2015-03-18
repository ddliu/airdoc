<?php
namespace ddliu\airdoc\Auth;

class BasicAuth implements AuthInterface {
    protected $realm;
    protected $users;

    public function __construct(array $users, $realm = null) {
        $this->users = $users;
        $this->realm = $realm?$realm:'Airdoc';
    }

    public function auth() {
        $user = $_SERVER['PHP_AUTH_USER'];
        $pass = $_SERVER['PHP_AUTH_PW'];
        if (isset($this->users[$user]) && $this->users[$user] === $pass) {
            return true;
        } else {
          header('WWW-Authenticate: Basic realm="'.$this->realm.'"');
          header('HTTP/1.0 401 Unauthorized');
          die ("Not authorized");
        }
    }
}
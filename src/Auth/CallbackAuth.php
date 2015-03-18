<?php
namespace ddliu\airdoc\Auth;

class CallbackAuth implements AuthInterface {
    protected $callback;
    public function construct($callback) {
        $this->callback = $callback;
    }

    public function auth() {
        return call_user_func($this->callback);
    }
}
<?php

class admin extends frontController {
    public $model;
    public $user;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->autoload_model();
        $this->user  = $this->load_user();
    }

    public function index()
    {
        if(!$this->user->hasPermission('admin_access'))
        {
            echo("not permitted to be here");
        } else {
            echo("yay");
        }
    }
}

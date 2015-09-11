<?php

class error_404 extends frontController {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->variables(array(
            'site title' => '404 Page Not Found :: Design Develop Realize',
        ));
        $this->parse('error/404', $this->toParse, true);
    }
}

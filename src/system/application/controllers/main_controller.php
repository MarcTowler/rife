<?php

class main extends frontController {

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        $this->variables(array(
            'site_title' => 'Home :: Design Develop Realize',
        ));
        $this->parse('main', $this->toParse, true);
    }

    public function services()
    {
        $this->variables(array(
            'site_title' => 'Services :: Design Develop Realize',
            ));
        $this->parse('services', $this->toParse, true);
    }

    public function about()
    {
        $this->variables(array(
	    'site_title' => 'About :: Design Develop Realize',
        ));
	$this->parse('about', $this->toParse, true);
    }

    public function contact()
    {
        $this->variables(array(
            'site_title' => 'Contact :: Design Develop Realize',
        ));
        $this->parse('contact', $this->toParse, true);
    }
}

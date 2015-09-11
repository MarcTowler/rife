<?php

class member extends frontController {
    public $model;
    public $user;

    public function __construct()
    {
        parent::__construct();

        $this->model = $this->autoload_model();
        $this->user  = $this->load_user();
        $this->user->getUserRoles();
    }

    public function index()
    {
    	//Will only list the latest post ;)
        if(!$this->user->hasPermission('blog_access'))
        {
            $array = $this->model->list_posts();

			if(empty($array))
			{
				$this->variables(array(
				    'site_title' => 'Design Develop Realize :: View Blog Posts',
				    'post_title' => 'Sorry but there are no posts to display'
				));
			} else {
                $this->variables(array(
                    'site_title' => 'Design Develop Realize :: View Blog Posts',
                    'list'       => $array[0],
                    'post_title' => $array[0]['entry_title'],
                    'link'       => str_replace(' ', '_',$array[0]['entry_title']),
                ));
			}
        } else {
            $this->variables(array(
                'site_title' => 'Error :: Design Develop Realize',
                'body'       => 'Sorry, but you do not have permission to access this',
            ));
        }

        $this->parse('blog/list', $this->toParse);
    }

    public function view()
    {
        if(!$this->user->hasPermission('blog_access'))
        {
            $array = $this->model->view_post($this->loader->loaded['routes']['queryString'][1]);

            if(is_array($array))
			{
                $this->variables(array(
                    'site_title' => 'Design Develop Realize :: View Blog Posts',
                    'post_title' => $array[0]['entry_title'],
                    'author'     => $array[0]['username'],
                    'date'       => $array[0]['date_time'],
                    'body'       => $array[0]['entry'],
                ));
			} else {
				$this->variables(array(
				    'site_title' => 'Design Develop Realize :: Error',
					'post_title' => 'The selected post does not exist',
					'author'     => '',
					'date'       => '',
					'body'       => 'The blog post that you have tried to view does not exist. If you believe that this is an error please contact an administrator.'));
			}
        } else {
            $this->variables(array(
                'site_title' => 'Error :: Design Develop Realize',
                'body'       => 'Sorry, but you do not have permission to access this',
            ));
        }

        $this->parse('blog/view', $this->toParse);
    }

    public function new_entry()
    {
        if($this->user->hasPermission('blog_create_post'))
        {
            if(array_key_exists('post', $_POST))
            {
                $tmp = array();
                $expected = array('entry_title', 'entry', 'published', 'publish');

                foreach($_POST as $key => $value)
                {
                    if(in_array($key, $expected))
                    {
                        $tmp[$key] = $value;
                    }
                }
                //temp fix until sessions are on
                $tmp['poster'] = 1;
                $tmp['date_time'] = date('Y-m-d H:i:s');

                //need to display a success page with a link to the post
                echo($this->model->add_post($tmp)); //Model function, param is always an array
            } else {
                //It is a new post, load the form!!!
                $this->variables(array(
                    'site_title' => 'Creating a Blog Entry :: Design Develop Realize',
                ));
                $this->parse('blog/entry', $this->toParse, true);
            }
        } else {
                $this->variables(array(
                    'site_title' => 'Error :: Design Develop Realize',
                    'body'       => 'Sorry, but you do not have permission to access this',
                ));
        }
    }

    public function edit()
    {
        if($this->user->hasPermission('blog_admin'))
        {
            if(array_key_exists('updateEntry', $_POST))
            {
                $result = $this->model->update_entry($_POST);
                if($result > 0)
                {
                    echo("YAY");
                } else {
                    echo("nay, nothing was changed");
                }
            } else {
                if(isset($this->loader->loaded['routes']['queryString']) && $this->loader->loaded['routes']['queryString'] != '')
                {
                    if($details = $this->model->view_post($this->loader->loaded['routes']['queryString'][1]))
                    {
                        $this->variables(array(
                            'site_title'  => 'Edit Blog Post :: Design Develop Realize',
                            'entry_title' => $details[0]['entry_title'],
                            'body'        => $details[0]['entry'],
                        ));

                        $this->parse('blog/edit_entry', $this->toParse, true);
                    } else {
                        $this->variables(array(
                            'site_title'  => 'Error :: Design Develop Realize',
                            'body'        => 'Sorry but the post you are trying to edit does not exist!',
                        ));

                        $this->parse('blog/list', $this->toParse, true);
                    }
                } else {
                    echo("nah");
                }
            }
        } else {
            $this->variables(array(
                    'site_title' => 'Error :: Design Develop Realize',
                    'body'       => 'Sorry, but you do not have permission to access this',
                ));
        }
    }

    public function delete()
    {
        if($this->user->hasPermission('blog_admin'))
        {
            //$this->db->delete(
        } else {
            $this->variables(array(
                    'site title' => 'Error :: Design Develop Realize',
                    'body'       => 'Sorry, but you do not have permission to access this',
                ));
        }
    }
}

<?php

class blog_model extends Model {

    public function list_posts()
    {
        return $this->db->select('blog_post b', 'published = 1', '', 
                          'INNER JOIN user u ON u.id = b.poster', 
                          'b.entry_title, b.date_time, u.username, b.poster',
                          'date_time DESC');
    }

    public function view_post($title)
    {
        $title = str_replace('%20', ' ', $title);
		$title = str_replace('_', ' ', $title);
        $bind  = array (
                     ':title' => $title,
                 );

        return $this->db->select('blog_post b', 'b.entry_title = :title', $bind,
                                 'INNER JOIN user u ON u.id = b.poster',
                                 'b.entry_title, b.date_time, u.username, b.poster, b.entry');
    }

    public function update_entry(array $updated)
    {

        //hack to switch yes to 1 and no to 0
        $updated['publish'] = ($updated['publish'] == 'Yes') ? 1 : 0;

        $info = array (
            'entry_title' => $updated['entry_title'],
            'entry'       => $updated['entry'],
            'published'   => $updated['publish'],
        );

        $bind = array (
            ':title'       => $info['entry_title'],
        );

        return $this->db->update('blog_post', $info, 'entry_title = :title', $bind);
    }

    public function delete_post($title)
    {
        return $this->db->delete('blog_post', 'title = :title', array(':title' => $title));
    }

    public function add_post(array $new)
    {
        return $this->db->insert("blog_post", $new);
    }
}

<?php

namespace Inc\Pages;

use \Inc\Base\BaseController;

class Admin extends BaseController
{   
    function register() {
        add_action('admin_menu', array($this, 'add_admin_pages'));
    }

    public function add_admin_pages() {
        add_menu_page(
            'Cw Uploader Easy',
            'CwUploaderEasy',
            'manage_options',
            'cw_uploader_easy',
            array($this, 'admin_index'),
            'dashicons-database-import',
            110
        );
    }

    public function admin_index() {
        require_once $this->plugin_path . 'templates/admin.php';
    }
}

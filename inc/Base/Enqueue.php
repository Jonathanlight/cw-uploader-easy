<?php

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController
{
    public function register() 
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
    }

    public function enqueue() 
    {
        wp_enqueue_style('cw_uploader_easy_style', $this->plugin_url.'assets/css/style.css');
        wp_enqueue_script('cw_uploader_easy_script', $this->plugin_url.'assets/js/script.js');
    }
}

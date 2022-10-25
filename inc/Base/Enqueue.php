<?php

namespace Inc\Base;

use \Inc\Base\BaseController;

class Enqueue extends BaseController
{
    public function register() 
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueue'));
        add_action('init', array($this, 'createTableCwUploaderEasy'));
    }

    public function enqueue() 
    {
        wp_enqueue_style('cw_uploader_easy_style', $this->plugin_url.'assets/css/style.css');
        wp_enqueue_script('cw_uploader_easy_script', $this->plugin_url.'assets/js/script.js');
    }

    public function createTableCwUploaderEasy() 
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $table_name = $wpdb->prefix . self::CW_MANAGER_UPLOADER_STOCK_TABLE;

        if ($wpdb->get_var("SHOW TABLES LIKE '". $table_name ."'"  ) != $table_name ) {
            $sql = "CREATE TABLE `${table_name}` 
            (
                `id` INT(200) NOT NULL AUTO_INCREMENT,
                `author_name` VARCHAR(200) NOT NULL,
                `path` VARCHAR(250) NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`)
            ) ENGINE = InnoDB;";

            dbDelta($sql);
            update_option('tables_created', true);
        }
    }
}

<?php

namespace Inc\Base;

class BaseController
{
    public $plugin_path;
    public $plugin_url;
    public $plugin;
    public const CW_MANAGER_UPLOADER_STOCK_TABLE = "cw_manager_upload_stock";
    
    public function __construct()
    {
        $this->plugin_path = plugin_dir_path(dirname(__FILE__, 2));
        $this->plugin_url = plugin_dir_url(dirname(__FILE__, 2));
        $this->plugin = plugin_basename(dirname(__FILE__, 3)).'/cw-uploader-easy.php';
    }
}
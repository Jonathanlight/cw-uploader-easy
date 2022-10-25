<?php

namespace Inc\Base;

use \Inc\Base\BaseController;

 class Deactivate extends BaseController
 {
     public static function deactivate() 
     {
        flush_rewrite_rules();
     }
 }
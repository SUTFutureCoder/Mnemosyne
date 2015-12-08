<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(TempLates); //引入Smarty
class Template extends Smarty {
    public $template;
    public function __construct() {
        $this -> template = new Smarty();
        $this -> template_dir = TempLates_Dir;
        $this -> compile_dir = TempLates_C;
        return $this -> template;
    }
}

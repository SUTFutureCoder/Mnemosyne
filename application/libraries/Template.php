<?php
/**
 * @package Giant.Union_CPS
 * @author houhuiyang@sankuai.com
 * @copyright Sankuai Ltd.
 * @modified 2011-05-16 
 * @todo 模板引入类
 */
if (!defined('BASEPATH')) exit('No direct script access allowed');
require_once(TempLates); //引入Smarty
class Template extends Smarty {
    public function __construct() {
        $this -> template = new Smarty();
        $this -> template_dir = TempLates_Dir;
        $this -> compile_dir = TempLates_C;
        return $this -> template;
    }
}

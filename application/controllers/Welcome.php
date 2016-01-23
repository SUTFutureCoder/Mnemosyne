<?php
/**
 * Api for Init pages
 *
 *
 * @author  *Chen <linxingchen@baidu.com>
 */
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * A class to provide api for init
 *
 */
class Welcome extends CI_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see http://codeigniter.com/user_guide/general/urls.html
	 */
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
    }
	public function index()
	{
		$this->load->view('welcome_message');
	}

    /**
     * Show how to test api in phpunit
     *
     *
     * @access public
     */
	public function testUnitTest(){
		$test = $this->input->post('test');

		echo 'hello world' . '-' . $test;
	}

	public function testSchoolId(){
		$this->load->model('school');
		echo $this->school->getSchoolId('沈阳工业大学');
	}
    public function testSmarty(){
        $test = "testSmarty";
        $this->template->assign("test", $test);
        $this->template->display("test.html");
    }

	public function testPasswd(){
		$testPwd = '12345678910abcdefghijk';
		echo password_hash($testPwd, PASSWORD_DEFAULT);
	}

	public function testLogPath(){
		$this->load->library('MLog');
		$this->load->library('CoreConst');
		MLog::trace('TEST');
	}

	public function testPlatform(){
		$this->load->library('CoreConst');
		print_r(CoreConst::PLATFORM_MOBILE);
	}
}

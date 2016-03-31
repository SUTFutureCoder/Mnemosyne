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
		$test = $this->input->post('test', true);

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
        $this->load->model('UserModels');
		$this->load->library('util/MLog');
		$this->load->library('CoreConst');
		$ret = $this->UserModels->checkUserExists('13940022196', '506200331@qq.com');
		$ret = $this->UserModels->checkUserExists('13940022196', '506200331@qq.com');
		MLog::trace(CoreConst::MODULE_ACCOUNT, 'TEST');
		MLog::warning(CoreConst::MODULE_ACCOUNT, 'TEST');
	}

	public function testPlatform(){
		$this->load->library('CoreConst');
		print_r(CoreConst::PLATFORM_MOBILE);
	}

	public function testDebugBacktrace(){
		$this->load->model('UserModels');
        CoreConst::$userId = 3;
		$ret = $this->UserModels->checkUserExists('13940022196', '506200331@qq.com');
		$ret = $this->UserModels->checkUserExists('13940022196', '506200331@qq.com');
//		echo '<pre>';
//		print_r(debug_backtrace());
//		echo '</pre>';
	}

    public function testTimer(){
        $this->load->library('util/Timer');
        Timer::start();
        sleep(2);
        Timer::start('testTimer');
        Timer::start();
        sleep(1);
        Timer::stop();
        Timer::stop('testTimer');
        Timer::stop();

        echo Timer::get('testTimer', 'Ms');
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo Timer::get('testTimer', 'us');
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo Timer::get(null, 'Ms');
        echo '<br/>';
        echo '<br/>';
        echo '<br/>';
        echo Timer::get(null, 'Ms');

    }

    public function testWebsocket(){
        $this->load->library('util/SAL');
        $arrUrl  = '127.0.0.1:2121';
        $arrData = array(
            'type' => 'publish',
            'to'   => 1455954162000,
            'content' => '消息内容',
        );
        $ret = $this->sal->doHttp('get', $arrUrl, $arrData);
        print_r($ret);
    }


    public function sendEmailByLibrary(){
        $this->load->library('util/MEmail');
        $ret = $this->memail->send(
            'message@bricksfx.cn',
            'bricks科技',
            '252142844@qq.com',
            null,
            '阿里里~阿里里~阿里阿里里',
            array(
                '/home/lin/图片/T2v6FWXzRXXXXXXXXX_!!88677701.jpg',
                '/home/lin/图片/06caf309b3de9c823dafc8456e81800a1bd84340.jpg',
            ),
            null,
            null
        );

        if (false === $ret){
            echo MLog::getLastError();
        }
    }

    public function testBosClient(){
        $this->load->library('util/BosClient');
        $this->bosclient->test();
    }

    public function testThrow(){
        throw new MException(CoreConst::MODULE_KERNEL, ErrorCodes::ERROR_PARAM_ERROR);
    }

}

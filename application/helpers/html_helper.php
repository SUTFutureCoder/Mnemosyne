<?php
function getHorizontalNavbar($nav_num)
{
    $nav_list = array(
        array("name" => "我的首页", "url" => "user/userinfo"),
        array("name" => "修改信息", "url" => "index/completeinfo"),
        //array("name" => "分发同学录", "url" => "index/send"),
        array("name" => "填写同学录", "url" => "#"),
        array("name" => "表白", "url" => "index/showlove"),
    );
    $ret = "<div class='navbar navbar-inverse' role='navigation'>
        <div class='container-fluid'>
            <div class='navbar-header'>
                <button type='button' class='navbar-toggle collapsed' data-toggle='collapse' data-target='.navbar-collapse'>
                    <span class='sr-only'></span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                    <span class='icon-bar'></span>
                </button>
                <a class='navbar-brand' href='#'>Mnemosyne</a>
            </div>
            <div class='collapse navbar-collapse'>
                <ul class='nav navbar-nav'>\n";
                    foreach($nav_list as $i => $temp)
                    {
                    $str = ($i == $nav_num) ? "active" : "";
                    $ret .= "<li class='" . $str . "'><a href='" . base_url() . $temp['url'] . "'>" . $temp['name'] . "</a></li>\n";
                    }
            $ret .= "</ul>\n";
            $ret .= "<ul class='nav navbar-nav navbar-right'>";
            $ret .= "<li class='' ><button type='button' class='btn btn-default navbar-btn'";
            $ret .=  "data-container='body' data-toggle='popover' data-placement='bottom' title='新消息提示' data-content='黄鹤带着小姨子跑了.'";
            $ret .= ">";
            $ret .= "<span class='glyphicon glyphicon-modal-window' aria-hidden='true'></span>";
            $ret .= "</button></li>";
            $ret .= "</ul>\n";


            $ret .= "</div>\n</div>\n</div>\n";
    return $ret;
}
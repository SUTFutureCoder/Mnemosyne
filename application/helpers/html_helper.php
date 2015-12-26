<?php
function getHorizontalNavbar($nav_num)
{
    $nav_list = array(
        array("name" => "我的首页", "url" => "index/login"),
        array("name" => "完善信息", "url" => "index/completeinfo"),
        array("name" => "分发同学录", "url" => "index/send"),
        array("name" => "填写同学录", "url" => "index/edit"),
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
                    $ret .= "</ul>\n</div>\n</div>\n</div>\n";
    return $ret;
}
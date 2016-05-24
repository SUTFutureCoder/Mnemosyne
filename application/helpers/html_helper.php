<?php
function getHorizontalNavbar($nav_num){
    $nav_list = array(
        array("name" => "我的首页", "url" => "user/userinfo"),
        array("name" => "修改信息", "url" => "user/index"),
        array("name" => "填写同学录", "url" => "#"),
        array("name" => "我的好友", "url" => "Friends/friendsView"),
        array("name" => "表白", "url" => "index/showlove"),
    );
    $ret = "";
    foreach($nav_list as $i => $temp)
    {
        $str = ($i == $nav_num) ? "active" : "";
        $ret .= "<li class='" . $str . "'><a href='" . base_url() . $temp['url'] . "'>" . $temp['name'] . "</a></li>\n";
    }
    return $ret;
}

function getVerticalNavtar($nav_num){
    $nav_list = array(
        array("name" => "我的好友" , "url" => "Friends/friendsView"),
        array("name" => "添加好友" , "url" => "Friends/addFriends"),
        array("name" => "好友请求" , "url" => "Friends/friendsRequest"),
    );
    $ret = "";
    foreach($nav_list as $i => $temp)
    {
        $str = ($i == $nav_num) ? "active" : "";
        $ret .= "<li class='" . $str . "'><a href='" . base_url() . $temp['url'] . "'>" . $temp['name'] . "</a></li>\n";
    }
    return $ret;
}

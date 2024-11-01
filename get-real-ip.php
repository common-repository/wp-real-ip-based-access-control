<?php
/*
Plugin Name: WP Real IP-based Access Control
Plugin URI: http://www.hitoy.org/wp-real-ip-based-access-control.html
Description: 基于用户真实IP的访问控制插件，特别适用于网站使用CDN加速，用户使用代理上网，WORDPRESS获取不到用户真实IP的情况。
Version: 1.3.1
Author: Hitoy
Author URI: http://www.hitoy.org/
 */

//安装时需要运行的函数
function _real_ip_install(){
	//添加默认处理动作
	//none表示默认，fvisit表示禁止访问，fcomment表示可以访问，禁止留言
	add_option("acl_ctrl_mode","none","yes");
	//添加访问控制列表，默认为空
	add_option("acl_ctrl_addr","","yes");
}

//卸载时需要运行的函数
function _real_ip_uninstall(){
	delete_option("acl_ctrl_mode");
	delete_option("acl_ctrl_addr");
}

//获取真实IP函数，并赋值给REMOTE_ADDR
function _get_real_ip(){
	$ip=$_SERVER["REMOTE_ADDR"];
	if(isset($_SERVER["HTTP_CLIENT_IP"])){
		$ip=$_SERVER["HTTP_CLIENT_IP"];	
	}else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
		$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
	}else if(isset($_SERVER["HTTP_X_REAL_IP"])){
		$ip=$_SERVER["HTTP_X_REAL_IP"];
	}
	//$_SERVER["REMOTE_ADDR"]=$ip;
	$ipexp="/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i";
	if(preg_match($ipexp,$ip)){
		$_SERVER["REMOTE_ADDR"]=$ip;
	}
	return $ip;
}

//判断当前用户IP是否在受控制的IP列表段中
function is_forbid(){
	$ip_acl=trim(get_option("acl_ctrl_addr"));
	$ip_list=preg_split("/[\n\r]+/i",$ip_acl);
	foreach($ip_list as $ip_seg){

		if(strstr($ip_seg,"/")){
			$mask=substr(strstr($ip_seg,"/"),1);
			$fip=iptransform(explode("/",$ip_seg)[0]);
		}else{
			$mask=32;
			$fip=iptransform($ip_seg);
		}
		$uip=iptransform(_get_real_ip());

		//对两个IP的网络号进行对比，如果相等，则次IP被禁止
		if(substr($fip,0,$mask-1)==substr($uip,0,$mask-1)){
			return true;
			break;
		}
	}
	return false;
}

///把十进制IP地址转化为二进制不带点数字
function iptransform($ipv4){
	$ipposition=explode(".",$ipv4);
	$fullipv4="";
	foreach($ipposition as $bip){
		$fullipv4.=getfullip(decbin($bip));
	}
	return $fullipv4;
}

//自动补全二进制IP位址为八位
function getfullip($str){
	if(strlen($str)<8){
		$str="0".$str;
		return getfullip($str);
	}else{
		return $str;
	}
}

//当禁止访问的提示
function forbidv(){
	wp_die("You have been banned for accessing the site, Please try again later!","Prohibit Access",array('response'=>403));
	return false;
}
//禁止留言的提示
function forbidc($commentdata){
	wp_die("You have been banned for commenting, Please try again later!","Prohibit Access",array('response'=>403));
    return $commentdata;
}


//系统初始化时，获取用户的真实IP
add_action("init","_get_real_ip");

if(is_admin()){
	//当进入后台时，进入后台管理功能页面
	require("get-real-ip-admin.php");
}else{
	//当用户不是在后台时，根据需要执行匹配
	$acl_ctrl_mode=get_option("acl_ctrl_mode");
	if($acl_ctrl_mode=="fvisit"&&is_forbid()){
		add_action("init","forbidv");
	}else if($acl_ctrl_mode=="fcomment"&&is_forbid()){
		add_action("preprocess_comment","forbidc");
	}
}
//注册安装插件运行
register_activation_hook(__FILE__,'_real_ip_install');
//停用插件运行
register_deactivation_hook(__FILE__,'_real_ip_uninstall');

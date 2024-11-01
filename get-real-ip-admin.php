<?php
//系统设置更新功能
if(isset($_POST["acl_ctrl_mode"])){
	update_option("acl_ctrl_mode",trim($_POST["acl_ctrl_mode"]));
}else if(isset($_POST["acl_ctrl_addr"])){
	update_option("acl_ctrl_addr",trim($_POST["acl_ctrl_addr"]));
}

//更新语言
load_plugin_textdomain('wp-real-ip-based-access-control', false, dirname(plugin_basename(__FILE__)) . '/languages');

//展示后台页面
function display_acl_menu(){
	add_options_page('WP Real IP-based Access Control', 'WP Real IP-based ACL', 'manage_options','get-real-ip-admin.php', 'show_manage_page');
}

//设置页面
function show_manage_page(){
	$acl_ctrl_mode=get_option("acl_ctrl_mode");
?>
<div class="wrap">
	<div style="background:#ffc;border:1px solid #333;margin:2px;margin-top:50px;padding:5px;float:right;width:260px">
	<h3 style="text-align:center"><?php _e('Make The WordPress More Safer','wp-real-ip-based-access-control');?></h3>
	<?php _e('<p>WP Real IP-based Access Control is developing and maintaining by <a href="http://www.hitoy.org/">Hito</a>. Its main function is  control the visit and comment privileges based on the user\'s real IP(If the visitor use a proxy, or your site use a CDN service, the visitor\'s IP address is not their real IP address). With this plug, it can not only reduce waste flow, but also can directly discard the spam rather than stored the spam in database. </p>
	<p>Have any suggestions, please contact vip@hitoy.org.</p>
	<h3 style="text-align:center">Rating for This Plugin</h3>
	<p>Please <a href="http://wordpress.org/plugins/wp-real-ip-based-access-control/">Rating for this plugin</a> and tell me your needs. This is very useful for my development.</p>
	<p><a href="https://me.alipay.com/hitoy" target="_blank">Donate to this plugin »</a></p>','wp-real-ip-based-access-control');?>
	</div>
<h2>WP Real IP-based Access Control <?php _e('Settings','wp-real-ip-based-access-control')?></h2>
<hr/>
<h3><?php _e('Access Control Function','wp-real-ip-based-access-control');?></h3>
	<form action="" method="POST">
	<input type="radio" name="acl_ctrl_mode" value="none" <?php echo $acl_ctrl_mode=="none"?"checked=\"checked\"":"";?>/><?php _e('Off','wp-real-ip-based-access-control')?>
&nbsp;&nbsp;
<input type="radio" name="acl_ctrl_mode" value="fvisit" <?php echo $acl_ctrl_mode=="fvisit"?"checked=\"checked\"":"";?>/><?php _e('Block Access','wp-real-ip-based-access-control')?>
&nbsp;&nbsp;
<input type="radio" name="acl_ctrl_mode" value="fcomment" <?php	echo $acl_ctrl_mode=="fcomment"?"checked=\"checked\"":"";?>/><?php _e('Prohibit Comments','wp-real-ip-based-access-control')?><br/><br/>
	<input class="button-primary" type="submit" value="<?php _e('Update','wp-real-ip-based-access-control')?> »">
	</form>
	<br/>
	<h3><?php _e('Access Control List Management','wp-real-ip-based-access-control')?></h3>
	<p><?php _e('When the above function is turned on, and the visitors IP address are in the following list, the system will be prevents visitors from accessing or comment  according to your settings.','wp-real-ip-based-access-control')?></p>
<p><?php _e('Supports Two Formats:  Single IP Address or IP address/Mask<br/>For example: 127.0.0.1 or 192.168.0.0/24.','wp-real-ip-based-access-control')?></p>
<p><?php _e('Make sure that one single IP in a Single line.','wp-real-ip-based-access-control')?></p>
	<form action="" method="POST">
	<textarea name="acl_ctrl_addr" style="width:460px;max-width:460px;height:320px;"><?php echo get_option('acl_ctrl_addr');?></textarea><br/><br/>
	<input class="button-primary" type="submit" value="<?php _e('Update','wp-real-ip-based-access-control')?> »">
	</form>
</div>
<?php
}
add_action('admin_menu', 'display_acl_menu');

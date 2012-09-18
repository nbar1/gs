<?php
session_start();
require('db.php');

if($_POST['set'])
{
	// Check to see if nickname exists, if TRUE take over
	$sql = "SELECT * FROM thegogre_grooveshark.users WHERE user_nickname='".mysql_real_escape_string($_POST['nickname'])."'";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) < 1)
	{
		// Continue with creating user
		$sql = "INSERT INTO thegogre_grooveshark.users (user_nickname, user_created, user_lastlogin) VALUES ('".mysql_real_escape_string($_POST['nickname'])."', '".date('Y-m-d H:i:s')."', '".date('Y-m-d H:i:s')."')";
		$result = mysql_query($sql);
		if($result)
		{
			$_SESSION['nickname'] = mysql_real_escape_string($_POST['nickname']);
			$_SESSION['active'] = TRUE;
			setcookie('user', mysql_insert_id(), strtotime("+5 years"));
		} else {
			// TODO Error creating user
		}
	} else {
		$row = mysql_fetch_assoc($result);
		$_SESSION['nickname'] = $row['user_nickname'];
		$_SESSION['active'] = $row['user_active'];
		setcookie('user', $row['user_id'], strtotime("+5 years"));
	}
header('location: queue.php');
	exit();
}

if($_COOKIE['user'])
{
	$sql = "SELECT * FROM thegogre_grooveshark.users WHERE user_id='".mysql_real_escape_string($_COOKIE['user'])."' LIMIT 1";
	$result = mysql_query($sql);
	if(mysql_num_rows($result) > 0)
	{
		$row = mysql_fetch_assoc($result);
		$_SESSION['nickname'] = $row['user_nickname'];
		$_SESSION['active'] = $row['user_active'];
		// Update user login time
		$sql = "UPDATE thegogre_grooveshark.users SET user_lastlogin='".date("Y-m-d H:i:s")."' WHERE user_id='".mysql_real_escape_string($_COOKIE['user'])."'";
		$result = mysql_query($sql);
		header('location: queue.php');
		exit();
	}
}
?>
<div class="setUser_header">enter your name</div>
<input type="text" class="setUser_textbox" id="setUser_textbox" placeholder="name" maxlength="32"/>
<div id="setUser_submit" onclick=''>submit</div>

<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
include('../../config.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'paypaldigit':
		$r = isset($_POST['r'])?strip_tags($_POST['r']):0;
		$ip = isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:(isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:0);
		$d = '../../data/_sdata-'.$sdata.'/_paypal/tmp/';
		@file_put_contents($d.'digit'.$r.'.json', $ip);
		$h = opendir($d);
		while(($f=readdir($h))!==false)
			{
			if(is_file($d.$f) && substr(0,5,$f)=='digit' && filemtime($d.$f)<time()-86400) unlink($d.$f); // 24h
			}
		closedir($h);
		break;
		// ********************************************************************************************
		}
	exit;
	}
?>

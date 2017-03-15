<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
?>
<?php
include(dirname(__FILE__).'/../../config.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'paypaldigit':
		$r = (isset($_POST['r'])?preg_replace('/[^0-9]/','',$_POST['r']):0);
		$ip = (isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:(isset($_SERVER['REMOTE_HOST'])?$_SERVER['REMOTE_HOST']:0));
		$d = dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/';
		@file_put_contents($d.'digit'.$r.'.txt', $ip);
		$h = @opendir($d);
		if($h)
			{
			while(($f=readdir($h))!==false)
				{
				if($f=='.' || $f=='..') continue;
				if(is_file($d.$f) && (substr($f,0,5)=='digit' || substr($f,0,9)=='acces-ipn') && filemtime($d.$f)<time()-86400) @unlink($d.$f); // 24h
				}
			@closedir($h);
			}
		break;
		// ********************************************************************************************
		}
	exit;
	}
?>

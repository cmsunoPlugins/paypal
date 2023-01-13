<?php
if(!isset($_SESSION['cmsuno'])) exit();
// Activates external method PAYPAL in Payment Plugin if not
if(file_exists('data/payment.json')) {
	$q = file_get_contents('data/payment.json'); $b = json_decode($q,true);
	if(empty($b['method'])) $b['method'] = array('paypal'=>1);
	else if(empty($b['method']['paypal'])) $b['method']['paypal'] = 1;
	else $b = 0;
	if($b) file_put_contents('data/payment.json',json_encode($b));
}
?>

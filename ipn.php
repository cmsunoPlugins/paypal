 <?php 
// http://www.johnboy.com/blog/http-11-paypal-ipn-example-php-code
// http://www.lafermeduweb.net/billet/tutorial-integrer-paypal-a-son-site-web-en-php-partie-2-276.html#ipn
// https://developer.paypal.com/docs/classic/ipn/ht_ipn/
// ex : 
// mc_gross=19.95&protection_eligibility=Eligible&address_status=confirmed&payer_id=LPLWNMTBWMFAY&tax=0.00&address_street=1+Main+St&payment_date=20%3A12%3A59+Jan+13%2C+2009+PST&payment_status=Completed&charset=windows-1252&address_zip=95131&first_name=Test&mc_fee=0.88&address_country_code=US&address_name=Test+User&notify_version=2.6&custom=&payer_status=verified&address_country=United+States&address_city=San+Jose&quantity=1&verify_sign=AtkOfCXbDm2hu0ZELryHFjY-Vb7PAUvS6nMXgysbElEn9v-1XcmSoGtf&payer_email=gpmac_1231902590_per%40paypal.com&txn_id=61E67681CH3238416&payment_type=instant&last_name=User&address_state=CA&receiver_email=gpmac_1231902686_biz%40paypal.com&payment_fee=0.88&receiver_id=S8XGHLYDW9T3S&txn_type=express_checkout&item_name=&mc_currency=USD&item_number=&residence_country=US&test_ipn=1&handling_amount=0.00&transaction_subject=&payment_gross=19.95&shipping=0.00
//
include(dirname(__FILE__).'/../../config.php');
$q = file_get_contents(dirname(__FILE__).'/../../data/paypal.json');
$a = json_decode($q,true);
if($a && isset($_POST['txn_id']))
	{
	$hostPaypal = (($a['mod']=='test')?'www.sandbox.paypal.com':'www.paypal.com'); // test / prod
	$urlPaypal = (($a['mod']=='test')?'https://www.sandbox.paypal.com':'https://www.paypal.com'); // test / prod
	$req = 'cmd=_notify-validate'; // read the post from PayPal system and add 'cmd'
	$kv = array("time" => time(), "treated" => 0, "mode" => $a['mod']);
	$charset = ((isset($_POST['charset'])&&$_POST['charset']!='utf-8')?$_POST['charset']:0);
	foreach($_POST as $k=>$v)
		{
		$kv[$k] = ($charset?mb_convert_encoding($v,'utf-8',$charset):$v);
		$v = urlencode(stripslashes($v));
		$req .= "&$k=$v";
		}
	//
	// ipn handshake
	//
	$res = 0; $door = ((isset($a['ssl'])&&$a['ssl'])?1:0);
	// Solution 1 : CURL
	if(!$door && function_exists('curl_version'))
		{
		$kv['IpnMethod'] = 'CURL controled';
		$ch = curl_init($urlPaypal.'/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
		$res = curl_exec($ch);
		curl_close($ch);
		}
	// Solution 2 : OPENSSL
	else if(!$door && function_exists('openssl_open'))
		{
		$kv['IpnMethod'] = 'FSOCKOPEN controled';
		$fp = fsockopen('ssl://'.$hostPaypal,443,$errno,$errstr,30);
		if($fp!==false)
			{
			$header = "POST /cgi-bin/webscr HTTP/1.1\r\n";
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($req) . "\r\n";
			$header .= "Host: ".$hostPaypal."\r\n"; // sans http - sans /cgi-bin/webscr
			$header .= "Connection: close\r\n\r\n";
			$written = fwrite($fp,$header.$req);
			if($written===false)
				{
				file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorNotWritten'.$_POST['txn_id'].'.json', json_encode($kv));
				exit;
				}
			$res = stream_get_contents($fp);
			fclose($fp);
			}
		}
	if($res) // sol 1 & 2
		{
		$res = trim($res);
		$kv['IpnResponse'] = $res;
		}
	// Solution 3 : no handshake (not safe !)
	if($res==0)
		{
		clearstatcache();
		$door = 1;
		$kv['IpnMethod'] = 'not controled';
		// 1. check server data
		$h = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:0;
		if(strpos($h,'PayPal IPN')===false && strpos(stripslashes($h),'paypal.com/ipn')===false || !$h) $door = 0;
		// 2. check file created when clic on Paypal button : paypalCall.php - only digital - <30 min
		if(isset($_POST['custom']) && substr($_POST['custom'],0,8)=='DIGITAL|')
			{
			$dtmp = dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/';
			$r = explode("|", $_POST['custom']); // [3] : key
			if(isset($r[3]) && file_exists($dtmp.'digit'.$r[3].'.txt') && filemtime($dtmp.'digit'.$r[3].'.txt')>time()-1800) $kv['Ip_buyer'] = file_get_contents($dtmp.'digit'.$r[3].'.txt');
			else $door = 0;
			}
		$kv['Server'] = str_replace(',',', ',json_encode($_SERVER));
		}
	//
	// Control & Actions
	//	
	$ipn = json_encode($kv);
	if($door || strcmp($res,"VERIFIED")==0)
		{
		if($_POST['payment_status']=="Completed")
			{
			// vérifier que txn_id n'a pas été précédemment traité
			if(VerifIXNID($_POST['txn_id'],$sdata)==0)
				{ // vérifier que receiver_email est votre adresse email PayPal principale
				if($a['mail']==$_POST['receiver_email'] || $a['mail']==$_POST['receiver_id'])
					{ // OK
					include(dirname(__FILE__).'/lang/lang.php');
					if(file_exists(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'))
						{
						$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true);
						$mailAdmin = $b['mel'];
						}
					else $mailAdmin = false;
					include dirname(__FILE__).'/../../template/mailTemplate.php';
					$bottom = str_replace('[[unsubscribe]]','&nbsp;',$bottom);
					$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/ssite.json'); $b = json_decode($q,true);
					// DIGITAL ?
					if(substr($_POST['custom'],0,8)=='DIGITAL|')
						{
						$q = file_get_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/markdown.json'); $b1 = json_decode($q,true);
						$d = explode("|", $_POST['custom']); // 1/ Ubusy ; 2/ shortcode (name) : 3/ key
						$Ubusy = $d[1];
						$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b2 = json_decode($q,true);
						if($_POST['mc_gross']<$b1[$Ubusy]['md'][$d[2]]['p'])
							{ // Price Lower than it must be
							file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/badPrice'.$_POST['txn_id'].'.json', $ipn);
							}
						else
							{
							// copy & rename file
							$fi = dirname(__FILE__).'/../../../files/';
							if(!is_dir($fi.'upload/')) mkdir($fi.'upload/');
							if(!file_exists($fi.'upload/index.html')) file_put_contents($fi.'upload/index.html', '<html></html>');
							if(file_exists($fi.$d[2].'/'.$b1[$Ubusy]['md'][$d[2]]['k'].$d[2].'.zip')) copy($fi.$d[2].'/'.$b1[$Ubusy]['md'][$d[2]]['k'].$d[2].'.zip',$fi.'upload/'.$d[3].$d[2].'.zip');
							$zip = new ZipArchive;
							if($zip->open($fi.'upload/'.$d[3].$d[2].'.zip')===true)
								{
								$zip->addFromString($d[2].'/key.php', '<?php $key = "'.$d[3].'"; ?>');
								$zip->close();
								}
							if(!is_dir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/'))
								{
								mkdir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/');
								file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/index.html', '<html></html>');
								}
							file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_digital/'.$d[3].$d[2].'.json', '{"t":"'.time().'","p":"paypal","d":"'.$d[2].'","k":"'.$d[3].'"}');
							// link to zip in mail
							$msg = $d[2].'.zip :<br />'."\r\n".'<a href="'.$b2['url'].'/files/upload/'.$d[3].$d[2].'.zip">'.$b2['url'].'/files/upload/'.$d[3].$d[2].'.zip</a>'."\r\n<br /><br />\r\n"._('Thank you for your trust, see you soon!')."\r\n";
							// MAIL USER LINK TO ZIP
							mailUser($_POST['payer_email'], 'Download - '.$d[2], $msg, $bottom, $top);
							}
						}
					// ORDER ?
					if(isset($_POST['item_name1']) && isset($_POST['custom']))
						{
						if($n=strpos($_POST['custom'],'ADRESS|')!==false)
							{
							$v = explode("|", substr($_POST['custom'],$n));
							$name =  str_replace("\\","",$v[1]); $adre = str_replace("\\","",$v[2]); $mail = $v[3]; $Ubusy = $v[4];
							$q = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/site.json'); $b2 = json_decode($q,true);
							$msgOrder = '<p style="text-align:right;">'.date("d/m/Y H:i").'</p><p>'; $b3 = 0; $p = 0; $name = ''; $n = 1;
							while(isset($_POST['item_name'.$n]))
								{
								if(!$b3) $b3=1;
								$msgOrder .= (isset($_POST['quantity'.$n])?$_POST['quantity'.$n]:1).' x '.$_POST['item_name'.$n].' ('.(isset($_POST['mc_gross_'.$n])?$_POST['mc_gross_'.$n]:$_POST['mc_gross'.$n]).$_POST['mc_currency'].') = '.((isset($_POST['quantity'.$n])?$_POST['quantity'.$n]:1) * (isset($_POST['mc_gross_'.$n])?$_POST['mc_gross_'.$n]:$_POST['mc_gross'.$n])).$_POST['mc_currency'].'<br />';
								$p += ((isset($_POST['quantity'.$n])?$_POST['quantity'.$n]:1) * (isset($_POST['mc_gross_'.$n])?$_POST['mc_gross_'.$n]:$_POST['mc_gross'.$n]));
								++$n;
								}
							if($mail && $Ubusy)
								{
								$msgOrder .= '</p><p>'._('Total').' : <strong>'.$p.' &euro;</strong></p>';
								$msgOrder = str_replace(".",",",$msgOrder);
								$msgOrder .= '<p>'._('Paid by Paypal').'.</p><hr /><p>'._('Name').' : '.$name.'<br />'._('Address').' : '.$adre.'<br />'._('Mail').' : '.$mail.'</p>';
								if($b3)
									{
									// MAIL ADMIN ORDER
									mailAdmin(_('New order by Paypal'). ' - '.$_POST['txn_id'], $msgOrder, $bottom, $top, $b2['url']);
									// MAIL USER ORDER
									$iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND);
									$r = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, 'payment', $_POST['txn_id'].'|'.$mail, MCRYPT_MODE_ECB, $iv));
									$info = "<a href='".stripslashes($b2['url']).'/uno/plugins/payment/paymentOrder.php?a=look&b='.urlencode($r)."&t=paypal'>"._("Follow the evolution of your order")."</a>";
									$msgOrderU = $msgOrder.'<br /><p>'._('Thank you for your trust.').'</p><p>'.$info.'</p>';
									mailUser($mail, $b2['tit'].' - '._('Order'), $msgOrderU, $bottom, $top, $b2['url'].'/'.$Ubusy.'.html');
									}
								}
							// ADD MEMO TAX
							$q1 = file_get_contents(dirname(__FILE__).'/../../data/'.$Ubusy.'/payment.json'); $a1 = json_decode($q1,true);
							$kv['Utax'] = $a1['taa'].'|'.$a1['tab'].'|'.$a1['tac'].'|'.$a1['tad'];
							$kv['Ubusy'] = $Ubusy;
							$ipn = json_encode($kv);
							}
						}
					if($mailAdmin)
						{
						$msg = "<table>";
						foreach($kv as $k=>$v) $msg .= "<tr><td>".$k."&nbsp:&nbsp</td><td>".$v."</td></tr>\r\n";
						$msg .= "</table>\r\n";
						// MAIL ADMIN PAYMENT
						mailAdmin('Paypal - '._('Payment receipt').' : '.$_POST['mc_gross'].$_POST['mc_currency'], $msg, $bottom, $top, $b2['url']);
						}
					file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['txn_id'].'.json', $ipn); // OK
					}
				else
					{ // Mauvaise adresse email paypal
					file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorMailPaypal'.$_POST['txn_id'].'.json', $ipn);
					}
				}
			else
				{ // ID de transaction déjà utilisé
				file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorRepetition'.$_POST['txn_id'].'.json', $ipn);
				}
			}
		else
			{ // Statut de paiement: Echec
			file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorNotCompleted'.$_POST['txn_id'].'.json', $ipn);
			}
		}
	else if(strcmp($res,"INVALID")==0)
		{
		file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorINVALID'.$_POST['txn_id'].'.json', $ipn);
		}
	else file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorResponse'.$_POST['txn_id'].'.json', $ipn);
	}
else if(isset($_POST['txn_id'])) file_put_contents(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/tmp/errorTopIpn'.$_POST['txn_id'].'.json', '');
//
function VerifIXNID($txn_id,$sdata)
	{ // fonction pour verifier si la depense est deja effectue (1) ou pas (0)
	$a=array();
	if ($h=opendir(dirname(__FILE__).'/../../data/_sdata-'.$sdata.'/_paypal/'))
		{
		while (($file=readdir($h))!==false) { if($file==$txn_id.'.json') {closedir($h); return 1;}}
		closedir($h);
		}
	return 0;
	}
//
function mailAdmin($tit, $msg, $bottom, $top, $url)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	$body = '<b><a href="'.$url.'/uno.php" style="color:#000000;">'.$tit.'</a></b><br />'.$rn.$msg.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">MIME-Version: 1.0".$rn."Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn."Content-Type: text/plain; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgT.$rn;
	$msg.=$rn."--".$boundary.$rn."Content-Type: text/html; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgH.$rn.$rn."--".$boundary."--".$rn.$rn."--".$boundary."--".$rn;
	$subject = mb_encode_mimeheader(stripslashes($tit),"UTF-8");
	if(mail($mailAdmin, $subject, stripslashes($msg), $header)) return true;
	else return false;
	}
//
function mailUser($dest, $tit, $msg, $bottom, $top, $url=false)
	{
	global $mailAdmin;
	$rn = "\r\n";
	$boundary = "-----=".md5(rand());
	if($url) $body = '<b><a href="'.$url.'.html" style="color:#000000;">'.$tit.'</a></b><br />'.$rn.$msg.$rn;
	else $body = "<b>".$tit."</b><br />".$rn.$msg.$rn;
	$msgT = strip_tags($body);
	$msgH = $top . $body . $bottom;
	$header  = "From: ".$mailAdmin."<".$mailAdmin.">".$rn."Reply-To:".$mailAdmin."<".$mailAdmin.">MIME-Version: 1.0".$rn."Content-Type: multipart/alternative;".$rn." boundary=\"$boundary\"".$rn;
	$msg= $rn."--".$boundary.$rn."Content-Type: text/plain; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgT.$rn;
	$msg.=$rn."--".$boundary.$rn."Content-Type: text/html; charset=\"utf-8\"".$rn."Content-Transfer-Encoding: 8bit".$rn.$rn.$msgH.$rn.$rn."--".$boundary."--".$rn.$rn."--".$boundary."--".$rn;
	$subject = mb_encode_mimeheader(stripslashes($tit),"UTF-8");
	if(mail($dest, $subject, stripslashes($msg), $header)) return true;
	else return false;
	}
//
//$er = error_get_last();
//file_put_contents(dirname(__FILE__).'/../../data/errorIPNPaypal'.time().'.txt', $er);
?>

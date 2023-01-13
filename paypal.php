<?php
session_start(); 
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
include('lang/lang.php');
$busy = (isset($_POST['ubusy'])?preg_replace("/[^A-Za-z0-9-_]/",'',$_POST['ubusy']):'index');
if(!is_dir('../../data/_sdata-'.$sdata.'/_paypal/')) mkdir('../../data/_sdata-'.$sdata.'/_paypal/',0711);
if(!is_dir('../../data/_sdata-'.$sdata.'/_paypal/tmp/')) mkdir('../../data/_sdata-'.$sdata.'/_paypal/tmp/');
// ********************* actions *************************************************************************
if(isset($_POST['action'])) {
	switch ($_POST['action']) {
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/paypal/paypal.css" />
		<div class="blocForm">
			<div id="paypalA" class="bouton fr" onClick="f_paypalArchiv();" title="<?php echo T_("Archives");?>"><?php echo T_("Archives");?></div>
			<div id="paypalC" class="bouton fr" onClick="f_paypalConfig();" title="<?php echo T_("Configure Paypal plugin");?>"><?php echo T_("Config");?></div>
			<div id="paypalV" class="bouton fr current" onClick="f_paypalVente();" title="<?php echo T_("Sales list");?>"><?php echo T_("Sales");?></div>
			<div id="paypalD" class="bouton fr current" title="<?php echo T_("Payment Details");?>" style="display:none;"><?php echo T_("Payment Details");?></div>
			<h2><?php echo T_("Paypal");?></h2>
			<div id="paypalConfig" style="display:none;">
				<img style="float:right;margin:10px;" src="uno/plugins/paypal/img/paypalLogo.png" />
				<p><?php echo T_("This plugin allows you to add the Paypal payment gateway to the Payment plugin.");?></p>
				<p><?php echo T_("Create your account on");?>&nbsp;<a href='https://www.paypal.com/'>Paypal</a>.</p>
				<h3><?php echo T_("Default Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("Email or Paypal ID");?></label></td>
						<td><input type="text" class="input" name="payMail" id="payMail" style="width:150px;" /></td>
						<td><em><?php echo T_("Email address or Paypal ID for the Paypal account. Select the ID helps prevent SPAM.");?></em></td>
					</tr>
					<tr>
						<td>
							<label><?php echo T_("No IPN handshake");?></label>
							<?php echo (function_exists('curl_version'))?'<div class="curl">'.T_("Curl available").'</div>':'<div class="nocurl">'.T_("Curl not available").'</div>'; ?>
						</td>
						<td><input type="checkbox" name="paySSL" id="paySSL" /></td>
						<td><em><?php echo T_("If your host is not SHA2 or if Curl is not available, you should check this unsafe option.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Currency");?></label></td>
						<td>
							<select name="payCurr" id="payCurr">
								<option value="EUR"><?php echo T_("Euro");?></option>
								<option value="USD"><?php echo T_("US Dollar");?></option>
								<option value="CAD"><?php echo T_("Canadian Dollar");?></option>
								<option value="GBP"><?php echo T_("Pound Sterling");?></option>
								<option value="CHF"><?php echo T_("Swiss Franc");?></option>
								<option value="DKK"><?php echo T_("Danish Krone");?></option>
								<option value="NOK"><?php echo T_("Norwegian Krone");?></option>
								<option value="SEK"><?php echo T_("Swedish Krona");?></option>
								<option value="PLN"><?php echo T_("Polish Zloty");?></option>
								<option value="RUB"><?php echo T_("Russian Ruble");?></option>
							</select>
						</td>
						<td><em><?php echo T_("What is the currency of payment.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Notify URL");?></label></td>
						<td><?php echo substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';?></td>
						<td><em><?php echo T_("Local File for Paypal Instant Payment Notification (IPN)"); ?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo T_("Publish Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo T_("Mode");?></label></td>
						<td>
							<select name="payMod" id="payMod">
								<option value="prod"><?php echo T_("Production");?></option>
								<option value="test"><?php echo T_("Test (sandbox)");?></option>
							</select>
						</td>
						<td><em><?php echo T_("When publishing : Production = Real payment ; Test = Dummy payment to test account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo T_("Lang");?></label></td>
						<td>
							<select name="paylang" id="payLang">
								<option value=""><?php echo T_("Current language").' ('.$lang.')';?></option>
								<option value="en"><?php echo T_("English");?></option>
								<option value="fr"><?php echo T_("French");?></option>
								<option value="es"><?php echo T_("Spanish");?></option>
							</select>
						</td>
						<td><em><?php echo T_("Select the language to use in the form.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr" onClick="f_save_paypal();" title="<?php echo T_("Save settings");?>"><?php echo T_("Save");?></div>
				<div class="clear"></div>
			</div>
			<div id="paypalDetail" style="display:none;"></div>
			<div id="paypalArchiv" style="display:none;"></div>
			<div id="paypalVente"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$a = Array();
		if(file_exists('../../data/paypal.json')) {
			$q = file_get_contents('../../data/paypal.json');
			if($q) $a = json_decode($q,true);
		}
		if(file_exists('../../data/payment.json')) { // idem paypalMake2.php
			$q = file_get_contents('../../data/payment.json'); $b = json_decode($q,true);
			if(empty($b['method'])) $b['method'] = array('paypal'=>1);
			else if(empty($b['method']['paypal'])) $b['method']['paypal'] = 1;
			else $b = 0;
			if($b) file_put_contents('../../data/payment.json',json_encode($b));
		}
		$a['mail'] = strip_tags($_POST['mail']);
		$a['ssl'] = (!empty($_POST['ssl'])?1:0);
		$a['curr'] = $_POST['curr'];
		$a['mod'] = strip_tags($_POST['mod']);
		$a['url'] = substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';
		$a['home'] = substr($_SERVER['HTTP_REFERER'],0,-7).$busy.'.html';
		$a['lng'] = strip_tags($_POST['lng']);
		$out = json_encode($a);
		if(file_put_contents('../../data/paypal.json', $out)) echo T_('Backup performed');
		else echo '!'.T_('Impossible backup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'.T_("List of the Paypal payments").' :</h3>';
		echo '<style>
				#paypalVente table tr{border-bottom:1px solid #888;}
				#paypalVente table th{text-align:center;padding:5px 2px;font-weight:700;}
				#paypalVente table td{text-align:left;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
				#paypalVente table tr.PayTreatedYes td{color:green;}
				#paypalVente table td.yesno{text-decoration:underline;cursor:pointer;}
				#paypalVente .paypalArchiv{width:16px;height:16px;margin:0 auto;background-position:-112px -96px;cursor:pointer;background-image:url("'.$_POST['udep'].'includes/img/ui-icons_444444_256x240.png")}
			</style>';
		$tab = array(); $d = '../../data/_sdata-'.$sdata.'/_paypal/';
		if($dh=opendir($d)) {
			while(($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
			closedir($dh);
		}
		if(count($tab)) {
			echo '<br /><table>';
			echo '<tr><th>'.T_("Date").'</th><th>'.T_("Type").'</th><th>'.T_("Name").'</th><th>'.T_("Address").'</th><th>'.T_("Article").'</th><th>'.T_("Price").'</th><th>'.T_("Treated").'</th><th>('.T_("Del").')</th><th>'.T_("Archive").'</th></tr>';
			$b = array();
			foreach($tab as $r) {
				$q = @file_get_contents($r);
				$a = json_decode($q,true);
				$b[] = $a;
			}
			function sortTime($u1,$u2) {return (isset($u2['time'])?$u2['time']:0) - (isset($u1['time'])?$u1['time']:0);}
			usort($b, 'sortTime');
			foreach($b as $r) {
				if($r) {
					$typ = '';
					if(isset($r['custom']) && strpos($r['custom'],'DIGITAL|')!==false) $typ = '<br />(Digital)';
					$item = ((isset($r['item_number'])&&$r['item_number'])?$r['item_number'].' : ':'').((isset($r['item_name']) && isset($r['quantity']))?$r['item_name'].(($r['quantity']!="0")?' ('.$r['quantity'].')':''):'');
					if(!$item) {
						$v = 1;
						while(isset($r['item_name'.$v])) {
							$item .= ($item?'<br />':'').((isset($r['item_number'.$v])&&$r['item_number'.$v])?$r['item_number'.$v].' : ':'').$r['item_name'.$v].' ('.$r['quantity'.$v].')';
							++$v;
						}
					}
					echo '<tr'.($r['treated']?' class="PayTreatedYes"':'').'>';
					echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paypalDetail(\''.$r['txn_id'].'\')">'.$r['txn_id'].'</span></td>';
					echo '<td style="text-align:center">'.(isset($r['subscr_id'])?'Sub':((isset($r['quantity'])&&$r['quantity']=="0")?'Don':'Pay')).$typ.'<br />'.((isset($r['IpnMethod'])&&$r['IpnMethod']=='not controled')?'<span style="font-size:.9em;color:#300;">'.T_("Unsafe").'</span>':'').'</td>';
					echo '<td>'.$r['first_name'].'&nbsp;'.$r['last_name'].'<br />'.$r['payer_email'].'</td>';
					echo '<td>'.$r['address_street'].'<br />'.$r['address_zip'].' - '.$r['address_city'].'<br />'.$r['address_state'].' - '.$r['address_country'].'</td>';
					echo '<td>'.$item.'</td>';
					echo '<td>'.$r['mc_gross'].' '.$r['mc_currency'].'</td>';
					echo '<td '.(!$r['treated']?'onClick="f_treated_paypal(this,\''.$r['txn_id'].'\',\''.T_("Yes").'\')"':'').($r['treated']?'>'.T_("Yes"):' class="yesno">'.T_("No")).'</td>';
					if(isset($r['test_ipn']) && $r['test_ipn']=='1' && isset($r['txn_id'])) echo '<td width="30px" style="cursor:pointer;background:transparent url(\''.$_POST['udep'].'includes/img/close.png\') no-repeat scroll center center;" onClick="f_supp_paypal(this,\''.$r['txn_id'].'\')">&nbsp;</td>';
					else echo '<td></td>';
					echo '<td><div class="paypalArchiv" onClick="f_archivOrderPaypal(\''.$r['txn_id'].'\',\''.T_("Are you sure ?").'\')"></div></td>';
					echo '</tr>';
				}
			}
			echo '</table>';
		}
		break;
		// ********************************************************************************************
		case 'treated':
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['id']).'.json')) {
			$q = file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['id']).'.json');
			if($q) {
				$a = json_decode($q,true);
				$a['treated'] = 1;
				$out = json_encode($a);
				if(file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['id']).'.json', $out)) echo T_('Treated');
				exit;
			}
		}
		echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		$d = $_POST['f'];
		$a = explode('__',$d);
		if(count($a)>2) $d1 = $a[0].'.json';
		else $d1 = $d;
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$d) && rename('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$d, '../../data/_sdata-'.$sdata.'/_paypal/'.$d1)) echo T_('Restored');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		$p = '../../data/_sdata-'.$sdata.'/_paypal/archive';
		if(!is_dir($p)) mkdir($p,0711);
		$d = strip_tags($_POST['id']).'.json';
		$q = file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$d);
		if($q) $a = json_decode($q,true);
		else $a = array();
		if(!empty($a['time']) && !empty($a['mc_gross'])) {
			$d1 = substr($d,0,-5).'__'.$a['time'].'__'.str_replace('.','',$a['mc_gross']).'__.json';
		}
		else $d1 = $d;
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.$d) && rename('../../data/_sdata-'.$sdata.'/_paypal/'.$d, $p.'/'.$d1)) echo T_('Archived');
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		$p = '../../data/_sdata-'.$sdata.'/_paypal/archive';
		if(is_dir($p) && $h=opendir($p)) {
			$b = array();
			while(($d=readdir($h))!==false) {
				$ext = explode('.',$d);
				$ext = $ext[count($ext)-1];
				if($d!='.' && $d!='..' && $ext=='json') {
					if(strpos($d,'__')!==false) {
						$a = explode('__',$d);
						if(count($a)>2) $b[] = array('txn_id'=>$a[0], 'time'=>$a[1], 'mc_gross'=>$a[2], 'file'=>$d);
					}
					else {
						$q = file_get_contents($p.'/'.$d);
						if($q) $a = json_decode($q,true);
						else $a = array();
						if(!empty($a['time']) && !empty($a['mc_gross'])) {
							$d1 = substr($d,0,-5).'__'.$a['time'].'__'.str_replace('.','',$a['mc_gross']).'__.json';
							rename($p.'/'.$d, $p.'/'.$d1);
						}
					}
				}
			}
			closedir($h);
			usort($b, function($f,$g) { return $g['time'] - $f['time'];});
			$o = '<div id="paypalArchData"></div><div>';
			foreach($b as $r) {
				$o .= '<div class="paypalListArchiv" onClick="f_paypalViewA(\''.$r['file'].'\');">'.$r['txn_id'].' - '.date('dMy',$r['time']).' - '.substr($r['mc_gross'],0,-2).'&euro;</div>';
			}
			echo $o.'</div><div style="clear:left;"></div>';
		}
		break;
		// ********************************************************************************************
		case 'viewA':
		if(isset($_POST['arch']) && file_exists('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['arch'])) {
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/archive/'.strip_tags($_POST['arch']));
			$a = json_decode($q,true);
			$o = '<h3>'.T_('Archives').'</h3><table class="paypalTO">';
			foreach($a as $k=>$v) {
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
			}
			echo $o.'</table><div class="bouton fr" onClick="f_paypalRestaurOrder(\''.strip_tags($_POST['arch']).'\');" title="'.T_("Restore").'">'.T_("Restore").'</div><div style="clear:both;"></div>';
		}
		break;
		// ********************************************************************************************
		case 'detail':
		if(isset($_POST['id']) && file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['id']).'.json')) {
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['id']).'.json');
			$a = json_decode($q,true);
			$o = '<h3>'.T_('Payment Details').'</h3><table class="paypalTO">';
			foreach($a as $k=>$v) {
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
			}
			$o .= '</table>';
			$o .= '<div class="bouton fr" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').' onClick="f_archivOrderPaypal(\''.$_POST['id'].'\',\''.T_("Are you sure ?").'\')" title="">'.T_("Archive").'</div>';
			$o .= '<div style="clear:both;"></div>';
			echo $o;
		}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
		case 'suppsandbox':
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['file']).'.json')) {
			unlink('../../data/_sdata-'.$sdata.'/_paypal/'.strip_tags($_POST['file']).'.json');
			echo T_('Removed');
		}
		else echo '!'.T_('Error');
		break;
		// ********************************************************************************************
	}
	clearstatcache();
	exit;
}
?>

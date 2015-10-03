<?php
session_start(); 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])!='xmlhttprequest') {sleep(2);exit;} // ajax request
if(!isset($_POST['unox']) || $_POST['unox']!=$_SESSION['unox']) {sleep(2);exit;} // appel depuis uno.php
?>
<?php
include('../../config.php');
if (!is_dir('../../data/_sdata-'.$sdata.'/_paypal/')) mkdir('../../data/_sdata-'.$sdata.'/_paypal/',0711);
if (!is_dir('../../data/_sdata-'.$sdata.'/_paypal/tmp/')) mkdir('../../data/_sdata-'.$sdata.'/_paypal/tmp/');
include('lang/lang.php');
// ********************* actions *************************************************************************
if (isset($_POST['action']))
	{
	switch ($_POST['action'])
		{
		// ********************************************************************************************
		case 'plugin': ?>
		<link rel="stylesheet" type="text/css" media="screen" href="uno/plugins/paypal/paypal.css" />
		<div class="blocForm">
			<div id="paypalA" class="bouton fr" onClick="f_paypalArchiv();" title="<?php echo _("Archives");?>"><?php echo _("Archives");?></div>
			<div id="paypalC" class="bouton fr" onClick="f_paypalConfig();" title="<?php echo _("Configure Paypal plugin");?>"><?php echo _("Config");?></div>
			<div id="paypalV" class="bouton fr current" onClick="f_paypalVente();" title="<?php echo _("Sales list");?>"><?php echo _("Sales");?></div>
			<div id="paypalD" class="bouton fr current" title="<?php echo _("Payment Details");?>" style="display:none;"><?php echo _("Payment Details");?></div>
			<h2><?php echo _("Paypal");?></h2>
			<div id="paypalConfig" style="display:none;">
				<img style="float:right;margin:10px;" src="uno/plugins/paypal/images/paypalLogo.png" />
				<p><?php echo _("This plugin allows you to add different Paypal buttons in your website.");?></p>
				<p><?php echo _("It is used with the button") .'<img src="uno/plugins/paypal/ckpaypal/icons/ckpaypal.png" style="border:1px solid #aaa;padding:3px;margin:0 6px -5px;border-radius:2px;" />' . _("added to the text editor when the plugin is enabled.");?></p>
				<p><?php echo _("Create your account on");?>&nbsp;<a href='https://www.paypal.com/'>Paypal</a>.</p>
				<h3><?php echo _("Default Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Email or Paypal ID");?></label></td>
						<td><input type="text" class="input" name="payMail" id="payMail" style="width:150px;" /></td>
						<td><em><?php echo _("Email address or Paypal ID for the Paypal account. Select the ID helps prevent SPAM.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Currency");?></label></td>
						<td>
							<select name="payCurr" id="payCurr">
								<option value="EUR"><?php echo _("Euro");?></option>
								<option value="USD"><?php echo _("US Dollar");?></option>
								<option value="CAD"><?php echo _("Canadian Dollar");?></option>
								<option value="GBP"><?php echo _("Pound Sterling");?></option>
								<option value="CHF"><?php echo _("Swiss Franc");?></option>
								<option value="DKK"><?php echo _("Danish Krone");?></option>
								<option value="NOK"><?php echo _("Norwegian Krone");?></option>
								<option value="SEK"><?php echo _("Swedish Krona");?></option>
								<option value="PLN"><?php echo _("Polish Zloty");?></option>
								<option value="RUB"><?php echo _("Russian Ruble");?></option>
							</select>
						</td>
						<td><em><?php echo _("What is the currency of payment.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Tax rate (%)");?></label></td>
						<td><input type="text" class="input" name="payTax" id="payTax" style="width:100px;" /></td>
						<td><em><?php echo _("Tax rate in your country for the payment (%). Will be added to prices.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Appearance");?></label></td>
						<td>
							<select name="payApp" id="payApp" onChange="f_btn_paypal();">
								<option value="CC_LG"><?php echo _("Standard with flags");?></option>
								<option value="_LG"><?php echo _("Standard");?></option>
								<option value="_SM"><?php echo _("Small");?></option>
							</select>
						</td>
						<td>
							<img id="payCC_LG" src="uno/plugins/paypal/images/btnCC_LG.gif" alt="<?php echo _("Standard with flags");?>" />
							<img id="pay_LG" style="display:none;" src="uno/plugins/paypal/images/btn_LG.gif" alt="<?php echo _("Standard with flags");?>" />
							<img id="pay_SM" style="display:none;" src="uno/plugins/paypal/images/btn_SM.gif" alt="<?php echo _("Standard with flags");?>" />
						</td>
					</tr>
					<tr>
						<td><label><?php echo _("Selling");?></label></td>
						<td>
							<select name="payAct" id="payAct">
								<option value="products"><?php echo _("products");?></option>
								<option value="services"><?php echo _("services");?></option>
							</select>
						</td>
						<td><em><?php echo _("Define what are you selling (only buy button).");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Donation value");?></label></td>
						<td>
							<select name="payDon" id="payDon">
								<option value="1"><?php echo _("fixed");?></option>
								<option value="0"><?php echo _("free");?></option>
							</select>
						</td>
						<td><em><?php echo _("Define a value for donation or let the client choose (only donate button).");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Notify URL");?></label></td>
						<td style="vertical-align:middle;padding:10px;"><?php echo substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';?></td>
						<td><em><?php echo _("Local File for Paypal Instant Payment Notification (IPN)"); ?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo _("Options :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("External use");?></label></td>
						<td><input type="checkbox" name="payExt" id="payExt" /></td>
						<td><em><?php echo _("Use Paypal from another plugin : complete system with cart or digital product.");?></em></td>
					</tr>
				</table>
				<br />
				<h3><?php echo _("Publish Settings :");?></h3>
				<table class="hForm">
					<tr>
						<td><label><?php echo _("Mode");?></label></td>
						<td>
							<select name="payMod" id="payMod">
								<option value="prod"><?php echo _("Production");?></option>
								<option value="test"><?php echo _("Test (sandbox)");?></option>
							</select>
						</td>
						<td><em><?php echo _("When publishing : Production = Real payment ; Test = Dummy payment to test account.");?></em></td>
					</tr>
					<tr>
						<td><label><?php echo _("Popup");?></label></td>
						<td>
							<select name="payPop" id="payPop">
								<option value="0"><?php echo _("No");?></option>
								<option value="1"><?php echo _("Yes");?></option>
							</select>
						</td>
						<td><em><?php echo _("Open Paypal in a new small windows. Allow visitor to keep an eye on your website.");?></em></td>
					</tr>
				</table>
				<div class="bouton fr" onClick="f_save_paypal();" title="<?php echo _("Save settings");?>"><?php echo _("Save");?></div>
				<div class="clear"></div>
			</div>
			<div id="paypalDetail" style="display:none;"></div>
			<div id="paypalArchiv" style="display:none;"></div>
			<div id="paypalVente"></div>
		</div>
		<?php break;
		// ********************************************************************************************
		case 'save':
		$q = file_get_contents('../../data/busy.json'); $a = json_decode($q,true); $home = $a['nom'];
		$q = @file_get_contents('../../data/paypal.json');
		if($q) $a = json_decode($q,true);
		else $a = Array();
		$a['mail'] = $_POST['mail'];
		$a['curr'] = $_POST['curr'];
		$a['tax'] = ($_POST['tax']?$_POST['tax']:0);
		$a['app'] = $_POST['app'];
		$a['mod'] = $_POST['mod'];
		$a['pop'] = $_POST['pop'];
		$a['act'] = $_POST['act'];
		$a['don'] = ($_POST['don']?$_POST['don']:0);
		$a['url'] = substr($_SERVER['HTTP_REFERER'],0,-4).'/plugins/paypal/ipn.php';
		$a['home'] = substr($_SERVER['HTTP_REFERER'],0,-7).($home?$home:'index').'.html';
		$a['lang5'] = (isset($langPlug[$lang])?substr($langPlug[$lang],0,5):'en_US');
		$a['ext'] = ($_POST['ext']?1:0);
		$out = json_encode($a);
		if (file_put_contents('../../data/paypal.json', $out)) echo _('Backup performed');
		else echo '!'._('Impossible backup');
		break;
		// ********************************************************************************************
		case 'vente':
		echo '<h3>'._("List of the Paypal payments").' :</h3>';
		echo '<style>
				#paypalVente table tr{border-bottom:1px solid #888;}
				#paypalVente table th{text-align:center;padding:5px 2px;font-weight:700;}
				#paypalVente table td{text-align:left;padding:2px 6px;vertical-align:middle;color:#0b4a6a;}
				#paypalVente table tr.PayTreatedYes td{color:green;}
				#paypalVente table td.yesno{text-decoration:underline;cursor:pointer;}
			</style>';
		$tab=''; $d='../../data/_sdata-'.$sdata.'/_paypal/';
		if ($dh=opendir($d))
			{
			while (($file = readdir($dh))!==false) { if ($file!='.' && $file!='..') $tab[]=$d.$file; }
			closedir($dh);
			}
		if(count($tab))
			{
			echo '<br /><table>';
			echo '<tr><th>'._("Date").'</th><th>'._("Type").'</th><th>'._("Name").'</th><th>'._("Address").'</th><th>'._("Article").'</th><th>'._("Price").'</th><th>'._("Treated").'</th></tr>';
			$b = array();
			foreach($tab as $r)
				{
				$q=@file_get_contents($r);
				$a=json_decode($q,true);
				$b[]=$a;
				}
			function sortTime($u1,$u2) {return (isset($u2['time'])?$u2['time']:0) - (isset($u1['time'])?$u1['time']:0);}
			usort($b, 'sortTime');
			foreach($b as $r)
				{
				if($r)
					{
					$typ = '';
					if(isset($r['custom']) && strpos($r['custom'],'DIGITAL|')!==false) $typ = '<br />(Digital)';
					$item=((isset($r['item_number'])&&$r['item_number'])?$r['item_number'].' : ':'').((isset($r['item_name']) && isset($r['quantity']))?$r['item_name'].(($r['quantity']!="0")?' ('.$r['quantity'].')':''):'');
					if(!$item)
						{
						$v=1;
						while(isset($r['item_name'.$v]))
							{
							$item.=($item?'<br />':'').((isset($r['item_number'.$v])&&$r['item_number'.$v])?$r['item_number'.$v].' : ':'').$r['item_name'.$v].' ('.$r['quantity'.$v].')';
							++$v;
							}
						}
					echo '<tr'.($r['treated']?' class="PayTreatedYes"':'').'>';
					echo '<td>'.(isset($r['time'])?date("dMy H:i", $r['time']):'').'<br /><span style="font-size:.8em;text-decoration:underline;cursor:pointer;" onClick="f_paypalDetail(\''.$r['txn_id'].'\')">'.$r['txn_id'].'</span></td>';
					echo '<td style="text-align:center">'.(isset($r['subscr_id'])?'Sub':((isset($r['quantity'])&&$r['quantity']=="0")?'Don':'Pay')).$typ.'</td>';
					echo '<td>'.$r['first_name'].'&nbsp;'.$r['last_name'].'<br />'.$r['payer_email'].'</td>';
					echo '<td>'.$r['address_street'].'<br />'.$r['address_zip'].' - '.$r['address_city'].'<br />'.$r['address_state'].' - '.$r['address_country'].'</td>';
					echo '<td>'.$item.'</td>';
					echo '<td>'.$r['mc_gross'].' '.$r['mc_currency'].'</td>';
					echo '<td '.(!$r['treated']?'onClick="f_treated_paypal(this,\''.$r['txn_id'].'\',\''._("No").'\')"':'').($r['treated']?'>'._("Yes"):' class="yesno">'._("No")).'</td>';
					echo '</tr>';
					}
				}
			echo '</table>';
			}
		break;
		// ********************************************************************************************
		case 'treated':
		$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json');
		if($q)
			{
			$a = json_decode($q,true);
			$a['treated'] = 1;
			}
		$out = json_encode($a);
		if (file_put_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json', $out)) echo _('Treated');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'restaur':
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['f']) && rename('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['f'],'../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['f'])) echo _('Restored');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'archiv':
		if(!is_dir('../../data/_sdata-'.$sdata.'/_paypal/archive')) mkdir('../../data/_sdata-'.$sdata.'/_paypal/archive',0711);
		if(file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json') && rename('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json','../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['id'].'.json')) echo _('Archived');
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		case 'viewArchiv':
		if (is_dir('../../data/_sdata-'.$sdata.'/_paypal/archive') && $h=opendir('../../data/_sdata-'.$sdata.'/_paypal/archive'))
			{
			$o = '<div id="paypalArchData"></div><div>';
			while(($d=readdir($h))!==false)
				{
				$ext=explode('.',$d); $ext=$ext[count($ext)-1];
				if($d!='.' && $d!='..' && $ext=='json')
					{
					$o .= '<div class="paypalListArchiv" onClick="f_paypalViewA(\''.$d.'\');">'.$d.'</div>';
					}
				}
			closedir($h);
			echo $o.'</div><div style="clear:left;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'viewA':
		if(isset($_POST['arch']) && file_exists('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['arch']))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/archive/'.$_POST['arch']);
			$a = json_decode($q,true); $o = '<h3>'._('Archives').'</h3><table class="paypalTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			echo $o.'</table><div class="bouton fr" onClick="f_paypalRestaurOrder(\''.$_POST['arch'].'\');" title="'._("Restore").'">'._("Restore").'</div><div style="clear:both;"></div>';
			}
		break;
		// ********************************************************************************************
		case 'detail':
		if(isset($_POST['id']) && file_exists('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json'))
			{
			$q = @file_get_contents('../../data/_sdata-'.$sdata.'/_paypal/'.$_POST['id'].'.json');
			$a = json_decode($q,true); $o = '<h3>'._('Payment Details').'</h3><table class="paypalTO">';
			foreach($a as $k=>$v)
				{
				if($k=='time') $v .= ' => '.date("d/m/Y H:i",$v);
				$o .= '<tr><td>'.$k.'</td><td>'.(is_array($v)?json_encode($v):$v).'</td></tr>';
				}
			$o .= '</table>';
			$o .= '<div class="bouton fr" '.((isset($a['treated']) && $a['treated']==0)?'style="display:none;"':'').' onClick="f_archivOrderPaypal(\''.$_POST['id'].'\',\''._("Are you sure ?").'\')" title="">'._("Archive").'</div>';
			$o .= '<div style="clear:both;"></div>';
			echo $o;
			}
		else echo '!'._('Error');
		break;
		// ********************************************************************************************
		}
	clearstatcache();
	exit;
	}
?>

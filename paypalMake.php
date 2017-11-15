<?php
if (!isset($_SESSION['cmsuno'])) exit();
?>
<?php
if(file_exists('data/paypal.json'))
	{
	include('plugins/paypal/lang/lang.php');
	$q1 = file_get_contents('data/paypal.json');
	$a1 = json_decode($q1,true);
	$prod = 'https://www.paypal.com/';
	$sand = 'https://www.sandbox.paypal.com/';
	if($a1['mod']=='test') $Ucontent = str_replace($prod,$sand,$Ucontent); // mode SANDBOX
	else if($a1['mod']=='prod') $Ucontent = str_replace($sand,$prod,$Ucontent); // mode PRODUCTION
	if($a1['pop']=='1') $Ucontent = str_replace("appendChild(fm);fm.submit();","appendChild(fm);window.open('','paypop','width=960,height=600,resizeable,scrollbars');fm.target='paypop';fm.submit();",$Ucontent); // Paypal dans un popup
	else $Ucontent = str_replace("appendChild(fm);window.open('','paypop','width=960,height=600,resizeable,scrollbars');fm.target='paypop';fm.submit();","appendChild(fm);fm.submit();",$Ucontent);
	if($a1['ext'] && (strpos($Ucontent,'paypalCart(')!==false || strpos($Uhtml,'paypalCart(')!==false || strpos($Ufoot,'paypalCart(')!==false)) // paymentMake executed before paypalMake
	if(!empty($a1['act']))
		{
		// JSON : {"prod":{"0":{"n":"clef de 12","p":8.5,"i":"","q":1,"t":1},"1":{"n":"tournevis","p":1.5,"i":"","q":2,"t":1},"2":{"n":"papier craft","p":0.21,"i":"","q":30,"t":1}},"digital":"Ubusy|readme","ship":"4","name":"Sting","adre":"rue du lac 33234 PLOUG","mail":"bob@example.com"}
		// n=nom, p=prix, i=ID, q=quantite, t=taxe id (1+2+4+8)
		$tmp = "<script type=\"text/javascript\">";
		$tmp .= "function paypalCart(f){var d=0,r=0,dg=0;f=JSON.parse(f);if(f['prod']){";
			$tmp .= "Object.size=function(o){var s=0,k;for(k in o){if(o.hasOwnProperty(k))s++;}return s;};";
			$tmp .= "if(f['digital']){d=f['digital'];r=Math.random().toString().substr(2);}";
			$tmp .= "fm=document.createElement('form');fm.action='".(($a1['mod']=='test')?'https://www.sandbox.paypal.com/cgi-bin/webscr':'https://www.paypal.com/cgi-bin/webscr')."';fm.method='post';fm.target='_top';";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='business';ip.value='".$a1['mail']."';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='notify_url';ip.value='".$a1['url']."';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='return';if(d!=0)ip.value='".$a1['home']."?digit='+d+'|'+r+'&paypal=ok';else ip.value='".$a1['home']."?paypal=ok';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='cmd';ip.value='_cart';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='upload';ip.value='1';fm.appendChild(ip);";
			$tmp .= "for(v=0;v<Object.size(f['prod']);v++){";
				$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='item_name_'+(v+1);ip.value=f['prod'][v]['n'];fm.appendChild(ip);";
				$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='amount_'+(v+1);ip.value=f['prod'][v]['p'];fm.appendChild(ip);";
				$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='item_number_'+(v+1);if(d!=0)ip.value='';else ip.value=f['prod'][v]['i']+'|'+f['prod'][v]['t'];fm.appendChild(ip);";
				$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='quantity_'+(v+1);ip.value=f['prod'][v]['q'];fm.appendChild(ip);";
			$tmp .= "};if(f.hasOwnProperty('ship')&&f['ship']!=0){";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='item_name_'+(v+1);ip.value='".T_('Shipping cost')."';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='amount_'+(v+1);ip.value=f['ship'];fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='quantity_'+(v+1);ip.value='1';fm.appendChild(ip);}";
			$tmp .= "else{ip=document.createElement('input');ip.type='hidden';ip.name='shipping';ip.value='".$a1['tax']."';fm.appendChild(ip);}";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='currency_code';ip.value='".$a1['curr']."';fm.appendChild(ip);";
			$tmp .= "if(d!=0){ip=document.createElement('input');ip.type='hidden';ip.name='custom';ip.value='DIGITAL|'+d+'|'+r;fm.appendChild(ip);";
			$tmp .= "var x=new XMLHttpRequest(),p='action=paypaldigit&r='+r;x.open('POST','uno/plugins/paypal/paypalCall.php',true);x.setRequestHeader('Content-type','application/x-www-form-urlencoded');x.setRequestHeader('X-Requested-With','XMLHttpRequest');x.setRequestHeader('Content-length',p.length);x.setRequestHeader('Connection','close');x.send(p);"; // d : shortcode
			$tmp .= "}else if(f.hasOwnProperty('name')&&f.hasOwnProperty('adre')){ip=document.createElement('input');ip.type='hidden';ip.name='custom';ip.value='ADRESS|'+f['name']+'|'+f['adre']+'|'+f['mail']+'|'+f['Ubusy'];fm.appendChild(ip);}"; // d : shortcode
			$tmp .= "else{ip=document.createElement('input');ip.type='hidden';ip.name='lc';ip.value='".strtoupper($lang)."';fm.appendChild(ip);";
			$tmp .= "ip=document.createElement('input');ip.type='hidden';ip.name='no_note';ip.value='1';fm.appendChild(ip);}";
			$tmp .= "document.body.appendChild(fm);fm.submit();";
		$tmp .= "}};</script>"."\r\n";
		$Ufoot .= $tmp;
		$Uonload .= "if('ok'==unoGvu('paypal')){unoPop('".T_('Thank you for your payment')."',5000);document.cookie='cart=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';}";
		$unoPop = 1; // include unoPop.js in output
		}
	}
?>

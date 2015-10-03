//
// CMSUno
// Plugin Paypal
//
function f_save_paypal(){
	jQuery(document).ready(function(){
		var mail=document.getElementById("payMail").value;
		var curr=document.getElementById("payCurr").options[document.getElementById("payCurr").selectedIndex].value;
		var tax=document.getElementById("payTax").value;
		var app=document.getElementById("payApp").options[document.getElementById("payApp").selectedIndex].value;
		var mod=document.getElementById("payMod").options[document.getElementById("payMod").selectedIndex].value;
		var pop=document.getElementById("payPop").options[document.getElementById("payPop").selectedIndex].value;
		var act=document.getElementById("payAct").options[document.getElementById("payAct").selectedIndex].value;
		var don=document.getElementById("payDon").options[document.getElementById("payDon").selectedIndex].value;
		var ext=(document.getElementById('payExt').checked?1:0);
		jQuery.post('uno/plugins/paypal/paypal.php',{'action':'save','unox':Unox,'mail':mail,'curr':curr,'tax':tax,'app':app,'mod':mod,'pop':pop,'act':act,'don':don,'ext':ext},function(r){
			f_alert(r);
		});
	});
}
function f_load_paypal(){
	jQuery(document).ready(function(){
		jQuery.getJSON("uno/data/paypal.json?r="+Math.random(),function(r){
			if(r.mail!=undefined)document.getElementById('payMail').value=r.mail;
			if(r.curr){
				t=document.getElementById("payCurr");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.curr){to[v].selected=true;v=to.length;}}
			}
			if(r.tax!=undefined)document.getElementById('payTax').value=r.tax;
			if(r.app){
				t=document.getElementById("payApp");
				to=t.options;
				for(v=0;v<to.length;v++){
					if(to[v].value==r.app){
						to[v].selected=true;
						v=to.length;
						f_btn_paypal(r.app);
					}
				}
			}
			if(r.mod){
				t=document.getElementById("payMod");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.mod){to[v].selected=true;v=to.length;}}
			}
			if(r.pop){
				t=document.getElementById("payPop");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.pop){to[v].selected=true;v=to.length;}}
			}
			if(r.act){
				t=document.getElementById("payAct");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.act){to[v].selected=true;v=to.length;}}
			}
			if(r.don!=undefined){
				t=document.getElementById("payDon");
				to=t.options;
				for(v=0;v<to.length;v++){if(to[v].value==r.don){to[v].selected=true;v=to.length;}}
			}
			if(r.ext!=undefined&&r.ext)document.getElementById('payExt').checked=true;
		});
	});
}
function f_btn_paypal(b){
	var a=document.getElementById('payApp');
	if(!b)var b=a.options[a.selectedIndex].value;
	if(b=='CC_LG'){
		document.getElementById('payCC_LG').style.display="inline";
		document.getElementById('pay_LG').style.display="none";
		document.getElementById('pay_SM').style.display="none";
	}
	else if(b=='_LG'){
		document.getElementById('payCC_LG').style.display="none";
		document.getElementById('pay_LG').style.display="inline";
		document.getElementById('pay_SM').style.display="none";
	}
	else if(b=='_SM'){
		document.getElementById('payCC_LG').style.display="none";
		document.getElementById('pay_LG').style.display="none";
		document.getElementById('pay_SM').style.display="inline";
	}
}
function f_treated_paypal(f,g,h){
	jQuery.post('uno/plugins/paypal/paypal.php',{'action':'treated','unox':Unox,'id':g},function(r){f_alert(r);});
	f.parentNode.className="PayTreatedYes";
	f.innerHTML=h;f.className="";f.onclick="";
}
function f_archivOrderPaypal(f,g){if(confirm(g)){jQuery.post('uno/plugins/paypal/paypal.php',{'action':'archiv','unox':Unox,'id':f},function(r){f_alert(r);if(r.substr(0,1)!='!')f_paypalVente();});}}
function f_paypalRestaurOrder(f){jQuery.post('uno/plugins/paypal/paypal.php',{'action':'restaur','unox':Unox,'f':f},function(r){f_alert(r);f_paypalArchiv();});}
function f_paypalViewA(f){
	jQuery('#paypalArchData').empty();
	jQuery.post('uno/plugins/paypal/paypal.php',{'action':'viewA','unox':Unox,'arch':f},function(r){jQuery('#paypalArchData').append(r);jQuery('#paypalArchData').show();});
}
function f_paypalArchiv(){
	jQuery('#paypalArchiv').empty();
	document.getElementById('paypalArchiv').style.display="block";
	document.getElementById('paypalConfig').style.display="none";
	document.getElementById('paypalVente').style.display="none";
	document.getElementById('paypalDetail').style.display="none";
	document.getElementById('paypalA').className="bouton fr current";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr";
	document.getElementById('paypalD').style.display="none";
	jQuery.post('uno/plugins/paypal/paypal.php',{'action':'viewArchiv','unox':Unox},function(r){jQuery('#paypalArchiv').append(r);jQuery('#paypalArchData').hide();});
}
function f_paypalConfig(){
	document.getElementById('paypalArchiv').style.display="none";
	document.getElementById('paypalConfig').style.display="block";
	document.getElementById('paypalVente').style.display="none";
	document.getElementById('paypalDetail').style.display="none";
	document.getElementById('paypalA').className="bouton fr";
	document.getElementById('paypalC').className="bouton fr current";
	document.getElementById('paypalV').className="bouton fr";
	document.getElementById('paypalD').style.display="none";
}
function f_paypalVente(){
	document.getElementById('paypalArchiv').style.display="none";
	document.getElementById('paypalConfig').style.display="none";
	jQuery('#paypalVente').empty();document.getElementById('paypalVente').style.display="block";
	document.getElementById('paypalDetail').style.display="none";
	document.getElementById('paypalA').className="bouton fr";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr current";
	document.getElementById('paypalD').style.display="none";
	jQuery.post('uno/plugins/paypal/paypal.php',{'action':'vente','unox':Unox},function(r){jQuery('#paypalVente').append(r);});
}
function f_paypalDetail(f){
	jQuery('#paypalDetail').empty();
	document.getElementById('paypalArchiv').style.display="none";
	document.getElementById('paypalConfig').style.display="none";
	document.getElementById('paypalVente').style.display="none";
	document.getElementById('paypalDetail').style.display="block";
	document.getElementById('paypalA').className="bouton fr";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr";
	document.getElementById('paypalD').style.display="block";
	jQuery.post('uno/plugins/paypal/paypal.php',{'action':'detail','unox':Unox,'id':f},function(r){
		if(r.substr(0,1)!='!')jQuery('#paypalDetail').append(r);
		else f_alert(r);
	});
}
//
f_load_paypal();f_paypalVente();
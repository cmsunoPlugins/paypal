//
// CMSUno
// Plugin Paypal
//
function f_save_paypal(){
	var mail=document.getElementById("payMail").value;
	var ssl=(document.getElementById('paySSL').checked?1:0);
	var curr=document.getElementById("payCurr").options[document.getElementById("payCurr").selectedIndex].value;
	var mod=document.getElementById("payMod").options[document.getElementById("payMod").selectedIndex].value;
	var lng=document.getElementById("payLang").options[document.getElementById("payLang").selectedIndex].value;
	let x=new FormData();
	x.set('action','save');
	x.set('unox',Unox);
	x.set('ubusy',Ubusy);
	x.set('mail',mail);
	x.set('ssl',ssl);
	x.set('curr',curr);
	x.set('mod',mod);
	x.set('lng',lng);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
function f_load_paypal(){
	let x=new FormData();
	x.set('action','load');
	x.set('unox',Unox);
	fetch('uno/data/paypal.json?r='+Math.random(),{method:'post',body:x})
	.then(r=>r.json())
	.then(function(r){
		if(r.mail!=undefined)document.getElementById('payMail').value=r.mail;
		if(r.ssl!=undefined&&r.ssl)document.getElementById('paySSL').checked=true;
		if(r.curr!=undefined&&r.curr)document.getElementById('payCurr').value=r.curr;
		if(r.mod!=undefined&&r.mod)document.getElementById('payMod').value=r.mod;
		if(r.lng!=undefined&&r.lng)document.getElementById('payLang').value=r.lng;
	});
}
function f_treated_paypal(f,g,h){
	let x=new FormData();
	x.set('action','treated');
	x.set('unox',Unox);
	x.set('id',g);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f.parentNode.className="PayTreatedYes";
		f.innerHTML=h;f.className="";f.onclick="";
	});
}
function f_archivOrderPaypal(f,g){
	if(confirm(g)){
		let x=new FormData();
		x.set('action','archiv');
		x.set('unox',Unox);
		x.set('id',f);
		fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
		.then(r=>r.text())
		.then(function(r){
			f_alert(r);
			if(r.substr(0,1)!='!')f_paypalVente();
		});
	}
}
function f_paypalRestaurOrder(f){
	let x=new FormData();
	x.set('action','restaur');
	x.set('unox',Unox);
	x.set('f',f);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		f_alert(r);
		f_paypalArchiv();
	});
}
function f_paypalViewA(f){
	document.getElementById('paypalArchData').innerHTML='';
	let x=new FormData();
	x.set('action','viewA');
	x.set('unox',Unox);
	x.set('arch',f);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('paypalArchData').insertAdjacentHTML('beforeend',r);
		document.getElementById('paypalArchData').style.display="block";
	});
}
function f_paypalArchiv(){
	document.getElementById('paypalArchiv').innerHTML='';
	document.getElementById('paypalArchiv').style.display="block";
	document.getElementById('paypalConfig').style.display="none";
	document.getElementById('paypalVente').style.display="none";
	document.getElementById('paypalDetail').style.display="none";
	document.getElementById('paypalA').className="bouton fr current";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr";
	document.getElementById('paypalD').style.display="none";
	let x=new FormData();
	x.set('action','viewArchiv');
	x.set('unox',Unox);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('paypalArchiv').insertAdjacentHTML('beforeend',r);
		document.getElementById('paypalArchData').style.display="none";
	});
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
	document.getElementById('paypalVente').innerHTML='';
	document.getElementById('paypalVente').style.display="block";
	document.getElementById('paypalDetail').style.display="none";
	document.getElementById('paypalA').className="bouton fr";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr current";
	document.getElementById('paypalD').style.display="none";
	let x=new FormData();
	x.set('action','vente');
	x.set('unox',Unox);
	x.set('udep',Udep);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		document.getElementById('paypalVente').insertAdjacentHTML('beforeend',r);
	});
}
function f_paypalDetail(f){
	document.getElementById('paypalDetail').innerHTML='';
	document.getElementById('paypalArchiv').style.display="none";
	document.getElementById('paypalConfig').style.display="none";
	document.getElementById('paypalVente').style.display="none";
	document.getElementById('paypalDetail').style.display="block";
	document.getElementById('paypalA').className="bouton fr";
	document.getElementById('paypalC').className="bouton fr";
	document.getElementById('paypalV').className="bouton fr";
	document.getElementById('paypalD').style.display="block";
	let x=new FormData();
	x.set('action','detail');
	x.set('unox',Unox);
	x.set('id',f);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(function(r){
		if(r.substr(0,1)!='!')document.getElementById('paypalDetail').insertAdjacentHTML('beforeend',r);
		else f_alert(r);
	});
}
function f_supp_paypal(f,g){
	f.parentNode.parentNode.removeChild(f.parentNode);
	let x=new FormData();
	x.set('action','suppsandbox');
	x.set('unox',Unox);
	x.set('file',g);
	fetch('uno/plugins/paypal/paypal.php',{method:'post',body:x})
	.then(r=>r.text())
	.then(r=>f_alert(r));
}
//
f_load_paypal();f_paypalVente();

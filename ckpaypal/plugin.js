/**
 * Plugin CKPaypal
 * Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
var ckpayMem=0;

//	var s=document.getElementsByTagName('script'),u="",el=document.createElement('script');
//	el.async=false;el.type='text/javascript';
//	for(v=0;v<s.length;v++){if(s[v].src.match('ckpaypal/plugin.js')) u=s[v].src.substr(0,s[v].src.search('ckpaypal/plugin.js'));}
//	if(u!=""){
//		el.src=u+'ckpaypal/ckpaypalConfig.js';
//		(document.getElementsByTagName('HEAD')[0]||document.body).appendChild(el);
//	}
	
CKEDITOR.plugins.add('ckpaypal',{
	icons:'ckpaypal',
	lang: 'en,fr',
	init:function(editor){
		ckpayMem=0;
		var lang=editor.lang.ckpaypal;
		editor.addCommand('ckpaypalDialog',new CKEDITOR.dialogCommand('ckpaypalDialog'));
		editor.ui.addButton('ckpaypal',{
			label:lang.title,
			command:'ckpaypalDialog',
			toolbar:'cmsuno'
		});
		editor.on('doubleclick',function(evt){
			var el=evt.data.element;
			if(!el.isReadOnly()&&el.is('input')&&el.getAttribute('class')=='ckpaypal'){
				ckpayMem=el.getAttribute('alt');
				ckpayMem=((ckpayMem)?ckpayMem.split('|'):['','','','','','','']);
				evt.data.dialog='ckpaypalDialog';
			}
		});
		CKEDITOR.dialog.add('ckpaypalDialog',this.path+'dialogs/ckpaypal.js');
	}
});

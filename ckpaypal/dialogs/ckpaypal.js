/**
 * Plugin CKPaypal
 * Copyright (c) <2014> <Jacques Malgrange contacter@boiteasite.fr>
 * License MIT
 */
CKEDITOR.dialog.add('ckpaypalDialog',function(editor){
	var lang=editor.lang.ckpaypal;
	var payData={};
	var payBuy={'ckpayName':1,'ckpayIdnum':1,'ckpayAct':1,'ckpayShipping':1,'ckpayTax':1,'ckpayDonval':0,'ckpayPrice':1,'ckpayEvery1':0,'ckpayEveryA':0};
	var payCart={'ckpayName':1,'ckpayIdnum':1,'ckpayAct':0,'ckpayShipping':1,'ckpayTax':1,'ckpayDonval':0,'ckpayPrice':1,'ckpayEvery1':0,'ckpayEveryA':0};
	var payView={'ckpayName':0,'ckpayIdnum':0,'ckpayAct':0,'ckpayShipping':0,'ckpayTax':0,'ckpayDonval':0,'ckpayPrice':0,'ckpayEvery1':0,'ckpayEveryA':0};
	var payDonate={'ckpayName':1,'ckpayIdnum':1,'ckpayAct':0,'ckpayShipping':0,'ckpayTax':0,'ckpayDonval':1,'ckpayPrice':0,'ckpayEvery1':0,'ckpayEveryA':0};
	var paySubscribe={'ckpayName':1,'ckpayIdnum':1,'ckpayAct':0,'ckpayShipping':0,'ckpayTax':0,'ckpayDonval':0,'ckpayPrice':1,'ckpayEvery1':1,'ckpayEveryA':1};
	var payButton=function(payValue){
		var payJs='ip=document.createElement(\'input\');ip.type=\'hidden\';ip.name=\'';
		var payAll='fm=document.createElement(\'form\');fm.action=\'https://www.'+(paypalSand?'sandbox.':'')+'paypal.com/cgi-bin/webscr\';fm.method=\'post\';fm.target=\'_top\';'+
			payJs+'business\';ip.value=\''+payValue.email+'\';fm.appendChild(ip);'+
			payJs+'notify_url\';ip.value=\''+paypalUrl+'\';fm.appendChild(ip);'+
			payJs+'no_note\';ip.value=\'1\';fm.appendChild(ip);'+
			payJs+'return\';ip.value=\''+paypalHome+'\';fm.appendChild(ip);'+
			payJs+'lc\';ip.value=\''+paypalLang5.substr(3,2)+'\';fm.appendChild(ip);';
		var jsFrmBuy=payAll+payJs+'cmd\';ip.value=\'_xclick\';fm.appendChild(ip);'+
			payJs+'item_name\';ip.value=\''+payValue.name+'\';fm.appendChild(ip);'+
			payJs+'quantity\';ip.value=\'1\';fm.appendChild(ip);'+
			payJs+'amount\';ip.value=\''+(paypalTaxIncl?(Math.round(parseInt(payValue.price)/(100+parseInt(payValue.tax))*10000)/100):payValue.price)+'\';fm.appendChild(ip);'+
			payJs+'button_subtype\';ip.value=\''+payValue.act+'\';fm.appendChild(ip);';
			if(payValue.idnum)jsFrmBuy+=payJs+'item_number\';ip.value=\''+payValue.idnum+'\';fm.appendChild(ip);';
			if(payValue.shipping)jsFrmBuy+=payJs+'shipping\';ip.value=\''+payValue.shipping+'\';fm.appendChild(ip);';
			if(payValue.tax)jsFrmBuy+=payJs+'tax_rate\';ip.value=\''+payValue.tax+'\';fm.appendChild(ip);';
			jsFrmBuy+=payJs+'currency_code\';ip.value=\''+payValue.currency+'\';fm.appendChild(ip);'+
			payJs+'bn\';ip.value=\'PP-BuyNowBF:btn_buynow'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif:NonHostedGuest\';fm.appendChild(ip);'+
			'document.body.appendChild(fm);fm.submit();';
		var jsFrmCart=payAll+payJs+'cmd\';ip.value=\'_cart\';fm.appendChild(ip);'+
			payJs+'item_name\';ip.value=\''+payValue.name+'\';fm.appendChild(ip);'+
			payJs+'add\';ip.value=\'1\';fm.appendChild(ip);'+
			payJs+'amount\';ip.value=\''+(paypalTaxIncl?(Math.round(parseInt(payValue.price)/(100+parseInt(payValue.tax))*10000)/100):payValue.price)+'\';fm.appendChild(ip);';
			if(payValue.idnum)jsFrmCart+=payJs+'item_number\';ip.value=\''+payValue.idnum+'\';fm.appendChild(ip);';
			if(payValue.shipping)jsFrmCart+=payJs+'shipping\';ip.value=\''+payValue.shipping+'\';fm.appendChild(ip);';
			if(payValue.tax)jsFrmCart+=payJs+'tax_rate\';ip.value=\''+payValue.tax+'\';fm.appendChild(ip);';
			jsFrmCart+=payJs+'currency_code\';ip.value=\''+payValue.currency+'\';fm.appendChild(ip);'+
			payJs+'bn\';ip.value=\'PP-ShopCartBF:btn_cart'+((payValue.app==lang.small)?'_SM':'_LG')+'.gif:NonHostedGuest\';fm.appendChild(ip);'+
			'document.body.appendChild(fm);fm.submit();';
		var jsFrmView=payAll+payJs+'cmd\';ip.value=\'_cart\';fm.appendChild(ip);'+
			payJs+'display\';ip.value=\'1\';fm.appendChild(ip);'+
			payJs+'bn\';ip.value=\'PP-ShopCartBF:btn_viewcart'+((payValue.app==lang.small)?'_SM':'_LG')+'.gif:NonHostedGuest\';fm.appendChild(ip);'+
			'document.body.appendChild(fm);fm.submit();';
		var jsFrmDon=payAll+payJs+'cmd\';ip.value=\'_donations\';fm.appendChild(ip);';
			if(payValue.donval==lang.donfix)jsFrmDon+=payJs+'amount\';ip.value=\''+payValue.price+'\';fm.appendChild(ip);';
			jsFrmDon+=payJs+'currency_code\';ip.value=\''+payValue.currency+'\';fm.appendChild(ip);'+
			payJs+'bn\';ip.value=\'PP-DonationsBF:btn_donate'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif:NonHostedGuest\';fm.appendChild(ip);'+
			'document.body.appendChild(fm);fm.submit();';
		var jsFrmSub=payAll+payJs+'cmd\';ip.value=\'_xclick-subscriptions\';fm.appendChild(ip);'+
			payJs+'item_name\';ip.value=\''+payValue.name+'\';fm.appendChild(ip);';
			if(payValue.idnum)jsFrmCart+=payJs+'item_number\';ip.value=\''+payValue.idnum+'\';fm.appendChild(ip);';
			jsFrmSub+=payJs+'src\';ip.value=\'1\';fm.appendChild(ip);'+
			payJs+'a3\';ip.value=\''+payValue.price+'\';fm.appendChild(ip);'+
			payJs+'p3\';ip.value=\''+payValue.every1+'\';fm.appendChild(ip);'+
			payJs+'t3\';ip.value=\''+payValue.everyA+'\';fm.appendChild(ip);'+
			payJs+'currency_code\';ip.value=\''+payValue.currency+'\';fm.appendChild(ip);'+
			payJs+'bn\';ip.value=\'PP-SubscriptionsBF:btn_subscribe'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif:NonHostedGuest\';fm.appendChild(ip);'+
			'document.body.appendChild(fm);fm.submit();';
		if(payValue.type==lang.buy){
			out='<input class="ckpaypal" type="image" src="https://www.paypalobjects.com/'+paypalLang5+'/i/btn/btn_buynow'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif" border="0" name="submit" alt="'+payValue.type+'|'+payValue.name+'|'+payValue.idnum+'|'+payValue.price+'|'+payValue.shipping+'||" title="'+payValue.name+' - '+payValue.price+payValue.currency+'" onClick="'+jsFrmBuy+'" />';
		}
		else if(payValue.type==lang.cart){
			out='<input class="ckpaypal" type="image" src="https://www.paypalobjects.com/'+paypalLang5+'/i/btn/btn_cart'+((payValue.app==lang.small)?'_SM':'_LG')+'.gif" border="0" name="submit" alt="'+payValue.type+'|'+payValue.name+'|'+payValue.idnum+'|'+payValue.price+'|'+payValue.shipping+'||" title="'+payValue.name+' - '+payValue.price+payValue.currency+'" onClick="'+jsFrmCart+'" />';
		}
		else if(payValue.type==lang.view){
			out='<input class="ckpaypal" type="image" src="https://www.paypalobjects.com/'+paypalLang5+'/i/btn/btn_viewcart'+((payValue.app==lang.small)?'_SM':'_LG')+'.gif" border="0" name="submit" alt="'+payValue.type+'|'+payValue.name+'|'+payValue.idnum+'|'+payValue.price+'|'+payValue.shipping+'||" title="'+payValue.type+'" onClick="'+jsFrmCart+'" />';
		}
		else if(payValue.type==lang.donate){
			out='<input class="ckpaypal" type="image" src="https://www.paypalobjects.com/'+paypalLang5+'/i/btn/btn_donate'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif" border="0" name="submit" alt="'+payValue.type+'|'+payValue.name+'|'+payValue.idnum+'|'+payValue.price+'|'+payValue.shipping+'||" title="'+payValue.type+'" onClick="'+jsFrmDon+'" />';
		}
		else if(payValue.type==lang.subscribe){
			out='<input class="ckpaypal" type="image" src="https://www.paypalobjects.com/'+paypalLang5+'/i/btn/btn_subscribe'+((payValue.app==lang.standflag)?'CC_LG':((payValue.app==lang.stand)?'_LG':'_SM'))+'.gif" border="0" name="submit" alt="'+payValue.type+'|'+payValue.name+'|'+payValue.idnum+'|'+payValue.price+'|'+payValue.shipping+'|'+payValue.every1+'|'+payValue.everyA+'" title="'+payValue.name+' - '+payValue.price+payValue.currency+'" onClick="'+jsFrmSub+'" />';
		}
		return out;
	};
	return{
		title:lang.buttonPaypal,
		minWidth:250,
		minHeight:250,
		contents:[{
			id:'ckpay0',
			label:'',
			title:'',
			expand:false,
			padding:0,
			elements:[
			// part 1
			{
				type:'select',
				id:'ckpayType',
				label:lang.buttonType,
				labelStyle:'float:left;line-height:1.6em;padding-right:30px;',
				items:[[lang.buy],[lang.cart],[lang.view],[lang.donate],[lang.subscribe]],
				onLoad:function(){
					var dia=CKEDITOR.dialog.getCurrent();
					if(this.getValue()==lang.buy){
						for(k in payBuy){
							if(payBuy[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.cart){
						for(k in payCart){
							if(payCart[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.view){
						for(k in payView){
							if(payView[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.donate){
						for(k in payDonate){
							if(payDonate[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
						if(paypalDonval)dia.getContentElement('ckpay0','ckpayPrice').getElement().show();
					}
					else{
						for(k in paySubscribe){
							if(paySubscribe[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
				},
				onChange:function(){
					var dia=CKEDITOR.dialog.getCurrent();
					if(this.getValue()==lang.buy){
						for(k in payBuy){
							if(payBuy[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.cart){
						for(k in payCart){
							if(payCart[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.view){
						for(k in payView){
							if(payView[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
					else if(this.getValue()==lang.donate){
						for(k in payDonate){
							if(payDonate[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
						if(paypalDonval)dia.getContentElement('ckpay0','ckpayPrice').getElement().show();
					}
					else{
						for(k in paySubscribe){
							if(paySubscribe[k])dia.getContentElement('ckpay0',k).getElement().show();
							else dia.getContentElement('ckpay0',k).getElement().hide();
						}
					}
				},
				commit:function(){payData.type=this.getValue();}
			},{
				type:'text',
				id:'ckpayName',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.labelitemName+' *',
				commit:function(){payData.name=this.getValue();}
			},{
				type:'text',
				id:'ckpayIdnum',
				labelStyle:'display:block;line-height:1.6em;margin-top:5px;',
				label:lang.labelIdnum,
				commit:function(){payData.idnum=this.getValue();}
			},{
				type:'text',
				id:'ckpayPrice',
				label:lang.labelprice+' *',
				labelStyle:'display:block;line-height:1.6em;',
				commit:function(){payData.price=this.getValue();}
			},{
				type:'text',
				id:'ckpayShipping',
				label:lang.labelshipping+' *',
				labelStyle:'display:block;line-height:1.6em;',
				commit:function(){payData.shipping=(this.getValue()?this.getValue():0);}
			},{
				type:'select',
				id:'ckpayEvery1',
				label:lang.every,
				labelStyle:'line-height:1.6em;',
				items:[[1],[2],[3],[4],[5],[6],[7],[8],[9],[10],[11],[12],[13],[14],[15],[16],[17],[18],[19],[20],[21],[22],[23],[24],[25],[26],[27],[28],[29],[30]],
				style:'max-width:100px;',
				commit:function(){payData.every1=this.getValue();}
			},{
				type:'select',
				id:'ckpayEveryA',
				labelStyle:'line-height:1.6em;',
				items:[[lang.jou],[lang.sem],[lang.moi],[lang.ann]],
				style:'float:left;margin-top:-12px;margin-left:35px;max-width:150px;',
				commit:function(){payData.everyA=((this.getValue()==lang.jou)?'D':((this.getValue()==lang.sem)?'W':((this.getValue()==lang.moi)?'M':'Y')));}
			},
			// part 2
			{
				type:'text',
				id:'ckpayEmail',
				label:lang.labelMerchantEmail+' *',
				labelStyle:'display:block;line-height:1.6em;margin-top:15px;padding-top:10px;border-top:1px dashed #aaa;',
				'default':paypalMail,
				validate:function(){
					if(!this.getValue()){
						document.getElementById('ckpaypalValid').innerHTML=lang.validateMerchantEmail;
						return false;
					};
				},
				commit:function(){payData.email=this.getValue();}
			},{
				type:'text',
				id:'ckpayCurrency',
				label:lang.labelcurrency+' *',
				'default':paypalCurr,
				commit:function(){payData.currency=this.getValue();}
			},{
				type:'text',
				id:'ckpayTax',
				label:lang.labeltax+' *',
				'default':paypalTax,
				commit:function(){payData.tax=(this.getValue()?this.getValue():0);}
			},{
				type:'select',
				id:'ckpayApp',
				label:lang.app,
				labelStyle:'line-height:1.6em;',
				items:[[lang.standflag],[lang.stand],[lang.small]],
				'default':((paypalApp=='CC_LG')?lang.standflag:((paypalApp=='_LG')?lang.stand:lang.small)),
				commit:function(){payData.app=this.getValue();}
			},{
				type:'select',
				id:'ckpayAct',
				label:lang.act,
				labelStyle:'line-height:1.6em;',
				items:[['products'],['services']],
				'default':paypalAct,
				commit:function(){payData.act=this.getValue();}
			},{
				type:'select',
				id:'ckpayDonval',
				label:lang.donval,
				labelStyle:'line-height:1.6em;',
				items:[[lang.donfix],[lang.donfree]],
				'default':((paypalDonval)?lang.donfix:lang.donfree),
				onChange:function(){
					var dia=CKEDITOR.dialog.getCurrent();
					if(this.getValue()==lang.donfree)dia.getContentElement('ckpay0','ckpayPrice').getElement().hide();
					else dia.getContentElement('ckpay0','ckpayPrice').getElement().show();
				},
				commit:function(){payData.donval=this.getValue();}
			},{
				type:'html',
				html:'<div id="ckpaypalValid" style="color:red;font-weight:700;margin-top:20px;"></div>'
			}]
		}],
		onOk:function(){
			this.commitContent();
			var paypalButton=payButton(payData);
			editor.insertHtml(paypalButton);
			return;
		},
		onShow:function(){
			var dia=CKEDITOR.dialog.getCurrent();
			dia.getContentElement('ckpay0','ckpayType').setValue(ckpayMem[0]);
			dia.getContentElement('ckpay0','ckpayName').setValue(ckpayMem[1]);
			dia.getContentElement('ckpay0','ckpayIdnum').setValue(ckpayMem[2]);
			dia.getContentElement('ckpay0','ckpayPrice').setValue(ckpayMem[3]);
			dia.getContentElement('ckpay0','ckpayShipping').setValue(ckpayMem[4]);
			dia.getContentElement('ckpay0','ckpayEvery1').setValue(ckpayMem[5]);
			dia.getContentElement('ckpay0','ckpayEveryA').setValue(((ckpayMem[6]=='Y')?lang.ann:((ckpayMem[6]=='W')?lang.sem:((ckpayMem[6]=='M')?lang.moi:lang.jou))));
			payData={};
			document.getElementById('ckpaypalValid').innerHTML='';
			return;
		}
	};
});
//
var tag=document.getElementsByTagName('span'),v;
for(v in tag){if((' '+tag[v].className+' ').indexOf(' cke_button__ckpaypal_icon ')>-1)tag[v].onclick=function(){ckpayMem=['','','','',''];};}

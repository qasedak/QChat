// (c) 2010 ElfChat http://elfchat.ru Elfet

(function($){$.fn.extend({render:function(data,return_html){var html=$.render(this.attr('id'),this.html(),data);if(return_html!=undefined&&return_html==true)
return html;else
return $(html);}});$.extend({render:function(name,tmpl,data){var fn;if($.templates[name]){fn=$.templates[name];}else{fn=$.tmpl(tmpl);$.templates[name]=fn;}
return fn.call(data,data,0);},templates:{},tmplcmd:{"each":{_default:[null,"$i"],prefix:"$.each($1,function($2){with(this){",suffix:"}});"},"if":{prefix:"if($1){",suffix:"}"},"else":{prefix:"}else{"},"elseif":{prefix:"}else if($1){"},"html":{prefix:"_.push($1);"},"js":{_default:["this"],prefix:"$1;"},"=":{_default:["this"],prefix:"_.push($.html_encode($1));"}},html_encode:function(text){var chars=Array("&","<",">",'"',"'");var replacements=Array("&amp;","&lt;","&gt;","&quot;","'");for(var i=0;i<chars.length;i++)
{var re=new RegExp(chars[i],"gi");if(re.test(text))
{text=text.replace(re,replacements[i]);}}
return text;},tmpl:function(str){var fn=new Function("save","$i","var $=jQuery,_=[];_.data=save;_.index=$i;"+"with(save){_.push('"+
str.replace(/'/g,"\\'").replace(/[\r\t\n]/g," ").replace(/{=([^}]*)}/g,"{= $1}").replace(/{(\/?)(\w+|.)(?:\((.*?)\))?(?: (.*?))?}/g,function(all,slash,type,fnargs,args){var tmpl=$.tmplcmd[type];if(!tmpl){throw"Template not found: "+type;}
var def=tmpl._default||"";if(args!=undefined)args=args.replace(/\\'/g,"'");return"');"+tmpl[slash?"suffix":"prefix"].split("$1").join(args||def[0]).split("$2").join(fnargs||def[1])+"_.push('";})
+"');}return _.join('');");return fn;}});})(jQuery);
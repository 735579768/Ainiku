!function(){function e(){var b,a=$G("tabHeads").children;for(b=0;b<a.length;b++)domUtils.on(a[b],"click",function(b){var c,d,e=b.target||b.srcElement;for(c=0;c<a.length;c++)d=a[c].getAttribute("data-content-id"),a[c]==e?(domUtils.addClass(a[c],"focus"),domUtils.addClass($G(d),"focus")):(domUtils.removeClasses(a[c],"focus"),domUtils.removeClasses($G(d),"focus"))})}function f(){o(["videoFloat","upload_alignment"]),q($G("videoUrl")),g(),function(){var b,d,e,f,g,a=editor.selection.getRange().getClosedNode();a&&a.className&&(d="edui-faked-video"==a.className,e=-1!=a.className.indexOf("edui-upload-video"),(d||e)&&($G("videoUrl").value=b=a.getAttribute("_url"),$G("videoWidth").value=a.width,$G("videoHeight").value=a.height,f=domUtils.getComputedStyle(a,"float"),g=domUtils.getComputedStyle(a.parentNode,"text-align"),h("center"===g?"center":f)),e&&(c=!0)),r(b)}()}function g(){dialog.onok=function(){$G("preview").innerHTML="";var a=k("tabHeads","tabSrc");switch(a){case"video":return i();case"videoSearch":return j("searchList");case"upload":return s()}},dialog.oncancel=function(){$G("preview").innerHTML=""}}function h(a){var d,c,b=$G("videoFloat").children;for(c=0;d=b[c++];)d.getAttribute("name")==a?"focus"!=d.className&&(d.className="focus"):"focus"==d.className&&(d.className="")}function i(){var a=$G("videoWidth"),b=$G("videoHeight"),d=$G("videoUrl").value,e=k("videoFloat","name");return d?m([a,b])?(editor.execCommand("insertvideo",{url:l(d),width:a.value,height:b.value,align:e},c?"upload":null),void 0):!1:!1}function j(a){var e,d,b=domUtils.getElementsByTagName($G(a),"img"),c=[];for(d=0;e=b[d++];)e.getAttribute("selected")&&c.push({url:e.getAttribute("ue_video_url"),width:420,height:280,align:"none"});editor.execCommand("insertvideo",c)}function k(a,b){var d,f,e,c=$G(a).children;for(e=0;f=c[e++];)if("focus"==f.className){d=f.getAttribute(b);break}return d}function l(a){return a?a=utils.trim(a).replace(/v\.youku\.com\/v_show\/id_([\w\-=]+)\.html/i,"player.youku.com/player.php/sid/$1/v.swf").replace(/(www\.)?youtube\.com\/watch\?v=([\w\-]+)/i,"www.youtube.com/v/$2").replace(/youtu.be\/(\w+)$/i,"www.youtube.com/v/$1").replace(/v\.ku6\.com\/.+\/([\w\.]+)\.html.*$/i,"player.ku6.com/refer/$1/v.swf").replace(/www\.56\.com\/u\d+\/v_([\w\-]+)\.html/i,"player.56.com/v_$1.swf").replace(/www.56.com\/w\d+\/play_album\-aid\-\d+_vid\-([^.]+)\.html/i,"player.56.com/v_$1.swf").replace(/v\.pps\.tv\/play_([\w]+)\.html.*$/i,"player.pps.tv/player/sid/$1/v.swf").replace(/www\.letv\.com\/ptv\/vplay\/([\d]+)\.html.*$/i,"i7.imgs.letv.com/player/swfPlayer.swf?id=$1&autoplay=0").replace(/www\.tudou\.com\/programs\/view\/([\w\-]+)\/?/i,"www.tudou.com/v/$1").replace(/v\.qq\.com\/cover\/[\w]+\/[\w]+\/([\w]+)\.html/i,"static.video.qq.com/TPout.swf?vid=$1").replace(/v\.qq\.com\/.+[\?\&]vid=([^&]+).*$/i,"static.video.qq.com/TPout.swf?vid=$1").replace(/my\.tv\.sohu\.com\/[\w]+\/[\d]+\/([\d]+)\.shtml.*$/i,"share.vrs.sohu.com/my/v.swf&id=$1"):""}function m(a){var c,b,d;for(b=0;c=a[b++];)if(d=c.value,!n(d)&&d)return alert(lang.numError),c.value="",c.focus(),!1;return!0}function n(a){return/(0|^[1-9]\d*$)/.test(a)}function o(a){var c,b,d,e,f,g;for(b=0;c=a[b++];){d=$G(c),e={none:lang["default"],left:lang.floatLeft,right:lang.floatRight,center:lang.block};for(f in e)g=document.createElement("div"),g.setAttribute("name",f),"none"==f&&(g.className="focus"),g.style.cssText="background:url(images/"+f+"_focus.jpg);",g.setAttribute("title",e[f]),d.appendChild(g);p(c)}}function p(a){var d,c,b=$G(a).children;for(c=0;d=b[c++];)domUtils.on(d,"click",function(){for(var c,a=0;c=b[a++];)c.className="",c.removeAttribute&&c.removeAttribute("class");this.className="focus"})}function q(a){browser.ie?a.onpropertychange=function(){r(this.value)}:a.addEventListener("input",function(){r(this.value)},!1)}function r(a){if(a){var b=l(a);$G("preview").innerHTML='<div class="previewMsg"><span>'+lang.urlError+"</span></div>"+'<embed class="previewVideo" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer"'+' src="'+b+'"'+' width="'+420+'"'+' height="'+280+'"'+' wmode="transparent" play="true" loop="false" menu="false" allowscriptaccess="never" allowfullscreen="true" >'+"</embed>"}}function s(){var h,i,j,a=[],c=editor.getOpt("videoUrlPrefix"),e=$G("upload_width").value||420,f=$G("upload_height").value||280,g=k("upload_alignment","name")||"none";for(h in b)i=b[h],a.push({url:c+i.url,width:e,height:f,align:g});return(j=d.getQueueCount())?($(".info","#queueList").html('<span style="color:red;">'+"还有2个未上传文件".replace(/[\d]/,j)+"</span>"),!1):(editor.execCommand("insertvideo",a,"upload"),void 0)}function t(){d=new u("queueList")}function u(a){this.$wrap=a.constructor==String?$("#"+a):$(a),this.init()}var d,b=[],c=!1;window.onload=function(){$focus($G("videoUrl")),e(),f(),t()},u.prototype={init:function(){this.fileList=[],this.initContainer(),this.initUploader()},initContainer:function(){this.$queue=this.$wrap.find(".filelist")},initUploader:function(){function y(a){var b=c('<li id="'+a.id+'">'+'<p class="title">'+a.name+"</p>"+'<p class="imgWrap"></p>'+'<p class="progress"><span></span></p>'+"</li>"),d=c('<div class="file-panel"><span class="cancel">'+lang.uploadDelete+"</span>"+'<span class="rotateRight">'+lang.uploadTurnRight+"</span>"+'<span class="rotateLeft">'+lang.uploadTurnLeft+"</span></div>").appendTo(b),e=b.find("p.progress span"),f=b.find("p.imgWrap"),g=c('<p class="error"></p>').hide().appendTo(b),h=function(a){switch(a){case"exceed_size":text=lang.errorExceedSize;break;case"interrupt":text=lang.errorInterrupt;break;case"http":text=lang.errorHttp;break;case"not_allow_type":text=lang.errorFileType;break;default:text=lang.errorUploadRetry}g.text(text).show()};"invalid"===a.getStatus()?h(a.statusText):(f.text(lang.uploadPreview),-1=="|png|jpg|jpeg|bmp|gif|".indexOf("|"+a.ext.toLowerCase()+"|")?f.empty().addClass("notimage").append('<i class="file-preview file-type-'+a.ext.toLowerCase()+'"></i>'+'<span class="file-title">'+a.name+"</span>"):browser.ie&&browser.version<=7?f.text(lang.uploadNoPreview):u.makeThumb(a,function(a,b){if(a||!b||/^data:/.test(b)&&browser.ie&&browser.version<=7)f.text(lang.uploadNoPreview);else{var d=c('<img src="'+b+'">');f.empty().append(d),d.on("error",function(){f.text(lang.uploadNoPreview)})}},p,q),s[a.id]=[a.size,0],a.rotation=0,a.ext&&-1!=x.indexOf(a.ext.toLowerCase())||(h("not_allow_type"),u.removeFile(a))),a.on("statuschange",function(c,f){"progress"===f?e.hide().width(0):"queued"===f&&(b.off("mouseenter mouseleave"),d.remove()),"error"===c||"invalid"===c?(h(a.statusText),s[a.id][1]=1):"interrupt"===c?h("interrupt"):"queued"===c?s[a.id][1]=0:"progress"===c&&(g.hide(),e.css("display","block")),b.removeClass("state-"+f).addClass("state-"+c)}),b.on("mouseenter",function(){d.stop().animate({height:30})}),b.on("mouseleave",function(){d.stop().animate({height:0})}),d.on("click","span",function(){var d,b=c(this).index();switch(b){case 0:return u.removeFile(a),void 0;case 1:a.rotation+=90;break;case 2:a.rotation-=90}t?(d="rotate("+a.rotation+"deg)",f.css({"-webkit-transform":d,"-mos-transform":d,"-o-transform":d,transform:d})):f.css("filter","progid:DXImageTransform.Microsoft.BasicImage(rotation="+~~(a.rotation/90%4+4)%4+")")}),b.insertBefore(j)}function z(a){var b=c("#"+a.id);delete s[a.id],A(),b.off().find(".file-panel").off().end().remove()}function A(){var e,a=0,b=0,d=l.children();c.each(s,function(c,d){b+=d[0],a+=d[0]*d[1]}),e=b?a/b:0,d.eq(0).text(Math.round(100*e)+"%"),d.eq(1).css("width",Math.round(100*e)+"%"),C()}function B(b){if(b!=r){var d=u.getStats();switch(h.removeClass("state-"+r),h.addClass("state-"+b),b){case"pedding":e.addClass("element-invisible"),f.addClass("element-invisible"),k.removeClass("element-invisible"),l.hide(),g.hide(),u.refresh();break;case"ready":k.addClass("element-invisible"),e.removeClass("element-invisible"),f.removeClass("element-invisible"),l.hide(),g.show(),h.text(lang.uploadStart),u.refresh();break;case"uploading":l.show(),g.hide(),h.text(lang.uploadPause);break;case"paused":l.show(),g.hide(),h.text(lang.uploadContinue);break;case"confirm":if(l.show(),g.hide(),h.text(lang.uploadStart),d=u.getStats(),d.successNum&&!d.uploadFailNum)return B("finish"),void 0;break;case"finish":l.hide(),g.show(),d.uploadFailNum?h.text(lang.uploadRetry):h.text(lang.uploadStart)}r=b,C()}a.getQueueCount()?h.removeClass("disabled"):h.addClass("disabled")}function C(){var b,a="";"ready"===r?a=lang.updateStatusReady.replace("_",m).replace("_KB",WebUploader.formatSize(n)):"confirm"===r?(b=u.getStats(),b.uploadFailNum&&(a=lang.updateStatusConfirm.replace("_",b.successNum).replace("_",b.successNum))):(b=u.getStats(),a=lang.updateStatusFinish.replace("_",m).replace("_KB",WebUploader.formatSize(n)).replace("_",b.successNum),b.uploadFailNum&&(a+=lang.updateStatusError.replace("_",b.uploadFailNum))),g.html(a)}var u,a=this,c=jQuery,d=a.$wrap,e=d.find(".filelist"),f=d.find(".statusBar"),g=f.find(".info"),h=d.find(".uploadBtn"),j=(d.find(".filePickerBtn"),d.find(".filePickerBlock")),k=d.find(".placeholder"),l=f.find(".progress").hide(),m=0,n=0,o=window.devicePixelRatio||1,p=113*o,q=113*o,r="",s={},t=function(){var a=document.createElement("p").style,b="transition"in a||"WebkitTransition"in a||"MozTransition"in a||"msTransition"in a||"OTransition"in a;return a=null,b}(),v=editor.getActionUrl(editor.getOpt("videoActionName")),w=editor.getOpt("videoMaxSize"),x=(editor.getOpt("videoAllowFiles")||[]).join("").replace(/\./g,",").replace(/^[,]/,"");return WebUploader.Uploader.support()?editor.getOpt("videoActionName")?(u=a.uploader=WebUploader.create({pick:{id:"#filePickerReady",label:lang.uploadSelectFile},swf:"../../third-party/webuploader/Uploader.swf",server:v,fileVal:editor.getOpt("videoFieldName"),duplicate:!0,fileSingleSizeLimit:w,compress:!1}),u.addButton({id:"#filePickerBlock"}),u.addButton({id:"#filePickerBtn",label:lang.uploadAddFile}),B("pedding"),u.on("fileQueued",function(a){m++,n+=a.size,1===m&&(k.addClass("element-invisible"),f.show()),y(a)}),u.on("fileDequeued",function(a){m--,n-=a.size,z(a),A()}),u.on("filesQueued",function(){u.isInProgress()||"pedding"!=r&&"finish"!=r&&"confirm"!=r&&"ready"!=r||B("ready"),A()}),u.on("all",function(a,b){switch(a){case"uploadFinished":B("confirm",b);break;case"startUpload":var c=utils.serializeParam(editor.queryCommandValue("serverparam"))||"",d=utils.formatUrl(v+(-1==v.indexOf("?")?"?":"&")+"encode=utf-8&"+c);u.option("server",d),B("uploading",b);break;case"stopUpload":B("paused",b)}}),u.on("uploadBeforeSend",function(a,b,c){c["X_Requested_With"]="XMLHttpRequest"}),u.on("uploadProgress",function(a,b){var d=c("#"+a.id),e=d.find(".progress span");e.css("width",100*b+"%"),s[a.id][1]=b,A()}),u.on("uploadSuccess",function(a,d){var f,g,e=c("#"+a.id);try{f=d._raw||d,g=utils.str2json(f),"SUCCESS"==g.state?(b.push({url:g.url,type:g.type,original:g.original}),e.append('<span class="success"></span>')):e.find(".error").text(g.state).show()}catch(h){e.find(".error").text(lang.errorServerUpload).show()}}),u.on("uploadError",function(){}),u.on("error",function(a,b){("Q_TYPE_DENIED"==a||"F_EXCEED_SIZE"==a)&&y(b)}),u.on("uploadComplete",function(){}),h.on("click",function(){return c(this).hasClass("disabled")?!1:("ready"===r?u.upload():"paused"===r?u.upload():"uploading"===r&&u.stop(),void 0)}),h.addClass("state-"+r),A(),void 0):(c("#filePickerReady").after(c("<div>").html(lang.errorLoadConfig)).hide(),void 0):(c("#filePickerReady").after(c("<div>").html(lang.errorNotSupport)).hide(),void 0)},getQueueCount:function(){var a,b,c,d=0,e=this.uploader.getFiles();for(b=0;a=e[b++];)c=a.getStatus(),("queued"==c||"uploading"==c||"progress"==c)&&d++;return d},refresh:function(){this.uploader.refresh()}}}();
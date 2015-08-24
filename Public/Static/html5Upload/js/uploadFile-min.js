;function UPLOADFILE(){this.fileInput=null;this.uploadInput=null;this.dragDrop=null;this.url="";this.data={};this.uploadFile=[];this.lastUploadFile=[];this.perUploadFile=[];this.fileNum=0;this.filterFile=function(files){return files;};this.onSelect=function(selectFile,files){};this.onDelete=function(file,files){};this.onProgress=function(file,loaded,total){};this.onSuccess=function(file,responseInfo){};this.onFailure=function(file,responseInfo){};this.onComplete=function(responseInfo){};this.funDragHover=function(e){e.stopPropagation();e.preventDefault();this[e.type==="dragover"?"onDragOver":"onDragLeave"].call(e.target);return this;};this.funGetFiles=function(e){var self=this;this.funDragHover(e);var files=e.target.files||e.dataTransfer.files;self.lastUploadFile=this.uploadFile;this.uploadFile=this.uploadFile.concat(this.filterFile(files));var tmpFiles=[];var lArr=[];var uArr=[];$.each(self.lastUploadFile,function(k,v){lArr.push(v.name);});$.each(self.uploadFile,function(k,v){uArr.push(v.name);});$.each(uArr,function(k,v){if($.inArray(v,lArr)<0){tmpFiles.push(self.uploadFile[k]);}});this.uploadFile=tmpFiles;this.funDealtFiles();return true;};this.funDealtFiles=function(){var self=this;$.each(this.uploadFile,function(k,v){v.index=self.fileNum;self.fileNum++;});var selectFile=this.uploadFile;this.perUploadFile=this.perUploadFile.concat(this.uploadFile);this.uploadFile=this.lastUploadFile.concat(this.uploadFile);this.onSelect(selectFile,this.uploadFile);return this;};this.funDeleteFile=function(delFileIndex,isCb){var self=this;var tmpFile=[];var delFile=this.perUploadFile[delFileIndex];$.each(this.uploadFile,function(k,v){if(delFile!=v){tmpFile.push(v);}else{}});this.uploadFile=tmpFile;if(isCb){self.onDelete(delFile,this.uploadFile);}
return true;};this.funUploadFiles=function(){var self=this;$.each(this.uploadFile,function(k,v){self.funUploadFile(v);});};this.funUploadFile=function(file){var self=this;var formdata=new FormData();formdata.append("filelist",file);var b=this.data;for(var a in b){formdata.append(a,b[a]);}
var xhr=new XMLHttpRequest();xhr.upload.addEventListener("progress",function(e){self.onProgress(file,e.loaded,e.total);},false);xhr.addEventListener("load",function(e){self.funDeleteFile(file.index,false);self.onSuccess(file,xhr.responseText);if(self.uploadFile.length==0){self.onComplete("全部完成");}},false);xhr.addEventListener("error",function(e){self.onFailure(file,xhr.responseText);},false);xhr.open("POST",self.url,true);xhr.setRequestHeader("X_FILENAME",encodeURIComponent(file.name));xhr.send(formdata);};this.funReturnNeedFiles=function(){return this.uploadFile;};this.init=function(){var self=this;if(this.dragDrop){this.dragDrop.addEventListener("dragover",function(e){self.funDragHover(e);},false);this.dragDrop.addEventListener("dragleave",function(e){self.funDragHover(e);},false);this.dragDrop.addEventListener("drop",function(e){self.funGetFiles(e);},false);}
if(self.fileInput){this.fileInput.addEventListener("change",function(e){self.funGetFiles(e);},false);}
if(self.uploadInput){this.uploadInput.addEventListener("click",function(e){self.funUploadFiles(e);},false);}};};(function($,undefined){$.fn.zyUpload=function(options,param){var otherArgs=Array.prototype.slice.call(arguments,1);if(typeof options=='string'){var fn=this[0][options];if($.isFunction(fn)){return fn.apply(this,otherArgs);}else{throw("zyUpload - No such method: "+options);}}
return this.each(function(){var para={};var self=this;var defaults={data:{},width:"700px",height:"400px",itemWidth:"140px",itemHeight:"120px",url:"/upload/UploadAction",multiple:true,dragDrop:true,del:true,finishDel:false,onSelect:function(selectFiles,files){},onDelete:function(file,files){},onSuccess:function(file){},onFailure:function(file){},onComplete:function(responseInfo){}};para=$.extend(defaults,options);this.init=function(){this.createHtml();this.createCorePlug();};this.createHtml=function(){var multiple="";para.multiple?multiple="multiple":multiple="";var html='';if(para.dragDrop){html+='<form class="uploadForm" action="'+para.url+'" method="post" enctype="multipart/form-data">';html+=' <div class="upload_box">';html+='  <div class="upload_main">';html+='   <div class="upload_choose">';html+='    <div class="convent_choice">';html+='     <div class="andArea">';html+='      <div class="filePicker"><b class="addimg">+</b>添加文件</div>';html+='      <input   class="fileImage"  type="file" size="30" name="fileselect[]" '+multiple+'>';html+='     </div>';html+='    </div>';html+='    <span  class="fileDragArea upload_drag_area">或者将文件拖到此处</span>';html+='   </div>';html+='   <div class="status_bar">';html+='    <div  class="info status_info">选中0张，共0B。</div>';html+='    <div class="btns">';html+='     <div class="webuploader_pick">继续选择</div>';html+='     <div class="upload_btn">开始上传</div>';html+='    </div>';html+='   </div>';html+='   <div  class="preview upload_preview"></div>';html+='  </div>';html+='  <div class="upload_submit">';html+='   <button type="button"  class="fileSubmit upload_submit_btn">确认上传文件</button>';html+='  </div>';html+='  <div id="uploadInf" class="upload_inf"></div>';html+=' </div>';html+='</form>';}else{var imgWidth=parseInt(para.itemWidth.replace("px",""))-15;html+='<form class="uploadForm" action="'+para.url+'" method="post" enctype="multipart/form-data">';html+=' <div class="upload_box">';html+='  <div class="upload_main single_main">';html+='   <div class="status_bar">';html+='    <div class="info status_info">选中0张，共0B。</div>';html+='    <div class="btns">';html+='     <input class="fileImage" type="file" size="30" name="fileselect[]" '+multiple+'>';html+='     <div class="webuploader_pick">选择文件</div>';html+='     <div class="upload_btn">开始上传</div>';html+='    </div>';html+='   </div>';html+='   <div  class="preview upload_preview">';html+='    <div class="add_upload">';html+='     <a style="height:'+para.itemHeight+';width:'+para.itemWidth+';" title="点击添加文件" id="" class="rapidAddImg    add_imgBox" href="javascript:void(0)">';html+='      <div class="uploadImg" style="width:'+imgWidth+'px">';html+='       <img class="upload_image" src="/Public/Static/html5Upload/images/add_img.png" style="width:expression(this.width > '+imgWidth+' ? '+imgWidth+'px : this.width)" />';html+='      </div>';html+='     </a>';html+='    </div>';html+='   </div>';html+='  </div>';html+='  <div class="upload_submit">';html+='   <button type="button"  class="fileSubmit upload_submit_btn">确认上传文件</button>';html+='  </div>';html+='  <div id="uploadInf" class="upload_inf"></div>';html+=' </div>';html+='</form>';}
$(self).append(html).css({"width":para.width,"height":para.height});this.addEvent();};this.funSetStatusInfo=function(files){var size=0;var num=files.length;$.each(files,function(k,v){size+=v.size;});if(size>1024*1024){size=(Math.round(size*100/(1024*1024))/100).toString()+'MB';}else{size=(Math.round(size*100/1024)/100).toString()+'KB';}
$(para.parentsel+" .status_info").html("选中"+num+"张，共"+size+"。");};this.funFilterEligibleFile=function(files){var arrFiles=[];for(var i=0,file;file=files[i];i++){if(file.size>=512000000){alert('您这个"'+file.name+'"文件大小过大');}else{arrFiles.push(file);}}
return arrFiles;};this.funDisposePreviewHtml=function(file,e){var html="";var imgWidth=parseInt(para.itemWidth.replace("px",""))-15;var delHtml="";if(para.del){delHtml='<span class="file_del" data-index="'+file.index+'" title="删除"></span>';}
var fileImgSrc="/Public/Static/html5Upload/images/fileType/";if(file.name.indexOf(".rar")>0){fileImgSrc=fileImgSrc+"rar.png";}else if(file.name.indexOf(".zip")>0){fileImgSrc=fileImgSrc+"zip.png";}else if(file.name.indexOf(".text")>0){fileImgSrc=fileImgSrc+"txt.png";}else if(file.name.indexOf(".mp4")>0){fileImgSrc=fileImgSrc+"video.png";}else{fileImgSrc=fileImgSrc+"other.png";}
if(file.type.indexOf("image")==0){html+='<div  class="uploadList_'+file.index+'  upload_append_list">';html+=' <div class="file_bar">';html+='  <div style="padding:5px;">';html+='   <p class="file_name">'+file.name+'</p>';html+=delHtml;html+='  </div>';html+=' </div>';html+=' <a style="height:'+para.itemHeight+';width:'+para.itemWidth+';" href="#" class="imgBox">';html+='  <div class="uploadImg" style="width:'+imgWidth+'px">';html+='   <img id="uploadImage_'+file.index+'" class="upload_image" src="'+e.target.result+'" style="width:expression(this.width > '+imgWidth+' ? '+imgWidth+'px : this.width)" />';html+='  </div>';html+=' </a>';html+=' <p class="uploadProgress_'+file.index+' file_progress"></p>';html+=' <p id="uploadFailure_'+file.index+'" class="file_failure">上传失败，请重试</p>';html+=' <p id="uploadSuccess_'+file.index+'" class="file_success"></p>';html+='</div>';}else{html+='<div  class="uploadList_'+file.index+'  upload_append_list">';html+=' <div class="file_bar">';html+='  <div style="padding:5px;">';html+='   <p class="file_name">'+file.name+'</p>';html+=delHtml;html+='  </div>';html+=' </div>';html+=' <a style="height:'+para.itemHeight+';width:'+para.itemWidth+';" href="#" class="imgBox">';html+='  <div class="uploadImg" style="width:'+imgWidth+'px">';html+='   <img id="uploadImage_'+file.index+'" class="upload_image" src="'+fileImgSrc+'" style="width:expression(this.width > '+imgWidth+' ? '+imgWidth+'px : this.width)" />';html+='  </div>';html+=' </a>';html+=' <p class="uploadProgress_'+file.index+' file_progress"></p>';html+=' <p id="uploadFailure_'+file.index+'" class="file_failure">上传失败，请重试</p>';html+=' <p id="uploadSuccess_'+file.index+'" class="file_success"></p>';html+='</div>';}
return html;};this.createCorePlug=function(){var params={data:para.data,fileInput:$(para.parentsel+" .fileImage").get(0),uploadInput:$(para.parentsel+" .fileSubmit").get(0),dragDrop:$(para.parentsel+" .fileDragArea").get(0),url:$(para.parentsel+" .uploadForm").attr("action"),filterFile:function(files){return self.funFilterEligibleFile(files);},onSelect:function(selectFiles,allFiles){para.onSelect(selectFiles,allFiles);self.funSetStatusInfo(self.ZYFILE.funReturnNeedFiles());var html='',i=0;var funDealtPreviewHtml=function(){var file=selectFiles[i];if(file){var reader=new FileReader()
reader.onload=function(e){html+=self.funDisposePreviewHtml(file,e);i++;funDealtPreviewHtml();}
reader.readAsDataURL(file);}else{funAppendPreviewHtml(html);}};var funAppendPreviewHtml=function(html){if(para.dragDrop){$(para.parentsel+" .preview").append(html);}else{$(para.parentsel+" .add_upload").before(html);}
funBindDelEvent();funBindHoverEvent();};var funBindDelEvent=function(){if($(para.parentsel+" .file_del").length>0){$(para.parentsel+" .file_del").click(function(){self.ZYFILE.funDeleteFile(parseInt($(this).attr("data-index")),true);return false;});}
if($(para.parentsel+" .file_edit").length>0){$(para.parentsel+" .file_edit").click(function(){return false;});}};var funBindHoverEvent=function(){$(para.parentsel+" .upload_append_list").hover(function(e){$(this).find(".file_bar").addClass("file_hover");},function(e){$(this).find(".file_bar").removeClass("file_hover");});};funDealtPreviewHtml();},onDelete:function(file,files){$(para.parentsel+"  .uploadList_"+file.index).fadeOut();self.funSetStatusInfo(files);},onProgress:function(file,loaded,total){var eleProgress=$(para.parentsel+"  .uploadProgress_"+file.index),percent=(loaded/total*100).toFixed(2)+'%';if(eleProgress.is(":hidden")){eleProgress.show();}
eleProgress.css("width",percent);eleProgress.html(percent);},onSuccess:function(file,response){para.onSuccess(file,response);if(para.finishDel){$(para.parentsel+"  .uploadList_"+file.index).fadeOut();self.funSetStatusInfo(self.ZYFILE.funReturnNeedFiles());}},onFailure:function(file){para.onFailure(file);},onComplete:function(response){para.onComplete(response);},onDragOver:function(){$(this).addClass("upload_drag_hover");},onDragLeave:function(){$(this).removeClass("upload_drag_hover");}};self.ZYFILE=null
self.ZYFILE=$.extend(new UPLOADFILE(),params);self.ZYFILE.init();};this.addEvent=function(){if($(para.parentsel+" .filePicker").length>0){$(para.parentsel+" .filePicker").bind("click",function(e){$(para.parentsel+" .fileImage").click();});}
$(para.parentsel+"   .webuploader_pick").bind("click",function(e){$(para.parentsel+" .fileImage").click();});$(para.parentsel+"   .upload_btn").bind("click",function(e){if(self.ZYFILE.funReturnNeedFiles().length>0){$(para.parentsel+" .fileSubmit").click();}else{alert("请先选中文件再点击上传");}});if($(para.parentsel+"   .rapidAddImg").length>0){$(para.parentsel+"   .rapidAddImg").bind("click",function(e){$(para.parentsel+" .fileImage").click();});}};this.init();});};})(jQuery);
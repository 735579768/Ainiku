$(function() {
  window.commentsobj = {
    conf: {},
    formhtml: '',
    init: function(conf) {
      //$('#comments-name').val(commentsobj.readCookie('ankc_homecomment_name'));
      commentsobj.setliuyan();
      if (this.formhtml == '') {
        this.formhtml = $('#comments-form').html();
      }
      this.conf = conf;

    },
    initfocus: function() {

      $('.auto-label').focus(function(event) {
        var _this = $(this);
        var labobj = _this.prev();
        var ms = _this.attr('data-msg');
        _this.css('borderColor', '#ff6700');
        labobj.css('color', '#ff6700');
        //if (_this.val() == '') {
        labobj.show();
        _this.attr('placeholder', '');
        labobj.animate({
            top: -9,
            fontSize: 12
          },
          300,
          function() {
            _this.attr('placeholder', ms);
          });
        //}
      });
      $('.auto-label').blur(function(event) {
        var _this = $(this);
        var labobj = _this.prev();
        var ms = _this.attr('data-msg');
        _this.css('borderColor', '#e0e0e0');
        labobj.css('color', '#bbb');
        if (_this.val() == '') {
          _this.attr('placeholder', '');
          labobj.animate({
              top: 9,
              fontSize: 14
            },
            300,
            function() {
              _this.attr('placeholder', labobj.html());
            });
        }


      });
      $('.form-section .auto-label').each(function(index) {
        var str = $(this).val();
        if (str != '') {
          var _this = $(this);
          var labobj = _this.prev();
          var ms = _this.attr('data-msg');
          labobj.css({
            color: '#bbb',
            top: -9,
            fontSize: 12
          });
          _this.attr('placeholder', ms);
          labobj.show();
          _this.attr('placeholder', '');
        }

      });
    },
    initCheckMore: function() {
      //初始化查看更多回复
      $('.huifu-list').each(function(index, el) {
        var dle = $(this).children('dl');
        if (dle.length > 0) {
          var o = $(this).parent().prev().find('span').eq(0);
          if (!o.next().hasClass('check-huifu')) {
            o.after('<a class="check-huifu" onClick="commentsobj.checkMore(this);" href="javascript:;">收起>></a>');
          }

        }

      });
    },
    checkMore: function(obj) {
      var _t = $(obj);
      var tex = _t.text();
      if (tex == '查看更多回复>>') {

        _t.parents('dt').next().children('.huifu-list').slideDown(200, function() {
          _t.html('收起>>');
        });
      } else {

        _t.parents('dt').next().children('.huifu-list').slideUp(200, function() {
          _t.html('查看更多回复>>');
        });
      }
    },
    //单击回复按钮
    huifuclick: function(obj, pid, id) {
      // $('#commentsform').remove();
      var _t = $(obj);
      var dobj = _t.parents('dt').next();
      // ank.msgDialog({
      //   title: '留言回复:@' + _t.parents('span').children().eq(0).html(),
      //   width: 450,
      //   height: 'auto',
      //   content: '',
      //   cancel: function() {
      //     $('#comments-form').append(commentsobj.formhtml);
      //     //$('#comments-name').val(commentsobj.readCookie('ankc_homecomment_name'));
      //     commentsobj.setliuyan();
      //     commentsobj.initfocus();
      //   }
      // });
      layer.open({
        type: 1,
        title: '留言回复:@' + _t.parents('span').children().eq(0).html(),
        area: ['450px', 'auto'],
        content: $('#comments-form'),
        cancel: function() {
          // debugger;

          // $('#comments-form').append(commentsobj.formhtml);
          //$('#comments-name').val(commentsobj.readCookie('ankc_homecomment_name'));
          // commentsobj.setliuyan();
          // commentsobj.initfocus();
        },
        end: function() {
          $('#comments-form').show();
        }
      });
      $('#dialog-conn').append(this.formhtml);
      //$('#comments-name').val(commentsobj.readCookie('ankc_homecomment_name'));
      commentsobj.setliuyan();
      // pid = pid ? pid : id;
      $('#commentpid').val(id);
      commentsobj.initfocus();
      return true;
    },
    //取消回复按钮
    cancelhuifu: function(obj) {
      $('#comments-form').html($('#commentsform'));
      // //$('#comments-name').val(commentsobj.readCookie('ankc_homecomment_name'));
      commentsobj.setliuyan();
      $('#commentsform').show();
      $('#commentpid').val(0);
      $('#cancelbtn').hide();
    },
    /**
     * 初始化留言框
     */
    setliuyan: function() {

      $('#comments-name').val(decodeURIComponent(commentsobj.readCookie('ankc_homecomment_name')));
      $('#comments-email').val(decodeURIComponent(commentsobj.readCookie('ankc_homecomment_email')));
      $('#comments-homeurl').val(decodeURIComponent(commentsobj.readCookie('ankc_homecomment_url')));
    },
    //提交评论
    addcomments: function() {
      var formobj = $('#commentsform');
      formobj.submit(function(e) {
        return false;
      });
      if (!(/^[a-zA-Z0-9]{4}$/.test($('#verifyimg').val()))) {
        ank.msg('请填入正确的验证码!');
        return false;
      }
      if (/^\s*$/.test($('#comments-content').val())) {
        ank.msg('请填入内容!');
        return false;
      }
      if (this.conf.name) {
        if (!(/^.{2,5}$/.test($('#comments-name').val()))) {
          ank.msg('请填入正确的名字!');
          return false;
        }
      }
      if (this.conf.email) {
        if (!(/^[\w-]+(\.[\w-]+)*@[\w-]+(\.[\w-]+)+$/.test($('#comments-email').val()))) {
          ank.msg('请填入正确的邮箱!');
          return false;
        }
      }
      if (this.conf.homeurl) {
        //if (!(/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/.test($('#comments-homeurl').val()))) {
        if (!(/^(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(:\d+)?(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/.test($('#comments-homeurl').val()))) {
          ank.msg('请填入正确的网址!');
          return false;
        }
      }
      var uri = formobj.attr("action");
      postdata = formobj.serialize();
      // $("body").append('<div id="liuyanklbg" class="bg">');
      $('#addcommentsbtn').html('正在提交...');
      $.ajax({
        url: uri,
        type: "POST",
        datatype: "JSON",
        data: postdata,
        success: function(da) {
          if (da.status == '1') {
            commentsobj.cancelhuifu();
            ank.msg(da.info.msg);
            var o = $('#comments-huifulist-' + da.info.pid);
            if (da.info.pid == 0) {
              $('#comments-list').prepend(da.info.content);
            } else {
              o.prepend(da.info.content);
            }
            $('#dialog-wrap').remove();
            /*            var len = formobj.parents('#comments-list').length;

                        if (len === 1) {
                          formobj.before(da.info.content);
                          cancelhuifu();
                        } else {
                          $('#comments-list').prepend(da.info.content);
                        }*/
          } else {
            ank.msg(da.info);
          }
          $('#addcommentsbtn').html('提交评论');
        }

      });
      return false;
    },
    writeCookie: function(name, value, hours) {
      var expire = "";
      if (hours != null) {
        expire = new Date(new Date().getTime() + hours * 36e5);
        expire = "; expires=" + expire.toGMTString();
      }
      document.cookie = name + "=" + encodeURI(value) + expire;
    },

    ///////////////////////////////////////////////////////////////用cookies名字读它的值////////////////////////////
    readCookie: function(name) {
      var cookieValue = "";
      var search = name + "=";
      if (document.cookie.length > 0) {
        offset = document.cookie.indexOf(search);
        if (offset != -1) {
          offset += search.length;
          end = document.cookie.indexOf(";", offset);
          if (end == -1) end = document.cookie.length;
          cookieValue = decodeURI(document.cookie.substring(offset, end));
        }
      }
      return cookieValue;
    }

  };
  commentsobj.init(commentsconf);
  commentsobj.initfocus();
  //ank.loadhtml(commentsobj.conf.url, {}, , 'POST');
  $.ajax({
    url: commentsobj.conf.url,
    type: 'GET',
    success: function(da) {
      $('#comments-list').append(da.data);
    }
  });

});
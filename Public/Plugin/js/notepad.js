$(function() {
  window.notepad = {
    notepad_dom: null,
    init: function() {
      this.notepad_dom = $('#notepad_wrap');
      this.notepad_dom.css('left', am.readCookie('notepad_left') + 'px');
      this.notepad_dom.css('top', am.readCookie('notepad_top') + 'px');
      this.notepad_dom.kldrag({
        titleheight: 30
      });
      $('#admintheme').after('<i onClick="notepad.show();" style="font-size:16px;" class="fa fa-pencil-square-o"></i>');
      if (am.readCookie('notepad_open') == 1) {
        this.notepad_dom.show();
      } else {
        am.writeCookie('notepad_open', 0);
      }
      this.notepad_dom.mousemove(function(event) {
        notepad.setPosition();
      });
    },
    setPosition: function() {
      var left = this.notepad_dom.offset().left;
      var top = this.notepad_dom.offset().top;
      am.writeCookie('notepad_left', left);
      am.writeCookie('notepad_top', top);
    },
    hide: function() {
      this.notepad_dom.hide();
      am.writeCookie('notepad_open', 0);
    },
    editNotepad: function(uri) {
      var index = layer.load(1, {
        shade: [0.1, '#fff'] //0.1透明度的白色背景
      });
      $.get(uri, function(data) {
        layer.close(index);
        layer.open({
          shade: 0,
          title: '编辑内容',
          type: 1,
          content: data
        });
      });

    },
    saveNotepad: function(obj) {
      var _t = $(obj);
      var _f = _t.parents('form');
      _f.prop({
        action: _f.prop('action') + '&p=' + $('#cur_p').val(),
      })
      am.ajaxForm(obj, function(data) {
        if (data.status == 1) {
          $('#notepad-list').html(data.data);
        }
      });
    },
    delNotepad: function(uri) {
      layer.confirm('确定要删除吗?', {
        btn: ['确定', '取消'],
        yes: function(index, dom) {
          layer.close(index);
          $.get(uri, function(data) {
            ank.msg(data);
          });
        }
      });
    },
    show: function() {
      var o = $('#notepad_wrap');
      o.toggle();
      var cok = am.readCookie('notepad_open');
      if (cok == 1) {
        am.writeCookie('notepad_open', 0);
      } else {
        am.writeCookie('notepad_open', 1);
      }
    },
    /**
     * 记事本下一页
     * @param  {[type]} uri [description]
     * @param  {[type]} p   [description]
     * @return {[type]}     [description]
     */
    nextPage: function(uri) {
      $.get(uri, function(data) {
        if (data.status == 1) {
          $('#notepad-list').html(data.data);
        }
      });
    },
	icoClick:function(){
      this.notepad_dom.show();
      writeCookie('notepad_open', 1);
    }

  };
  notepad.init();
});
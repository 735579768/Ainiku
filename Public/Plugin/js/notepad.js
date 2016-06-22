$(function() {
  window.notepad = {
    init: function() {
      var notepad = $('#notepad_wrap');
      notepad.kldrag({
        titleheight: 30
      });
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
    }

  };
  notepad.init();
});
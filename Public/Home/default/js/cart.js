//购物车js

$(function() {
	window.cartobj = {
		//初始化提交地址
		url: {
			updategoodsnum: '',
			addcartgoods: '',
			delcartgoods: '',
			setcheck:''
		},
		//全部选中或取消
		init: function() {
			$('#all-selected').click(function(event) {
				var _this = $(this);
				var _li = $('.check-item');
				var ha = _this.hasClass('icon-check-selected');
				if (ha) {
					_this.removeClass('icon-check-selected');
					_li.removeClass('icon-check-selected');
					
				} else {
					_this.addClass('icon-check-selected');
					_li.addClass('icon-check-selected');
				}
				var strid=cartobj.setCartidStr();
				cartobj.updateCheck(strid,1);
				
			});
			$('.check-item').click(function(event) {
				var _this = $(this);
				var ha = _this.hasClass('icon-check-selected');
				if(ha){
				_this.removeClass('icon-check-selected') ;
				cartobj.updateCheck(_this.attr('data-id'),0);
				}else{
				_this.addClass('icon-check-selected');
				cartobj.updateCheck(_this.attr('data-id'),1);
				}
				cartobj.setCartidStr();
			});
			//数量改变
			$('.input-num').bind('change', function(event) {
				var _this = $(this);
				var t = _this.val();
				if (t < 1) _this.val(1);
			});
			//初始化总价
			cartobj.updateTotalPrice();
		},
		jia: function(obj) {
			var _this = $(obj);
			var v = parseInt(_this.prev().val());
			_this.prev().val(v + 1);
			//更新小计
			var v = parseInt(_this.prev().val());
			var pa = _this.parents('.col');
			var pr = parseFloat(pa.prev().html());
			pa.next().html((v * pr).toFixed(1) + '元');
			//更新总价
			cartobj.updateTotalPrice();
			//更新数量
			var cartid = _this.parents('.list-body').find('.icon-check').attr('data-id');
			cartobj.updateGoodsNum(cartid, v);
		},
		jian: function(obj) {
			var _this = $(obj);
			var v = parseInt(_this.next().val());
			if ((v - 1) > 0) {
				_this.next().val(parseInt(v - 1));
			} else {
				_this.next().val(1);
			}
			//更新小计
			var v = parseInt(_this.next().val());
			var pa = _this.parents('.col');
			var pr = parseFloat(pa.prev().html());
			pa.next().html((v * pr).toFixed(1) + '元');
			//更新总价
			cartobj.updateTotalPrice();
			//更新数量
			var cartid = _this.parents('.list-body').find('.icon-check').attr('data-id');
			cartobj.updateGoodsNum(cartid, v);
		},
		//更新产品数量
		updateGoodsNum: function(cartid, numb) {
			var _this = this;
			$.ajax({
				type: 'POST',
				url: _this.url.updategoodsnum,
				data: {
					cart_id: cartid,
					num: numb
				},
				success: function(da) {
					if (da.status == 0) {
						//ank.msg(da.info);
					}
				}
			});
		},
		//更新购物车总价
		updateTotalPrice: function() {
			var t = 0;
			$('.list-body .col-total').each(function(index) {
				t += parseFloat($(this).html());
			});
			$('#totalprice').html(t);
		},
		//删除购物车产品
		delCartGoods: function(cartid) {
			var _this = this;
			$.ajax({
				type: 'POST',
				url: _this.url.delcartgoods,
				data: {
					cart_id: cartid
				},
				success: function(da) {
					if (da.status == 0) {
						ank.msg(da.info);
					} else {
						$('.list-body .icon-check[data-id=' + cartid + ']').parents('.list-body').remove();
					}
				}
			});
		},
		//更新选中的产品id
		setCartidStr: function() {
			
			var strid = '';
			$('.check-item.icon-check-selected').each(function(index, element) {
				var str = $(this).attr('data-id');
				(str != '') && ((strid === '') ? strid = str : (strid += ',' + str));
			});
			$('#cartidstr').val(strid);
			return strid;
		},
		//更新购物车中产品选中状态
		updateCheck:function(cartid,sel){
			var _this=this;
			$.ajax({
				url: _this.url.setcheck,
				type: 'POST',
				data: {cart_id:cartid,selected:sel},
				success:function(da){
					if(da.status==0){
						ank.msg(da.info);
					}
				}
			});
		},
		//添加产品到购物车
		addToCart: function() {
			var _this = this;
			var arg = arguments;
			if (!(arg[0] && arg[1])) {
				ank.msg('参数不能为空!');
				return false;
			}
			var goodsid = arg[0];
			var numb = arg[1];
			var callback = arg[2];
			$.ajax({
				type: 'POST',
				url: _this.url,
				addcartgoods,
				data: {
					goods_id: goodsid,
					num: numb
				},
				success: function(da) {
					if (da.status == 1) {
						ank.msg('添加成功');
					} else {
						ank.msg('添加失败');
					}
					da.goods_id = goodsid;
					da.goods_num = numb;
					(typeof(callback) === 'function') && callback(da);
				}
			});
		},
		//绑定删除购物车产品按钮
		delListCartGoods: function(obj) {
			var _this = $(obj);
			ank.msgDialog({
				title: '删除提醒',
				content: '确定要删除吗?',
				btn: true,
				ok: function() {
					var cartid = _this.parents('.list-body').find('.icon-check').attr('data-id');
					cartobj.delCartGoods(cartid);
					return false;
				},
				cancel: function() {

				}
			});


		}
	};
});
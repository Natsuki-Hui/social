(function($,App){
    // 在里面定义App对象的方法（操作：登录、注册、发布动态、关注、点赞等）
    App.register = function(){
        var formdata = new FormData($('#reg')[0]);
        if ($.trim(formdata.get('ruser')).length==0) {
            $.toast('请输入昵称');
            return;
        }
        if (!checkEmail(formdata.get('remail'))) {
            $.toast('请输入正确的邮箱');
            return;
        }
        if (!checkPwd(formdata.get('rpwd'))) {
            $.toast('请输入6至12位密码');
            return;
        }
        if (!checkPhone(formdata.get('rphone'))) {
            $.toast('请输入正确的手机号码');
            return;
        }
        $.ajax({
            url:'http://wd1900115.pro.wdcase.com/social/api/user_api.php',
            // url:'http://localhost/social/api/user_api.php',
            type:'post',
            data : formdata,
            dataType:'json',
            contentType: false,
            processData: false,
            timeout: 15000,	//最长请求时间：15秒
            beforeSend: function () {
                $.showIndicator();
            },
            success: function(res) {
                $.toast(res.msg);
                if(res.error==0){
                    $.router.load('#login');
                }
            },
            error: function () {
                // 超时、或者请求失败执行的结果
            },
            complete: function () {
                $.hideIndicator();
            }
        })
    },
    // 用户登录
    App.login = function(){
        var formdata = new FormData($('#denglu')[0]);
        if (checkRequired(formdata.get('lphone'))) {
            $.toast('请输入手机号码');
            return;
        }
        if (checkRequired(formdata.get('lpwd'))) {
            $.toast('请输入密码');
            return;
        }
        $.ajax({
            url:'http://wd1900115.pro.wdcase.com/social/api/user_api.php',
            // url:'http://localhost/social/api/user_api.php',
            type:'post',
            data : formdata,
            dataType:'json',
            contentType: false,
            processData: false,
            timeout: 15000,	//最长请求时间：15秒
            beforeSend: function () {
                $.showIndicator();
            },
            success: function(res) {
                $.toast(res.msg);
                if(res.error==0){
                    var info = res.data;
                    $('.phone input').val("");
                    $('.pwd input').val("");
                    $.router.load('#index');
                    localStorage.setItem('name',info.nickname);
                    localStorage.setItem('photo',info.photo);
                    localStorage.setItem('uid',info.uid);
                    localStorage.setItem('sex',info.sex);
                    localStorage.setItem('email',info.email);
                    localStorage.setItem('txt',info.descs);
                    location.reload();
                }
            },
            error: function () {
                // 超时、或者请求失败执行的结果
            },
            complete: function () {
                $.hideIndicator();
            }
        })
    },
    // 发布动态
    App.fabu = function(){
        var uid = localStorage.getItem('uid');
        var formdata = new FormData($('#pub')[0]);    
        if (checkRequired(formdata.get('info'))) {
            $.toast('请输入文字');
            return;
        }
        if (checkRequired(formdata.get('pos'))) {
            $.toast('请选择位置');
            return;
        }
        // var c = $('.pags span').filter('pag_on').length;
        var c = 0;
		var tag = '';
		$('.pags a').each(function(){
			if($(this).hasClass('pag_on')){  // 判断span是否有active类名
				c++;
				tag += $(this).html()+',';
			}
		});
		if(c==0){
			$.toast('请至少选择一个标签');
			return;	// 阻止程序运行
		}
        // if(c==0){
        //     $.toast('请至少选择一个');
        //     return;  
        // }
        formdata.append('tag',tag);
        formdata.append('uid',uid);
        $.ajax({
            url: 'http://wd1900115.pro.wdcase.com/social/api/dynamic_api.php',
			// url: 'http://localhost/social/api/dynamic_api.php',
			type: 'post',
			data: formdata,
			contentType: false,
        	processData: false,
        	timeout:15000,	//最长请求时间：15秒
        	dataType: 'json',
        	beforeSend: function(){
        		$.showIndicator();
        	},
			success: function(res){
                $.toast(res.msg);
                if(res.error==0){
                    $('#pub')[0].reset();
                    $('.pags a').removeClass('pag_on');
                    $('.imgAll').html("");
                    $.router.load('#index');
                }
			},
			error: function(){
				// 超时、或者请求失败执行的结果
			},
			complete: function(){	
                $.hideIndicator();
			}
		});
    },
    // 用户信息修改
    App.modify = function(){
        var formdata = new FormData($('#modify')[0]);
        var uid = localStorage.getItem('uid');
        var src = $('.top-img a img').attr('src');
        formdata.append('src',src);
        formdata.append('uid',uid);
        $.ajax({
             url:'http://wd1900115.pro.wdcase.com/social/api/user_api.php',
            // url:'http://localhost/social/api/user_api.php',
            type: 'post',
            data: formdata,
            contentType: false,
            processData: false,
            timeout:15000,  //最长请求时间：15秒
            dataType: 'json',
            beforeSend: function(){
                $.showIndicator();
            },
            success: function(res){
                $.toast(res.msg);
                if(res.error==0){
                    var info = res.data;
                    console.log(info);
                    localStorage.setItem('name',info.nickname);
                    localStorage.setItem('photo',info.photo);
                    localStorage.setItem('uid',info.uid);
                    localStorage.setItem('sex',info.sex);
                    localStorage.setItem('email',info.email);
                    localStorage.setItem('txt',info.descs);
                    $('.user-photo').attr('src',info.photo.length != 0?info.photo:'./images/tx.jpg');
                    $('.user-name').html(info.nickname);
                    $.router.load('#user');    
                    $('.name-input').val(info.nickname);
                    $('.sex-input').val(info.sex);
                    $('.email-input').val(info.email);
                    $('.txt-input').val(info.descs);
                    $('.top-img a img').attr('src',info.photo.length != 0?info.photo : './images/tx.jpg'); 
                }
            },
            error: function(){
                // 超时、或者请求失败执行的结果
            },
            complete: function(){   
                $.hideIndicator();
            }
        });
    }
    // 用户点赞
    App.zan = function(){
        
    },
    // 用户关注
    App.focus = function(obj){
        var _this = obj;  
        var uid = localStorage.getItem('uid');
        if (uid>0){
            var touid = $(_this).attr('touid');
            $.ajax({
                url: 'http://wd1900115.pro.wdcase.com/social/api/user_api.php',
                // url: 'http://localhost/social/api/user_api.php',
                type: 'post',
                data: 'act=follow&uid='+uid+'&touid='+touid,
                dataType: 'json',
                    beforeSend: function(){
                        $.showIndicator();
                    },
                    success: function(res){
                        if($(_this).attr('status') == 0){
                            $('.touid'+res.data).html("已关注");
                            $('.touid'+res.data).addClass('follow_on');
                            $('.touid'+res.data).attr('status',1);
                        }else{
                            $('.touid'+res.data).html("+关注");
                            $('.touid'+res.data).removeClass('follow_on');
                            $('.touid'+res.data).attr('status',0);
                        }
                    },
                    error: function(){
                        // 超时、或者请求失败执行的结果
                    },
                    complete: function(){   
                        $.hideIndicator();
                    }
                }); 
           }else{
                $.confirm('您还未登录，请先登录',function(){

                $.router.load('#login');
                },function(){});
            } 
    }
})(Zepto,window.app={});//给window自定义一个对象APP

;(function($){
    $(".regsub").bind("click",function(){
        app.register();
    });
    $(".btn_bg").bind("click",function(){
        app.login();
    });
    $(".sure-btn").bind("click",function(){
        app.fabu();
    });
    $(".alter_bg").bind("click",function(){
        app.modify();
    });

    $(document).on("click",".follow",function(){
        app.focus(this);
    });
    
})(jQuery);
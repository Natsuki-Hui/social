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
            url:'http://localhost/social/api/user_api.php',
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
            url:'http://localhost/social/api/user_api.php',
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
                    $.router.load('#index');
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
        var tag = "";
        $('.pags span').each(function(){
            if($(this).isClass('pag_on')){
                c++;
                tag += $(this).html() + ",";
            }
        });
        // if(c==0){
        //     $.toast('请至少选择一个');
        //     return;  
        // }
        formdata.append('tag',tag);
        $.ajax({
			url: 'http://localhost/social/api/dynamic_api.php',
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
                    $('.pags span').removeClass('pag_on');
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
    // 用户点赞
    App.zan = function(){
        
    },
    // 用户关注
    App.focus = function(){
        
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
})(jQuery);
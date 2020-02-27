; (function ($, App) {
    // alert(1111)
    App.register = function () {
        var formdata = new FormData($('#reg')[0])
        //    var ruser=formdata.get('ruser')
        if ($.trim(formdata.get('ruser')).length == 0) {
            $.toast('请输入昵称')
            return
        }
        if (!checkEmail(formdata.get('remail'))) {
            $.toast('请输入邮箱')
            return
        }
        if (!checkPwd(formdata.get('rpwd'))) {
            $.toast('请输入正确的密码')
            return
        }
        if (!checkPhone(formdata.get('rphone'))) {
            $.toast('请输入正确的手机号')
            return
        }
        $.ajax({
            url:'http://wd1900113.pro.wdcase.com/social2/api/user_api.php',
            type: 'post',
            dataType: 'json',  // json字符串 => json对象
            data: formdata,// 传递数据
            contentType: false,
            processData: false,
            timeout:15000,	//最长请求时间：15秒
            beforeSend: function(){
                $.showIndicator();
            },
            success: function(res){
                // console.log(res)
                $.toast(res.msg)
                if(res.error==0){
                    setTimeout(function(){
                        $.router.load('#login')
                    },2000)
                }
            },
            error: function(){
            },
            complete: function(){
                setTimeout(function(){
                    $.hideIndicator()
                },1000)
            }
        });
    }
    App.login = function () {
        var formdata=new FormData($('#login_form')[0])

        if(!checkPhone(formdata.get('phone'))){
            $.toast('请正确输入手机号')
            return
        }
        if(!checkPwd(formdata.get('pwd'))){
            $.toast('请输入正确的密码')
            return
        }
        if(checkRequired(formdata.get('verify'))){
            $.toast('请输入验证码')
            return
        }
        
        $.ajax({
            url:'http://wd1900113.pro.wdcase.com/social2/api/user_api.php',
            type:'post',
            data:formdata,
            dataType:'json',
            contentType:false,
            processData:false,
            beforeSend:function(){
                $.showIndicator();
            },
            success:function(res){
                $.toast(res.msg)
                if(res.error==0){
                    var info = res.data;
                    localStorage.setItem('name',info.nickname);
                    localStorage.setItem('photo',info.photo);
                    localStorage.setItem('uid',info.uid);
                    $('#login_form')[0].reset()
                    // $('.verify-img').click()
                    setTimeout(function(){
                        $.router.load('#index')
                    },2000)
                }
            },
            error:function(){},
            complete:function(){
                setTimeout(function(){
                    $.hideIndicator()
                },1000)
            }
        })
    }
    //发布
    App.fabu=function(){
       var formdata=new FormData($('#pub_form')[0])
       // formdata.get('desc')
       // formdata.get('pos')
       var str=''
       $('.tag span').each(function(){
            if($(this).hasClass('active')){
                str+=$(this).html()+','
            }
       })
       str=str.substr(0,str.length-1)
       formdata.append('tag',str)
       // console.log(formdata.get('tag'))
       
       if(checkRequired(formdata.get('desc'))){
            $.toast("请输入文字")
            return
       }
       if(checkRequired(formdata.get('pos'))){
            $.toast("必须添加位置")
            return
       }
       if(formdata.get('tag')==''){
           $.toast("必须选择一个标签")
            return 
       }
        formdata.append('uid',localStorage.getItem('uid'));
       $.ajax({
        url:'http://wd1900113.pro.wdcase.com/social2/api/dynamic_api.php',
        type:'post',
        data:formdata,
        dataType:'json',
        contentType: false,
        processData: false,
        beforeSend:function(){
            $.showIndicator()
        },
        success:function(res){
            if(res.error==0){
                $.toast(res.msg)
                $('#pub_form')[0].reset()
                $('.tag span').removeClass('active')
                setTimeout(function(){
                    $.router.load("#index")
                },1500)
            }
        },
        error:function(){},
        complete:function(){
            setTimeout(function(){
                $.hideIndicator()
            },1000)
        }
       })
    }
    //用户关注
    App.focus=function(obj){

            var uid=localStorage.getItem('uid')
            var _this=obj;
            if(uid>0){

                if ($(obj).html() == '已关注') {
                    $(obj).html('+关注').removeClass('hui')
                } else {
                    $(obj).html('已关注').addClass('hui')
                }

                //已登录
                var touid=$(obj).attr('uid')
                $.ajax({
                    url:'http://wd1900113.pro.wdcase.com/social2/api/dynamic_api.php',
                    type: 'post',
                    dataType: 'json',  // json字符串 => json对象
                    data: 'act=notice&uid='+uid+'&touid='+touid,// 传递数据
                    beforeSend:function(){
                        $.showIndicator();
                    },
                    success: function(res){
                        console.log(res.data)
                        $(_this).attr('status',res.data>0?1:0);
                        //改变点赞的状态，数量
                        // $(_this).find('span').html(res.data)
                        // $(_this).toggleClass('fc')
                    },
                    error: function(){
                    },
                    complete: function(){
                        setTimeout(function(){
                            $.hideIndicator();  // 迷你指示器
                        },1000);
                    }
                    
                });

            }else{
                // (1)没有登录
                $.confirm('您未登录,请登录后操作?',
                    function () {
                        // 打开登录页面
                        $.router.load('#login');
                    },
                    function () {
                        // 不操作
                    }
                );
            }
        
    }
    //点赞
    App.zan=function(obj){
        var _this=obj;
        var uid=localStorage.getItem('uid')
        if(uid>0){
            //已登录
            var id=$(obj).attr('did')
            $.ajax({
            url:'http://wd1900113.pro.wdcase.com/social2/api/dynamic_api.php',
            type: 'post',
            dataType: 'json',  // json字符串 => json对象
            data: 'act=zan&uid='+uid+'&did='+id,// 传递数据
            beforeSend:function(){
                $.showIndicator();
            },
            success: function(res){
                //改变点赞的状态，数量
                $(_this).find('span').html(res.data)
                $(_this).toggleClass('fc')
            },
            error: function(){
            },
            complete: function(){
                setTimeout(function(){
                    $.hideIndicator();  // 迷你指示器
                },1000);
            }
            
        });

        }else{
            // (1)没有登录
            $.confirm('您未登录,请登录后操作?',
                function () {
                    // 打开登录页面
                    $.router.load('#login');
                },
                function () {
                    // 不操作
                }
            );
        }
    }
    
    

})(Zepto, window.app = {})

    // App.register()

    ; (function ($) {

       
        $('.regsub').click(function(){
            app.register()
        })

        $('.login').click(function(){
            app.login()
        })

        $('.conpub').click(function(){
            app.fabu()
        })
        $(document).on('click','.facebook-card .notice',function(){
            app.focus(this)
        })     
        $(document).on('click','.zan',function(){
            app.zan(this)
        })


    })(jQuery)
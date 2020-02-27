; (function ($) {
    

    $("#city-picker").cityPicker({
        toolbarTemplate: '<header class="bar bar-nav">\
<button class="button button-link pull-right close-picker">确定</button>\
<h1 class="title">选择地址</h1>\
</header>'
    });
    $('.textarea-wrap textarea').on('keyup', function () {
        var $len = $(this).val().length
        if ($len > 500) {
            var str = $(this).val().substr(0, 500)
            $(this).val(str)
            $.toast("字数不能超过500");
        } else {
            $('.title-wrap span i').html($len)
        }
    })


    $(document).on('click','.imgFileUploade .header span.imgClick',function(){
        $('.imgFiles').click();//没有加的话，要双击+才能上传

    });

    $(document).on('change','.imgFiles',function(){//给file元素添加改变事件
       $.showIndicator();  // 迷你指示器
    });


    var imgFile = new ImgUploadeFiles('.show_img',function(e){
        this.init({
                    MAX : 3, //限制个数
                    MH : 5800, //像素限制高度
                    MW : 5900, //像素限制宽度
                    callback : function(arr){
                        $.hideIndicator();  // 迷你指示器
                        // 回调
                        // console.log(arr);
                        // console.log(arr[0].src)      // base64文本（其实就是图片的内容）
                        var id = arr[0].delId - 1;
                        var src = arr[0].src;
                        var textarea = document.createElement('textarea');
                        textarea.setAttribute('name','pic'+id);
                        textarea.className = 'picimg';
                        textarea.value = src;
                        document.getElementById('pub_form').append(textarea);
                    }
                });
    });



})(Zepto)

; (function ($) {
    //上传图片
    // var $tgaUpload = $('#goodsUpload').diyUpload({
    //     url: '/uploadFilePath',
    //     success: function (data) { },
    //     error: function (err) { },
    //     buttonText: '',
    //     accept: {
    //         title: "Images",
    //         extensions: 'gif,jpg,jpeg,bmp,png'
    //     },
    //     thumb: {
    //         width: 120,
    //         height: 90,
    //         quality: 100,
    //         allowMagnify: true,
    //         crop: true,
    //         type: "image/jpeg"
    //     }
    // });

    $('.pub_content .content-tag span').bind('click',function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active')
        }else{
            $(this).addClass('active')
        }
    })

})(jQuery)
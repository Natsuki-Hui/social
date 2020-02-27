; (function ($) {
    $(document).on('click', '.create-actions', function () {
        var buttons1 = [
            // {
            // 	text: '请选择',
            // 	label: true
            // },
            {
                text: '分享到QQ',
                bold: true,
                color: 'danger',
                onClick: function () {
                    $.alert("你选择了“分享到QQ“");
                }
            },
            {
                text: '分享到微信',
                bold: true,
                color: 'danger',
                onClick: function () {
                    $.alert("你选择了“分享到微信“");
                }
            },
            {
                text: '分享到朋友圈',
                bold: true,
                color: 'danger',
                onClick: function () {
                    $.alert("你选择了“分享到朋友圈“");
                }
            }

        ];
        var buttons2 = [
            {
                text: '取消',
                bg: 'danger'
            }
        ];
        var groups = [buttons1, buttons2];
        $.actions(groups);
    });

})(Zepto)

    ; (function ($) {
        

        
    })(Zepto)
    ;(function($){
        $('.camera').bind('click',function(){
            $('.selfile').click()
        })
    })(jQuery)


    
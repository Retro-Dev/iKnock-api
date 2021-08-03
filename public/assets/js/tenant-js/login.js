
    $(document).ready(function(){
        $(function() {
            $('#custom-input1')
                .focus(function() {
                    $('.icon1').css('display','none');

                })
        });

        $(function() {
            $('#custom-input1')
                .focusout(function() {
                    $('.icon1').css('display','block');

                })
        });

        $(function() {
            $('#custom-input2')
                .focus(function() {
                    $('.icon2').css('display','none');
                })

        });

        $(function() {
            $('#custom-input2')
                .focusout(function() {
                    $('.icon2').css('display','block');
                })

        });
    });

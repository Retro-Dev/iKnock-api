
$(document).ready(function(){
    $('.add-link').click(function(){
        var url = $(this).data('href')
        window.location.href = url;
    })
});

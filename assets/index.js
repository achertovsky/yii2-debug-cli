$('.get-time-spent').on('click', function (e) {
    e.preventDefault();
    var tag = $(this).data('tag');
    $.ajax(
        {
            'url': tagInfoUrl+'?tag='+tag,
            'success': function (data) {
                var time = Math.round(data.time*1000);
                $('a[data-tag='+tag+']').text(time);
            }
        }
    );
});
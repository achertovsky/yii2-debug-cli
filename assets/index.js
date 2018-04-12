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

function getAvgTime()
{
    var stats = $('.get-time-spent');
    var total = 0;
    var sum = 0;
    $.each(stats, function (key, value) {
        var val = $(value).text();
        if (val == 'n/a') {
            return true;
        }
            total++;
            sum += +val;
    });
    console.log(sum/total);
}
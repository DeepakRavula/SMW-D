$(function() {
    var failCallback = function() {
        alert('Permission update was not successful');
    };
    ['add', 'remove'].forEach(function(action) {
        $(document).on('click', '.'+action+'-permission', function() {
            var role = $(this).data('role');
            var permission = $(this).data('permission');
            $.post('permission/'+action+'?role='+role+'&permission='+permission, function(data) {
                if(data.success) {
                    $.pjax.reload({container: '#permission-table-pjax', timeout : 6000});
                }
                else {
                    failCallback();
                }
            }).fail(failCallback);
        });
    });

});
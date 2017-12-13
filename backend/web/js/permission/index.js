$(function() {
    var failCallback = function() {
        alert('Permission update was not successful');
    };
    ['add', 'remove'].forEach(function(action) {
        $(document).on('click', '.'+action+'-permission', function() {
            var roleId = $(this).data('role');
            var permissionId = $(this).data('permission');
            $.post('permission/'+action+'?roleId='+roleId+'&permissionId='+permissionId, function(data) {
                if(data.success) {
                    $.pjax.reload({container: '#permission-table-pjax'});
                }
                else {
                    failCallback();
                }
            }).fail(failCallback);
        });
    });

});
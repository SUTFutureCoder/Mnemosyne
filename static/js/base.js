function loadUserinfo(){
    $.ajax({
        type: 'POST',
        url: '{{php}}echo base_url(){{/php}}api/account/account/login',
        data: loginPostData,
        dataType: 'json',
        success: function(data){
            if (data['status'] == 0){
                store.set('token', data['data']['token']);
                window.location.href='{{php}}echo base_url();{{/php}}user/completeinfo';
            } else {
                alert(data['description']);
            }
        },
        error: function(data){
            console.log(data);
            alert('操作失败');
        }
    });
}

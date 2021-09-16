$(document).ready(function(){
    save_dispute();
});

function save_dispute()
{
    $("#buyer_dispute").submit(function(e){
        e.preventDefault();

        var data = $(this)[0];
        var formData = new FormData(data);
        formData.append('tr_id',trans_id);
        formData.append('role',dispute_role);

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type : 'POST',
            cache : false,
            processData : false,
            contentType : false,
            url : url,
            dataType : 'json',
            data : formData,
            beforeSend: function()
            {
               $('#loader').show();
               $('.div-loading').addClass('background-load');
               $(".error").hide();
            },
            success : function(result)
            {
                if(result.err == 0)
                {
                    location.href=page_success;
                }
                else
                {
                    $('#loader').hide();
                    $('.div-loading').removeClass('background-load');

                    // $(".error").show();
                    $("#err_message").html('<div class="alert alert-danger>'+err_message+'</div>');
                }
            },
            error : function()
            {
                $('#loader').hide();
                $('.div-loading').removeClass('background-load');
            }
        });
    });
}
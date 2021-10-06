function search_seller()
{
    $("#btn-src").click(function(){
        var src = $("#search").val();
        var sort = $("select[name='sort']").val();
        var range = $("select[name='range']").val();
        display_buy_list(src,sort,range)
    });
}

function display_buy_list(src,sort,range)
{
   $.ajax({
      // headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
      type : 'GET',
      url : url_global,
      dataType : 'html',
      data : {'src' : src,'sort':sort,'range':range},
      beforeSend: function()
      {
         $('#loader').show();
         $('.div-loading').addClass('background-load');
         $(".error").hide();
      },
      success : function(result)
      {
          $("#seller_list").html(result);                    
      },
      complete : function()
      {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
      },
      error : function()
      {
          $('#loader').hide();
          $('.div-loading').removeClass('background-load');
      }
  });
}

//ajax pagination
function pagination()
{
    $(".page-item").removeClass('active').removeAttr('aria-current');
    var mulr = window.location.href;
    getActiveButtonByUrl(mulr)
  
    $('body').on('click', '.pagination .page-link', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        var src = $("#search").val();
        var sort = $("select[name='sort']").val();
        var range = $("select[name='range']").val();
        // console.log(url);
        loadPagination(url,src,sort);
    });
}

function loadPagination(url,src,sort,range) {
    $.ajax({
      beforeSend: function()
        {
          $('#loader').show();
          $('.div-loading').addClass('background-load');
        },
      url: url,
      data: {'src':src,'sort':sort,'range':range},
      dataType : 'html',
    }).done(function (data) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        getActiveButtonByUrl(url);
        $('#seller_list').html(data);
    }).fail(function (xhr,attr,throwable) {
        $('#loader').hide();
        $('.div-loading').removeClass('background-load');
        $("#buy_content").html('<div class="alert alert-danger">{{ Lang::get("custom.failed") }}</div>');
        console.log(xhr.responseText);
    });
}

function getActiveButtonByUrl(url)
{
  var page = url.split('?');
  if(page[1] !== undefined)
  {
    var pagevalue = page[1].split('=');
    $(".page-link").each(function(){
       var text = $(this).text();
       if(text == pagevalue[1])
        {
          $(this).attr('href',url);
          $(this).addClass('on');
        } else {
          $(this).removeClass('on');
        }
    });
  }
  else {
      var mod_url = url+'?page=1';
      getActiveButtonByUrl(mod_url);
  }
}

//end ajax pagination
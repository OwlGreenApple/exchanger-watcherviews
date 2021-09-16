function delay(callback, ms) {
    var timer = 0;
    return function() {
      var context = this, args = arguments;
      clearTimeout(timer);
      timer = setTimeout(function () {
        callback.apply(context, args);
      }, ms || 0);
    };
  }

function return_number(num)
{
   num = num.toString().replace(/\./g,'');
   num = parseInt(num);
   return num;
}

function formatNumber(num) 
{
    num = num.toString().replace(/\./g,'');
    num = parseInt(num);
    var result;
    if(isNaN(num) == true)
    {
       return '';
    }
    else
    {
       result = num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
       return result;
    }
}
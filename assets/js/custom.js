$(document).ready(function() {
	agreement();
	fixLayoutInputPhoneCountry();
	getDataFromCountry();
	countryChange();
});

function getDataFromCountry()
{
	 var data_country = $(".iti__selected-flag").attr('data-country');
	 var code_country = $(".iti__selected-flag").attr('data-code');
	 $("input[name='data_country']").val(data_country);
	 $("input[name='code_country']").val(code_country);
}

function countryChange()
{
 	jQuery("#phone").on('countrychange', function(e, countryData){
    	getDataFromCountry();
	});
} 

function fixLayoutInputPhoneCountry()
{
  	$(".iti").addClass('w-100');
}

function agreement(){
    $("input[name=agreement]").click(function(){
      var val = $(this).val();

      if(val == 1){
        $(this).val('on');
      }
      else {
        $(this).val(1);
      }

    });
  }
$(function()
{
	getDataFromCountry();
	countryChange();
	fixLayoutInputPhoneCountry();
});

function getDataFromCountry()
{
	 var data_country = $(".iti__selected-flag").attr('data-country');
	 $("input[name='data_country']").val(data_country);
}

function countryChange()
{
	jQuery("#phone").on('countrychange', function(e, countryData){
	    var data_country = $(".iti__selected-flag").attr('data-country');
	    $("input[name='data_country']").val(data_country);
	});
} 

function fixLayoutInputPhoneCountry()
{
  	$(".iti").addClass('w-100');
}

// Store table
$(document).ready(function(){
  $("#items-spec").hide();

  $(".submit").on('click',function(e){
    var tList = [];
    
    $("#item_table > tbody > tr").each(function (index,element) {
        var i = $(this).index();
        var tObject = {};
        tObject['Code'] = $(element).find(".item-unit option:selected").text();
        tObject['Lot_No'] = $(element).find(".lot_no").val();
        tObject['Usage'] = $(element).find(".usage").val();
        tObject['Order_usage'] = $(element).find(".order_usage").val();            
        tObject['Actual_order_usage'] = $(element).find(".actual_order_usage").val(); 
        tList[i]=tObject;
        tObject = {};
        $("#fbi").val(JSON.stringify(tList));
    });
    
    var spList = [];
    $("#specific_table > tbody > tr").each(function (index,element) {
        var i = $(this).index();
        var spObject = {};
        spObject['Item'] = $(element).find(".item").val();
        spObject['Min'] = $(element).find(".min").val();
        spObject['Max'] = $(element).find(".max").val();
        spObject['Result'] = $(element).find(".result").val();
        spList[i]=spObject;
        spObject = {};
        $("#fbs").val(JSON.stringify(spList));
    });
  });
});


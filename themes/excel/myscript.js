
$('.export_home_report').click(function(event){
	event.preventDefault();
	exportExcel();
});


function randomDate(start, end) {
    var d= new Date(start.getTime() + Math.random() * (end.getTime() - start.getTime()));
    return d;
}

function exportExcel1(){

    var excel = $JExcel.new("Calibri light 10");            
    excel.set( {sheet:0,value:"Sheet 1" } );
    
    var table = document.getElementById('table');
    var limit = table.rows.length;

    var headers = [];

    for (var i = 0; i <= 8; i++) {
        headers.push(table.rows[0].cells[i].innerHTML);
    }

    
    var regions = ['1'];
	
	$('#regions a').each(function(){
	   regions.push($(this).html());
	});
	
	
    var formatHeader=excel.addStyle({
        border: "none,none,none,thin #333333",font: "Calibri 12 #000 B"}
    );                                                         

    for (var i = 0; i < headers.length; i++){           
        excel.set(0,i,0,headers[i],formatHeader);    
        excel.set(0,i,undefined,"auto");             
    }
	
	for (var i = 1; i < limit; i++){                                    
        for(var j = 0; j < (headers.length-1); j++){
        	
        	if(i==1)
            	excel.set(0,(j+1),i,table.rows[i].cells[j].innerHTML,formatHeader);
            else{
            	excel.set(0,j,i,parseInt(table.rows[i].cells[j].innerHTML));
            }
        
        }
    }

    for (var i = 2; i <= regions.length; i++){           
        excel.set(0,1,i,regions[(i-1)]);                 
        excel.set(0,8,i,parseInt(table.rows[i].cells[8].innerHTML));                 
    
    }

    excel.generate("Ҳисобот.xlsx");    
}

<?php
	use app\assets\AdminLteAsset;
	use yii\helpers\Html;
	use yii\helpers\Url;
	use yii\web\JqueryAsset;

	$this->title = Yii::t('app', 'Machine Counter');
	/** @var TYPE_NAME $zone_list */
	$col_class = ["bg-blue", "bg-yellow", "bg-green", "bg-red", "bg-aqua"];
	$col_class_active = ["bg-blue-active", "bg-yellow-active", "bg-green-active", "bg-red-active", "bg-aqua-active"];
?>

<div class="machine-counter-index">
	<div class="panel">
		<div class="panel-heading">
			<?=Html::a(
				'<span  class="glyphicon glyphicon-home"></span>',
				['/index'], ['class' => 'btn btn-primary btn-sm', 'title' => Yii::t('app', 'Main menu')]
			)
			?>
			<?=Html::a(
				'<span  class="glyphicon glyphicon-th-large"></span>',
				['counter'], ['class' => 'btn btn-primary btn-sm', 'title' => Yii::t('app', 'Machine Counter')]
			)
			?>
		</div>
		<div class="panel-body">
			<div class="row row-centered col-sm-12" id="main_content">
				<?
					$zone_cnt = count($zone_list);
					switch($zone_cnt){
						case 0: ?>
							<div class='alert alert-danger alert-dismissible text-center'>
								<h1><?=Yii::t('app', 'Active zone not found')?></h1>
							</div>
							<?
							break;
//                case 1:
//                  break;
						default:
							foreach($zone_list as $zone){
								?>
								<div class="btn col-lg-3 col-md-4 col-sm-6 col-xs-12 col-centered zone_machine"
									id="z<?=$zone['id']?>"
									zone_name="<?=$zone['linename']?>">
									<div class="small-box bg-light-blue-active">
										<div class="inner">
											<h4 class="text-bold"><?=$zone['linename']?></h4>
											<p><?=$zone['description']?></p>
										</div>
									</div>
								</div>
								<?
							}
							break;
					} ?>
			</div>
		</div>
	</div>
</div>



<?
	$this->registerCssFile("@themes/css/jquery-confirm.min.css", ['depends' => [AdminLteAsset::className()]]);
	$this->registerJsFile("@themes/js/jquery-confirm.min.js", ['depends' => [JqueryAsset::className()]]);
	$url_machine_list = Url::to(['machine/machine-list'], true);
	$url_machine_mold_list = Url::to(['machine/machine-mold-list'], true);
	$err_title = Yii::t('app', 'Error!!!');
	$add_item = <<< JS
$(document).ready(function() {   
    var is_setting = 0;
    function submit_count(){
        var inpt_machine_id = $('#cnt_input').attr('inpt_machine_id');
        var dif_cnt_txt = $("#dif_cnt_txt").text();
        $.ajax({
            url: "/production-order/create-production-orders",
            type: "post",
            data: {
                cnt: dif_cnt_txt,
                machine_id: inpt_machine_id,
            },
            beforeSend: function (){
               WaitDialog = $.dialog({
                    title: false,
                    cancelButton: true,
                    confirmButton: false,
                    backgroundDismiss: true,
                    backgroundDismissAnimation: 'glow',
                    closeIcon: false,
                    columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
                    content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
               });
            },
            complete: function (){ WaitDialog.close() },						               
            success: function(response) {
                if(response.sts=='OK'){
                    $.confirm({
                       // columnClass: 'col-lg-2 col-lg-offset-5 col-md-2 col-md-offset-5 col-sm-6 col-sm-offset-3',
                       draggable: false,
                       columnClass: 'col-xs-12',
                       icon: 'fa fa-smile-o jconfirm-done',
                       title:"",
                       theme: 'supervan',
                       // content: false, // hides content block.
                       content: "<div class='text-dark col-xs-12 text-center' style='font-size:150%'>" + response.sms + "</div>",
                       buttons: {                            
                            close: { 
                                text: '✓',
                                btnClass: 'jconfirm-button-done',
                                keys: ['enter', 'shift'],
                                action: function(){
                                    window.location.replace("/machine/counter");
                                }                               
                            }
                       }    
                    });
                }else{
                    $.alert({
                       keyboardEnabled: true,
                       draggable: true,
                       columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                       icon: 'fa fa-warning',
                       title: "<span class='text-bold'>$err_title</span>",
                       content: "<div class='text-danger'>" + response.sms + "</div>",
                });
                }
                $('#cnt_input').val('');
                $('#dif_cnt_txt').html('-');
                $('#cnt_input').focus();
            },
            error: function(xhr) {
                $.alert({
                       keyboardEnabled: true,
                       draggable: true,
                       columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                       icon: 'fa fa-warning',
                       title: "<span class='text-bold'>$err_title</span>",
                       content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
                });
            }
        });   
    }
    
    function machine_setting(machine_id,mold_id){
        $.ajax({
            url: "/machine/machine-setting",
            type: "post",
            data: {
                machine_id: machine_id,
                mold_id: mold_id,
            },
            beforeSend: function (){
               WaitDialog = $.dialog({
                    title: false,
                    cancelButton: true,
                    confirmButton: false,
                    backgroundDismiss: true,
                    backgroundDismissAnimation: 'glow',
                    closeIcon: false,
                    columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
                    content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
               });
            },
            complete: function (){ WaitDialog.close() },						               
            success: function(response) {
                if(response.sts=='OK'){
                    $.confirm({
                       // columnClass: 'col-lg-2 col-lg-offset-5 col-md-2 col-md-offset-5 col-sm-6 col-sm-offset-3',
                       draggable: false,
                       columnClass: 'col-xs-12',
                       icon: 'fa fa-smile-o jconfirm-done',
                       title:"",
                       theme: 'supervan',
                       // content: false, // hides content block.
                       content: "<div class='text-dark col-xs-12 text-center' style='font-size:150%'>" + response.sms + "</div>",
                       buttons: {                            
                            close: { 
                                text: '✓',
                                btnClass: 'jconfirm-button-done',
                                keys: ['enter', 'shift'],
                                action: function(){
                                    window.location.replace("/machine/counter");
                                }                               
                            }
                       }    
                    });
                }else{
                    $.alert({
                       keyboardEnabled: true,
                       draggable: true,
                       columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                       icon: 'fa fa-warning',
                       title: "<span class='text-bold'>$err_title</span>",
                       content: "<div class='text-danger'>" + response.sms + "</div>",
                });
                }
                $('#cnt_input').val('');
                $('#dif_cnt_txt').html('-');
                $('#cnt_input').focus();
            },
            error: function(xhr) {
                $.alert({
                       keyboardEnabled: true,
                       draggable: true,
                       columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                       icon: 'fa fa-warning',
                       title: "<span class='text-bold'>$err_title</span>",
                       content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
                });
            }
        });   
    }
    
	$("#main_content").on('click', '.m_setting', function (){
            is_setting = 1;
            var machine_id = $(this).parent().parent().parent().parent().attr('machine_id'); 
            var mold_id = $(this).parent().parent().parent().parent().attr('mold_id');
            $.ajax({
            url: "$url_machine_mold_list",
            type: "get",
            data: {
                machine_id: machine_id,
            },            
            beforeSend: function (){
               WaitDialog = $.dialog({
                    title: false,
                    cancelButton: true,
                    confirmButton: false,
                    backgroundDismiss: true,
                    backgroundDismissAnimation: 'glow',
                    closeIcon: false,
                    columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
                    content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
               });
            },
            complete: function (){ WaitDialog.close() },						               
            success: function(response){
                var temp_div = "";
                var res_cnt = response.cnt;
                if(res_cnt>0){ 
                    // DEVICEning MOLDlari ruyxati
                    let items = response.mold_data;                    
                    $.each( items, function( key, value ) {
                    	var bg_color = 'bg-inactive';
	                    var this_icon = 'fa-square-o';
	                    var is_active = 0;
                      if(value.mold_id == mold_id){
                        this_icon = 'fa-check-square-o';
                        bg_color = 'bg-green';
                        is_active = 1;
                      }
												temp_div += "<div is_active="+is_active+" machine_id="+machine_id+" mold_id="+value.mold_id+" class='sel_mold  btn col-lg-6 col-lg-offset-3 col-md-6 col-md-offset-3 col-sm-11'>"+
																		" <div class='grp_div "+bg_color+"'>"+
																		" 	<div class='col_div col_icon'>"+
																		" 	  <div class='m_10'>"+
																		"		    <i class='fa "+this_icon+"'></i>"+
																		" 	  </div>"+
																		" 	</div>"+
																		" 	<div class='pull-right col_div col_content'>"+
																		" 	  <div class='m_10'>"+
																		" 	    <h4>"+value.mold_no+"</h4>"+
																		" 	    <div style='height:0px;width:100%;outline:#fff solid thin;margin-bottom:20px;'></div>"+
																		" 		  <div class='text-left'>"+value.part_list+"</div>"+
																		" 	  </div>"+
																		" 	</div>"+
																		" </div>"+
																		"</div>";
                    });
                }else{
                    temp_div = "<div class='alert alert-danger alert-dismissible text-center'><h1>"+response.sms404+"</h1></div>";
                }                
                $("#main_content").html(temp_div);  
                $('#cnt_input').focus();
            },
            error: function(xhr) {
                $.alert({
                       keyboardEnabled: true,
                       draggable: true,
                       columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                       icon: 'fa fa-warning',
                       title: "<span class='text-bold'>$err_title</span>",
                       content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
                });
            }
        });                     
    });
	
    $(document).on('click', '.zone_machine', function(){       
	    if(is_setting){
	        is_setting = 0;
	        return;
	    }        
	    if($(this).attr('id').substr(0, 1)=='z'){
	        var zone_name = $(this).attr('zone_name');
	    }
	    if($(this).attr('id').substr(0, 1)=='m'){
	        var zone_name = $(this).attr('zone_name');
            var machine_id = $(this).attr('machine_id');
            var machine_no = $(this).attr('machine_no');
            var last_count = $(this).attr('last_count');
            var mold_id = $(this).attr('mold_id');
            var mold_no = $(this).attr('mold_no');
            var part_no = $(this).attr('part_no');
	    }	    
	    
        $.ajax({
            url: "$url_machine_list",
            type: "post",
            data: {
                this_id: $(this).attr('id'),
            },            
            beforeSend: function (){
               WaitDialog = $.dialog({
                    title: false,
                    cancelButton: true,
                    confirmButton: false,
                    backgroundDismiss: true,
                    backgroundDismissAnimation: 'glow',
                    closeIcon: false,
                    columnClass: 'col-xs-6 col-xs-offset-3 col-sm-4 col-sm-offset-4 col-md-2 col-md-offset-5',
                    content: '<img src="/img/loading.gif" style="width:100%;height:100%"/>',
               });
            },
            complete: function (){ WaitDialog.close() },						               
            success: function(response) {
                var temp_div = "";
                var res_cnt = response.cnt;
                if(res_cnt>0){ 
                    // Zonaning DEVICE lari ruyxati
                    if(response.m_type=='z'){
                        let items = response.machine;
                        $.each( items, function( key, value ) {
                          temp_div += "<div class='btn col-lg-3 col-md-4 col-sm-6 col-xs-12 col-centered zone_machine' id='m"+value.mold_id+
                           "' zone_name='"+zone_name+"' machine_id='"+value.machine_id+"' machine_no='"+value.machine_no+"' last_count='"+value.last_count+"' mold_id='"+value.mold_id+"' mold_no='"+value.mold_no+"' part_no='"+value.part_no+"'>"+                           
                                  "<div class='small-box "+value.sts+"'>"+
                                      "<div class='inner text-bold'>"+
                                          "<div>"+
                                              "<div class='pull-right btn btn-social-icon btn-google m_setting'>" +
                                               "<i class='fa fa-gear'></i>" +
                                               "</div>" +
                                              "<h3>"+value.machine_no+"</h3>"+
                                          "</div>"+
                                          "<div class='pull-right' style='font-size:140%'>"+value.last_count+"</div>"+
                                          "<div class='text-left'>"+value.part_no+"</div>"+
                                      "</div>"+
                                  "</div>"+
                              "</div>";
                        });
                    }
                        
                    // DEVICEning Counteri
                    if(response.m_type=='m'){                        
                        let items = response.part_data;
                        var pt_cnt = 0;                         
                          temp_div += "<div class='col-lg-6 col-md-8 col-sm-12 col-xs-12 col-centered' machine_id='"+machine_id+"'>"+
                                "<table class='part_table'>"+
                                "<tr> <th colspan=6 class='noborder text-center' style='font-size:150%;'>"+zone_name+"</th> <tr> " +
                                "<tr> <td colspan=2 class='noborder text-right'>DEVICE:</td> " +
                                 "<td class='noborder text-bold' colspan=4>"+machine_no+"</td>" +
                                "</tr>"+
                                "<tr> " +
                                 "<td colspan=2 class='noborder text-right'>MOLD/DIE:</td> " +
                                 "<td class='noborder text-bold' colspan=4>"+part_no+"("+mold_no+")</td>" +
                                "</tr>"+                                
                                
                                "<tr>" +
                                 "<td colspan=6 class='noborder text-center'>" +
                                      "<div class='input-group margin'>" +
                                        "<input type='number' id='cnt_input' inpt_machine_id='"+machine_id+"' inpt_last_count='"+last_count+"' class='form-control'>" +
                                        "<span class='input-group-btn'>" +
                                          "<button type='button' id='ok' class='btn btn-info btn-flat' style='height:100px;width:100px;'>" +
                                          "<i class='glyphicon glyphicon-menu-right' style='font-size:350%'></i>"+
                                           "</button>" +
                                        "</span>" +
                                      "</div>" +
                                 "</td>" +
                                "</tr>"+ 
                                "<tr>"+
                                  "<th>№</th>"+
                                  "<th>PART NO</th>"+
                                  "<td id='l_cnt_td'class='text-center'>" +
                                   "<div id='l_cnt_txt'>"+last_count+"</div>" +
                                   "<div id='l_cnt_lbl'>Last Count</div>" +
                                  "</td>"+
                                  "<td colspan='2' id='dif_cnt_td' class='text-center'>" +
                                  "<div id='dif_cnt_txt'>-</div>" +
                                  "<div id='dif_cnt_lbl'>Prod.count</div>" +
                                  "</td>"+
                                "</tr>";
                        $.each( items, function( key, value ) {                                                    
                          temp_div += "<tr class='pt_items'>"+
                                        "<td style='width:10px' class='text-center'>"+ ++pt_cnt +"</td>" +
                                        "<td style='width:40%'>"+value.part_no+"</td>" +
                                        "<td class='text-right'>"+value.part_qty +"</td>" +
                                        "<td colspan='2' class='text-right'>0</td>" +"</td>" +
                                      "</tr>";
                        });
                        temp_div += "</table>";
                    }                    
                }else{
                    temp_div = "<div class='alert alert-danger alert-dismissible text-center'><h1>"+response.sms404+"</h1></div>";
                }                
                $("#main_content").html(temp_div);  
                $('#cnt_input').focus();
            },
            error: function(xhr) {
                $.alert({
                   keyboardEnabled: true,
                   draggable: true,
                   columnClass: 'col-lg-6 col-lg-offset-3 col-md-8 col-md-offset-2 col-sm-10 col-sm-offset-1',
                   icon: 'fa fa-warning',
                   title: "<span class='text-bold'>$err_title</span>",
                   content: "<div class='text-danger'>" + xhr.statusText + '<br>' + xhr.responseText + "</div>",
                });
            }
        });
	    
	});
	
	$("#main_content").on('click', '.sel_mold', function (){		
		var is_active = $(this).attr('is_active');
		var machine_id = $(this).attr('machine_id');
		var mold_id= $(this).attr('mold_id');
		if(is_active==0){
		  machine_setting(machine_id,mold_id);
		}
	});
	
	$("#main_content").on('click', '#ok', function (){
		var this_val = $('#cnt_input').val();
		var last_cnt_val = $('#l_cnt_txt').text();
		var prod_cnt = this_val-last_cnt_val;		    
		if(prod_cnt>0){
		  submit_count();
		}
	});
	
	
	$("#main_content").on('change keyup', '#cnt_input', function (event){
		var this_val = $(this).val();
    var last_cnt_val = $('#l_cnt_txt').text();
    var prod_cnt = this_val-last_cnt_val;		    
    $('#dif_cnt_txt').html(prod_cnt);
    $('.part_table .pt_items').each(function() {
            var td2 = $(this).find('td:eq(2)').text();
            let total = td2 * $("#dif_cnt_txt").html();
            $(this).find('td:eq(3)').text(total );
        });
    if(prod_cnt>0){
        $("#dif_cnt_td").removeClass("minus_cnt");
        $("#dif_cnt_td").addClass("plus_cnt");
        var keycode = (event.keyCode ? event.keyCode : event.which);
            if(keycode == '13' && prod_cnt>0){
                submit_count();                             
            }                  
    }else{
        $("#dif_cnt_td").removeClass("plus_cnt");  
        $("#dif_cnt_td").addClass("minus_cnt");
    }		    
    var inpt_last_count = $(this).attr('inpt_last_count');
    var inpt_machine_id = $(this).attr('inpt_machine_id');
	}); 
	
});
JS;
	$this->registerJs($add_item, yii\web\View::POS_END);
?>

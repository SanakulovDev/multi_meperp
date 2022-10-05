$(document).ready(function (){
	/* search form */
	$('#menu-search').keyup(function (){
		var filter = $(this).val()
		$('.sidebar-menu li').each(function (){
			$(this).show()
			if(filter != ''){
				if($(this).text().search(new RegExp(filter, 'i')) < 0){
					$(this).removeClass('find-searching')
					$(this).hide()
					$(this).fadeOut()
				}else{
					$(this).addClass('find-searching')
					$(this).show()
					$(this).fadeIn()
				}
			}
		})
		$('.find-searching a span').each(function (){
			if($(this).text().toLowerCase().indexOf($('#menu-search').val().toLowerCase()) != -1){
				if($(this).children('ul')){
					$('.find-searching').parentsUntil('.sidebar-menu').addClass('menu-open')
					$(this).parent().parent().children('ul').show()
					$(this).parent().parent().children('ul').children().show()
				}
			}
		})
	})
	/* search form */
	// append tr to table
	$('.btnAddDetail').on('click', function (){
		if($('#detailTable tbody tr').length > 2){
			$('#detailTable tr:last td .detail_part').select2('close')
		}
		var tr_content = $('#tr_template').html()
		$('#detailTable tr:last').
		after('<tr class="tr_item"> ' + tr_content + '</tr>')
		$('#detailTable tr.tr_item th:last').
		html('<input type="hidden" name="items[num][]" value="' +
			     ($('#detailTable tbody tr').length - 2) + '"/>' +
			     +($('#detailTable tbody tr').length - 2))
		//$(".select2").select2();
		$('#detailTable tr:last td .detail_part').select2()
		$('.select2-selection.select2-selection--single').last().focus()
	})
	$('.btnAddDetailFooter').on('click', function (){
		$('.btnAddDetail').trigger('click')
	})
	// remove current row from table
	$('#detailTable').on('click', '.removeIcon', function (){
		$(this).parent().parent().remove()
		var n = 0
		$('#detailTable tr.tr_item th').each(function (){
			n = n + 1
			$(this).
			html('<input type="hidden" name="items[num][]" value="' + n + '">' + n)
		})
		$('.detail-qty').trigger('change')
	})
	$('#detailTable').on('change', '.detail_part', function (){
		var elemant = $(this)
		var part_id = $(this).val()
		var wh_id   = $('#document-from_warehouse_id').val()
		var url     = $(this).attr('data-url')
		elemant.parent().parent().find('.partname').html('')
		elemant.parent().parent().find('.unit').html('')
		elemant.parent().parent().find('.stock').html('')
		if(wh_id != '' && part_id != ''){
			$.ajax({
				       dataType: 'json',
				       type: 'GET',
				       url: url + '?id=' + part_id + '&whid=' + wh_id,
				       success: function (jsondata){
					       //console.log(jsondata);
					       elemant.parent().
					               parent().
					               find('.partname').
					               html(jsondata.partname)
					       elemant.parent().parent().find('.unit').html(jsondata.unit)
					       elemant.parent().parent().find('.stock').html(jsondata.stock)
				       },
			       })
		}
	})
	// append tr to table
	$('#btnAddContDetail').on('click', function (){
		var tr_content = $('#tr_template').html()
		$('#inv_cont_ship tr:last').
		after('<tr class="tr_item"> ' + tr_content + '</tr>')
		$('#inv_cont_ship tr.tr_item th:last').
		html('<input type="hidden" name="items[num][]" value="' +
			     ($('#inv_cont_ship tbody tr').length - 2) + '"/>' +
			     +($('#inv_cont_ship tbody tr').length - 2))
	})
// remove current row from table
	$('#inv_cont_ship').on('click', '.removeIcon', function (){
		$(this).parent().parent().remove()
		var n = 0
		$('#inv_cont_ship tr.tr_item th').each(function (){
			n = n + 1
			$(this).
			html('<input type="hidden" name="items[num][]" value="' + n + '">' + n)
		})
	})
	// $(".dropdown_select2:not(#tr_template td .select2)").select2();
	$('#btnPdf').on('click', function (e){
		//var custom_css = $('#custom_css').html();
		var pdfarea = $('#printarea').html()
		//console.log($('#printarea1').html());
		var opt     = {
			margin: 5,
			filename: $('#docnum').html(),
			image: { type: 'jpeg', quality: 0.98 },
			html2canvas: { scale: 2 },
			jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
		}
		html2pdf(pdfarea, opt)
	})
	// $(".dropdown_select2:not(#tr_template td .select2)").select2();
	// init all select2 dropdowns
	$('.select2').select2(
		// {
		//   placeholder: '. . .',
		//     allowClear: true,
		// },
	)
	$(document).ajaxComplete(function (){
		$('select.select2').select2(
		)
	})

	$(document).
	on('beforeValidate', 'form:not(.modalForm,#formBarcode)',
	   function (event, messages, deferreds){
		   $(this).find(':submit').attr('disabled', true)
	   }).
	on('afterValidate', 'form:not(.modalForm,#formBarcode)',
	   function (event, messages, errorAttributes){
		   if(errorAttributes.length > 0){
			   $(this).find(':submit').attr('disabled', false)
		   }
	   })
	$(document).on('click', '.searchPjax', function (event){
		event.preventDefault()
		let url         = window.location.href
		window.location = url.replace('/index', '/xls')
	})
	$('.aButton').on('click', function (e){
		e.preventDefault()
		$(this).attr('style', 'display:none')
	})
})

/**
 * @param table_id
 * @param xls_name
 * for example: html_xls_export('table_id', 'xls_name');
 *
 */
function html_xls_export(table_id, xls_name){
	var uri       = 'data:application/vnd.ms-excel;base64,',
	    template  = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" <head><meta charset="utf-8"/><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
		    '<style>table{border-collapse:collapse; border:thin solid #969696 !important;}' +
		    'table th{ border:thin solid #969696 !important; background-color:#DDEBF7 !important;}' +
		    'table td{border:thin solid #969696 !important;}' +
		    '</style>' +
		    '</head><body><table>{table}</table></body></html>',
	    base64    = function (s){
		    return window.btoa(unescape(encodeURIComponent(s)))
	    },
	    format    = function (s, c){
		    return s.replace(/{(\w+)}/g, function (m, p){
			    return c[p]
		    })
	    }
	var toExcel   = document.getElementById(table_id).innerHTML
	var ctx       = {
		worksheet: xls_name || '', table: toExcel,
	}
	var link      = document.createElement('a')
	link.href     = uri + base64(format(template, ctx))
	link.download = xls_name + '.xls'
	link.click()
}

function separate100_dec(num = 0, separator = ',', dec = 2){
	var split   = num.toFixed(dec).toString().split('.')
	var numeric = split[0]
	var decimal = split.length > 1 ? '.' + split[1] : ''
	var reg     = /(\d+)(\d{3})/
	while(reg.test(numeric)){
		numeric = numeric.replace(reg, '$1' + separator + '$2')
	}
	return numeric + decimal
}

$('#btnDownloadIntTransit').on('click', function (e){
	var xlsarea = $('#div_fix_table').html()
	var data    = '<html xmlns:x="urn:schemas-microsoft-com:office:excel"><head></head><body>' +
		xlsarea + '</body></html>'
	window.open('data:application/vnd.ms-excel,' + encodeURIComponent(data))
})
$('#detailTable').on('focus', '.detail-qty', function (){
	$('.tr_item').each(function (i, obj){
		$(this).css('background-color', '')
	})
	$(this).parent().parent().css('background-color', '#daf3da')
})
$('#detailTable').on('change', '.detail-qty', function (){
	//$('#detailTable').on('change', '.detail-qty:not(#tr_template td .detail-qty)', function () {
	var total = 0
	var qty   = 0
	$('.detail-qty:not(#tr_template td .detail-qty)').each(function (i, obj){
		qty   = $(this).val() || 0
		total = total + parseFloat(qty)
//        console.log(typeof qty);
//        console.log(qty);
//        console.log(total);
	})
	//console.log(total);
	$('#total').html(total)
})
// on first focus (bubbles up to document), open the menu
$(document).on('focus', '.select2-selection.select2-selection--single', function (e){
	// highlight border
//    $('.select2-selection.select2-selection--single').each(function(i, obj) {
//        $(this).css("border-color","#d2d6de");
//    });
//
//    $(this).css("border-color","#f7b5fb");
	// ********
	// select2 open
	$(this).closest('.select2-container').siblings('select:enabled').select2('open')
	// tr background
	$('.tr_item').each(function (i, obj){
		$(this).css('background-color', '')
	})
	$(this).closest('.tr_item').css('background-color', '#daf3da')
})
// steal focus during close - only capture once and stop propogation
$('select.select2').on('select2:closing', function (e){
	$(e.target).data('select2').$selection.one('focus focusin', function (e){
		e.stopPropagation()
	})
})
$(document).keydown(function (event){
	var keycode = (event.keyCode ? event.keyCode : event.which)
	if(keycode == 113){
		$('.btnAddDetail').trigger('click')
	}
})
$('.action_dropdown').click(function (e){
	e.stopPropagation()
	$('.dropdown-toggle').dropdown('toggle')
	// $(this).parent().toggleClass('open');
})
$('.select_supp_wh').change(function (e){
	var wh_id = $(this).val()
	var url   = $(this).attr('data-url')
	$.ajax({
		       dataType: 'json',
		       type: 'GET',
		       url: url + '?whid=' + wh_id,
		       success: function (data){
			       $('.detail_part').each(function (i, obj){
				       var el = $(this)
				       el.html('')
				       el.append($('<option>', {
					       value: '',
					       text: 'Выберите...',
				       }))
				       $.each(data, function (k, part){
					       el.append($('<option>', {
						       value: part.id,
						       text: part.info,
					       }))
					       el.trigger('change')
				       })
			       })
		       },
	       })
})
$('#btn-pending').on('click', function (){
	$('#modal-pending').modal('show')
})
$('.product-parts-index').on('click', '.part_link', function (){
	var arrParts = []
	var part_id  = $(this).attr('data-part_id')
	var url      = $(this).attr('data-url')
	$.ajax({
		       dataType: 'json',
		       type: 'GET',
		       url: url + '?id=' + part_id,
		       success: function (data){
			       var spart = data.selected_part
			       // selected part
			       $('#selected_part_info').html(spart.part_info)
			       $('#selected_part_name').html(spart.part_name)
			       $('#selected_part_unit').html(spart.unit)
			       $('#selected_part_state').html(spart.state)
			       $('#selected_part_state').attr('title', spart.state_text)
			       if(spart.state == 'P'){
				       $('#selected_part_state').removeClass('text-primary')
				       $('#selected_part_state').addClass('text-success')
				       $('#selected_part_info').removeClass('text-primary')
				       $('#selected_part_info').addClass('text-success')
			       }else{
				       if(spart.state == 'S'){
					       $('#selected_part_state').removeClass('text-success')
					       $('#selected_part_state').addClass('text-primary')
					       $('#selected_part_info').removeClass('text-success')
					       $('#selected_part_info').addClass('text-primary')
				       }
			       }
//      var row = "";
//      row += "<tr>";
//      row += "<td>Part</td>";
//      row += "<td>Part name</td>";
//      row += "<td>Part state</td>";
//      row += "<td>Sub part</td>";
//      row += "<td>Sub part name</td>";
//      row += "<td>Sub part state</td>";
//      row += "<td>Uloc</td>";
//      row += "<td>Usage qty</td>";
//      row += "<td>UOM</td>";
//      row += "</tr>";
//
//      $('#tableDownload').html(row);
			       $('.bom_treeview').html(partRecursive(data.childs))
			       $('#dataDownload').val(JSON.stringify(arrParts))
			       console.log(arrParts)
			       $('.bom_treeview').treeView()
			       $('#modal-bom-collapse').modal('show')
		       },
	       })

	function partRecursive(childs){
		var treeviewContent = ''
		$.each(childs, function (){
			var content    = ''
			var colorClass = ''
			if(this.sub_part_state == 'P'){
				colorClass = 'text-success'
			}else{
				if(this.sub_part_state == 'S'){
					colorClass = 'text-primary'
				}else{
					if(this.sub_part_state == 'R'){
						colorClass = 'text-black'
					}
				}
			}
			content += '<li>'
			var item    = ''
			item += '<p style="float: left;margin-bottom: 0px"><span class="selected_part_state ' + colorClass + '"  title="' + this.sub_part_state_text + '">' + this.sub_part_state + '</span></p>'
			item += '<p style="float: left;margin-left: 10px;margin-bottom: 0px;min-width: 500px;">'
			item += '<span class="modal-title selected_part_info ' + colorClass + ' ">' + this.sub_part_info + '</span> | <span class="selected_part_uloc">' + this.uloc + '</span> | <span class="selected_part_uloc">' + this.usage_qty + '</span> | <span class="selected_part_unit">' + this.unit + '</span><br>'
			item += '<span class="selected_part_name">' + this.sub_part_name + '</span>'
			item += '</p><div style="clear: both"></div>'
			var objPart = {
				id: this.id,
				model: this.model,
				parent_part_number: this.parent_part_number,
				parent_part_color: this.parent_part_color,
				parent_part_name: this.parent_part_name,
				parent_part_state_text: this.parent_part_state_text,
				parent_part_status: this.parent_part_status,
				sub_part_number: this.sub_part_number,
				sub_part_color: this.sub_part_color,
				sub_part_name: this.sub_part_name,
				sub_part_state_text: this.sub_part_state_text,
				sub_part_status: this.sub_part_status,
				created_by: this.created_by,
				created_at: this.created_at,
				updated_by: this.updated_by,
				updated_at: this.updated_at,
				uloc: this.uloc,
				usage_qty: this.usage_qty,
				unit: this.unit,
				status: this.status,
				remark: this.remark,
			}
			arrParts.push(objPart)
			content += item
			if(this.childs.length !== 0){
				content += '<ul>'
				content += partRecursive(this.childs)
				content += '</ul>'
			}
			content += '</li>'
			treeviewContent += content
		})
		return treeviewContent
	}
})
$('#btnDownloadDetBom').on('click', function (e){
	$('#formDownload').submit()
})
$('#pending-menu').on('click', function (){
	if($(this).parent().hasClass('open')){
		$(this).parent().removeClass('open')
	}else{
		$(this).parent().addClass('open')
	}
})
$('#select_model').change(function (e){
	var floc     = $('#select_floc').val()
	var model_id = $('#select_model').val()
	var side     = $('#select_side').val()
	var url      = $(this).attr('data-url')
	console.log(url)
	$.ajax({
		       dataType: 'json',
		       type: 'GET',
		       url: url + '?floc=' + floc + '&model_id=' + model_id + '&side=' + side,
		       success: function (data){
			       $('#productionorder-part_id').each(function (i, obj){
				       var el = $(this)
				       el.html('')
				       el.append($('<option>', {
					       value: '',
					       text: 'Выберите...',
				       }))
				       $.each(data, function (k, part){
					       el.append($('<option>', {
						       value: part.id,
						       text: part.info,
					       }))
				       })
			       })
		       },
	       })
})
$('#select_side').change(function (e){
	$('#select_model').trigger('change')
})
$('#select_floc').change(function (e){
	$('#select_model').trigger('change')
})
// scaner - beginning
$('#barcode').keydown(function (e){
	var keyCode = e.keyCode || e.which
	if(keyCode == 9){
		e.preventDefault()
		var barcodeType = null
		var barcodeText = $(this).val().trim()
		var barcodePage = $(this).attr('data-barcode-page')
		if(barcodeText == '') return false
		var barcodeArray = barcodeText.split(':')
		if(barcodeArray[0] == 'W'){
			// WH barcode
			barcodeType = 'W'
			if(barcodeArray[1] == 'F'){
				if(barcodePage == 'receipt-local-kd' || barcodePage == 'receipt-local-con') return false
				// From wh
				var whFromId = barcodeArray[2]
				var whFrom   = getWhNamById(whFromId)
			}else
				if(barcodeArray[1] == 'T'){
					// To wh
					var whToId = barcodeArray[2]
					var whTo   = getWhNamById(whToId)
				}else{
					// Wrong wh
					return false
				}
		}else
			if(barcodeArray[0] == 'S'){
				// Receipt from suppliers
				barcodeType = 'S'
				if(barcodeArray[1] == 'F'){
					// From wh
					var whFromId = barcodeArray[2]
					if(barcodePage == 'receipt-local-kd'){
						var whFromId = barcodeArray[2]
						var whFrom   = getSupplierNameById(whFromId)
					}else{
						var whFromId = getWhIdBySupplierId(barcodeArray[2])
						var whFrom   = getSupplierNameById(barcodeArray[2])
					}
					console.log(whFrom)
				}else{
					return false
				}
			}else
				if(barcodeArray[0] == 'P'){
					// Part barcode
					barcodeType   = 'P'
					var part_name = getPartNamByPartNumber(barcodeArray[1])
				}else{
					// Ushbu barcode i/ch da fakt kiritganda hosil bo'lgan barcode
					barcodeType   = 'PP'
					var part_name = getPartNamByPartNumber(barcodeArray[0])
				}
		// displaying data
		// whs
		if(barcodeType == 'W'){
			$('#from-wh').html(whFrom)
			$('#to-wh').html(whTo)
			$('#from-wh-id').html(whFromId)
			$('#to-wh-id').html(whToId)
		}
		if(barcodeType == 'S'){
			if(barcodePage == 'receipt-local-kd'){
				$('#from-wh').html('OUTSOURCING (' + whFrom + ')')
			}else{
				$('#from-wh').html(whFrom)
			}
			$('#to-wh').html(whTo)
			$('#from-wh-id').html(whFromId)
			$('#to-wh-id').html(whToId)
		}
		// partlist
		if(barcodeType == 'P'){
			if(checkIfExists(barcodeText)){
				$('#error').fadeOut()
				$('#errorTitle').html('<i class="icon fa fa-ban"></i> ' + $('#err_title').html())
				$('#contentError').html($('#duplicate_barcode').html())
				$('#error').fadeIn()
				$(this).val('')
				$(this).focus()
				return false
			}
			addRow(barcodeArray[1], part_name, barcodeArray[2], barcodeText)
		}
		// partlist fact
		if(barcodeType == 'PP'){
			addRow(barcodeArray[0], part_name, barcodeArray[3])
		}
		// ***
		// HTML taglardagi data ni forma atributiga JSON qilib yigamiz.
		collectBarcodaData()
		//Barcode textboxni clear qilamiz va focus qilamiz.
		$(this).val('')
		$(this).focus()
		//Agar To WH scan qilinsa submit qilamiz
		if(barcodeType == 'W' && barcodeArray[1] == 'T'){
			$('#submit').trigger('click')
		}
	}
})
// remove current row from table
$('#detailTable').on('click', '.removeIcon', function (){
	$(this).parent().parent().remove()
	var n = 0
	$('#detailTable tr.tr_item th').each(function (){
		n = n + 1
		$(this).
		html('<input type="hidden" name="items[num][]" value="' + n + '">' + n)
	})
	collectBarcodaData()
	$('#barcode').focus()
})
// Forma qaysi tarafdan submit  qilinsa ham validation ishlaydi
$('#formBarcode').submit(function (e){
	var isValid = true
	var errors  = []
	$('#error').fadeOut()
	if($('#from-wh').html() == ''){
		isValid = false
		errors.push($('#err_from_wh').html())
	}
	if($('#to-wh').html() == ''){
		isValid = false
		errors.push($('#err_to_wh').html())
	}
	var partsCount = $('#detailTable tbody tr:not(#tr-head)').length
	if(partsCount == 0){
		isValid = false
		errors.push($('#err_part_list').html())
	}
	if(isValid == false){
		var errorlist = ''
		$.each(errors, function (index, value){
			errorlist += '- ' + value + '<br>'
		})
		$('#errorTitle').html('<i class="icon fa fa-ban"></i> ' + $('#err_title').html())
		$('#contentError').html(errorlist)
		$('#error').fadeIn()
		e.preventDefault()
	}
//    else{
//      return true;
//    }
})
$('#btnCloseErrors').click(function (){
	$('#error').fadeOut()
})

function collectBarcodaData(){
	var data      = {}
	// collecting data
	data.whFrom   = $('#from-wh').html()
	data.whTo     = $('#to-wh').html()
	data.whFromId = $('#from-wh-id').html()
	data.whToId   = $('#to-wh-id').html()
	var partList  = []
	$('#detailTable tr:not(#tr-head)').each(function (i, row){
		var part         = {}
		part.barcodeText = $(row).attr('data-barcode')
		part.partNumber  = $(row).find('td.part').html()
		part.partName    = $(row).find('td.part-name').html()
		part.qty         = $(row).find('td.qty').html()
		partList.push(part)
	})
	data.partList = partList
	// ***
	$('#barCodeData').val(JSON.stringify(data))
}

function addRow(partNumber, part_name, qty, barcodeText = null){
	var tr_content = '<th></th>'
	tr_content += '<td class="part">' + partNumber + '</td>'
	tr_content += '<td class="part-name" title="' + part_name + '">' + part_name + '</td>'
	tr_content += '<td style="text-align: right" class="qty">' + qty + '</td>'
	tr_content += '<td style="text-align: center"><span class="glyphicon glyphicon-trash text-danger removeIcon" aria-hidden="true"></span></td>'
	$('#detailTable tr:last').after('<tr class="tr_item" data-barcode="' + barcodeText + '" title="' + barcodeText + '" > ' + tr_content + '</tr>')
	$('#detailTable tr.tr_item th:last').
	html('<input type="hidden" name="items[num][]" value="' +
		     ($('#detailTable tbody tr').length - 1) + '"/>' +
		     +($('#detailTable tbody tr').length - 1))
}

function getWhNamById(wh_id){
	var json_wh_list = JSON.parse($('#json_wh_list').html())
	return json_wh_list[wh_id]
}

function getSupplierNameById(sp_id){
	var json_sp_list = JSON.parse($('#json_sp_list').html())
	return json_sp_list[sp_id]
}

function getWhIdBySupplierId(sp_id){
	var json_suppwh_ids = JSON.parse($('#json_suppwh_ids').html())
	return json_suppwh_ids[sp_id]
}

function getPartNamByPartNumber(part_number){
	var json_part_list = JSON.parse($('#json_part_list').html())
	return json_part_list[part_number]
}

function checkIfExists(barcodeText){
	var ifExists = false
	$('#detailTable tr:not(#tr-head)').each(function (i, row){
		if($(row).attr('data-barcode') == barcodeText){
			ifExists = true
		}
	})
	return ifExists
}

// end of scaner
$('.select_supplier').change(function (e){
	var sp_id = $(this).val()
	var url   = $(this).attr('data-url')
	$.ajax({
		       dataType: 'json',
		       type: 'GET',
		       url: url + '?spid=' + sp_id,
		       success: function (data){
			       $('.detail_part').each(function (i, obj){
				       var el = $(this)
				       el.html('')
				       el.append($('<option>', {
					       value: '',
					       text: 'Выберите...',
				       }))
				       $.each(data, function (k, part){
					       el.append($('<option>', {
						       value: part.id,
						       text: part.info,
					       }))
					       el.trigger('change')
				       })
			       })
		       },
	       })
})


$('#route-ship_mode').change(function (e){
	var ship_mode = $(this).val();
	var url   = $(this).attr('data-url');
	$.ajax({
		       dataType: 'json',
		       type: 'GET',
		       url: url + '?shipMode=' + ship_mode,
		       success: function (data){


				       var el_from = $('#route-from_point_id');
				       el_from.html('')

							 el_from.append($('<option>', {
					       value: '',
					       text: 'Выберите...',
							 }))

				       $.each(data, function (k, point){
					       el_from.append($('<option>', {
						       value: point.id,
						       text: point.name,
					       }))
							 })

							 var el_to = $('#route-to_point_id');
				       el_to.html('')

							 el_to.append($('<option>', {
					       value: '',
					       text: 'Выберите...',
							 }))

				       $.each(data, function (k, point){
					       el_to.append($('<option>', {
						       value: point.id,
						       text: point.name,
					       }))
				       })

		       },
	       })
});






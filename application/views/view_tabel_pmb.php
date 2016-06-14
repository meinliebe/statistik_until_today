<style>
/*	#tabelPmbWeek_wrapper{
		margin-top: 10px;
	}*/	
	div.wrapper-judul {
		/*background-color: #333;*/
		background-color: #337AB7;
		color: #FFF;
		margin: 0px;		
		text-align: left;
		margin-top: 5px;
		 /*padding: 10px; */
	}
	h5.judul {
		display: inline-block;
		margin: 10px;		
		padding: 0px;
		color:#FFFFFF;
	}	
	th.dt-center{ 
		text-align: center; vertical-align: middle !important;
	}
	td.column-dt-center{
		text-align: center; vertical-align: middle !important;
	}
	.bootbox-alert.in > div > div > div.modal-header{
		text-align: center;
	}
	.bootbox-alert.in > div > div > div.modal-body{
		text-align: center;		
	}
	
</style>	
<div class="row-fluid well row-table">
	<div class="col-xs-3 col-md-offset-3">
		<div class="form-group">
			<select id="inputNamaJurusan" name="inputNamaJurusan" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Silakan pilih jurusan yang ingin ditampilkan"></select>
		</div>	
	</div>
	<div class="col-xs-2" style="padding-left: 0px;">
		<div class="form-group">
			<select id="inputBulan" name="inputBulan" class="form-control input-sm">
				<!-- <option value="0"></option> -->
				<option value="01">Januari</option>
				<option value="02">Februari</option>
				<option value="03">Maret</option>
				<option value="04">April</option>
				<option value="05">Mei</option>
				<option value="06">Juni</option>
				<option value="07">Juli</option>
				<option value="08">Agustus</option>
				<option value="09">September</option>
				<option value="10">Oktober</option>
				<option value="11">November</option>
				<option value="12">Desember</option>
			</select>
		</div>
	</div>	
	<button type="button" id="tabelShow" class="btn btn-primary btn-sm">
		<span class="glyphicon glyphicon-list-alt"></span> Tampilkan
	</button>
	<div class="" style="margin-top: 15px;">
		<div class="wrapper-judul">
			<h5 class="judul"><strong>Tabel Penerimaan Mahasiswa Baru</strong></h5>
		</div>
		<table id="tabelPmbWeek" class="table table-hover table-bordered table-condensed table-striped" cellspacing="0" width="100%" style="background-color: white;">
			<thead>
				<tr>
					<th>Periode</th>
					<th>Jumlah Pendaftar</th>
					<th>Passing Grade</th>
					<th>Jumlah Diterima</th>
					<th>Jumlah Registrasi</th>
					<th>Average Score</th>
					<th>Qb Score</th>
					<th>Median Score</th>
					<th>Qa Score</th>
					<th>Maximal Score</th>
					<th>Minimal Score</th>			
				</tr>
			</thead>
			<tbody id="tabelPmbWeekBody"></tbody>
		</table>	
	</div>		
</div>

<script>	
	$(document).on('ready', function(){
		$('#inputNamaJurusan').select2({
			placeholder: "Silakan Pilih Jurusan",
		});

		var month = '<?php echo date("m"); ?>';		
		var jurusan = $('#inputNamaJurusan').val();
		$('#inputBulan').val('<?php echo date("m")?>');
		initTable(month, jurusan);

		function initTable(month, jurusan){
			$('#tabelPmbWeek').dataTable({
				"bSort" : false,
				"bInfo": false,
				"bFilter": false,
			    "bPaginate": false,
			    "bLengthChange": false,			    
			    "bAutoWidth": false,				    
			    "sAjaxDataProp":"",	
			    "responsive": true,
			    // "oLanguage": {"sZeroRecords": "", "sEmptyTable": ""},
			    "ajax": {
			    	"url": '<?php echo base_url(); ?>index.php/statistik_pmb/load_tabel',
	              	"type": 'POST',
	              	"data": {
	              		month: month,
	              		jurusan: jurusan              		
	              	}
			    },
		        "columns": [
					{"data": "periode", width: "20%", className: "dt-center column-dt-center"},
					{"data": "jml_daftar", width: "10%", className: "dt-center"},
					{"data": "passing_grade", width: "10%", className: "dt-center"},
					{"data": "jml_diterima", width: "10%", className: "dt-center"},
					{"data": "jml_reg", width: "10%", className: "dt-center"},
					{"data": "average_score", width: "10%", className: "dt-center"},
					{"data": "q1", width: "10%", className: "dt-center"},
					{"data": "median_score", width: "10%", className: "dt-center"},
					{"data": "q3", width: "10%", className: "dt-center"},
					{"data": "maximal_score", width: "10%", className: "dt-center"},
					{"data": "minimal_score", width: "10%", className: "dt-center"},
		        ]
			});			
		}

		$('#tabelShow').on('click', function(){
			var month = $('#inputBulan').val();
			var jurusan = $('#inputNamaJurusan').val();			
			if(month == 0){
				bootbox.alert({
					size: "small",
					onEscape: false,
					closeButton: false,
					title: "PERHATIAN",
					message: "Silakan pilih bulan terlebih dahulu!",					
				});
			} else {
				$('#tabelPmbWeek').dataTable().fnDestroy();
				initTable(month, jurusan);				
			}
		})
		
		// $('a[href="#1"]').on('shown.bs.tab', function(e){
		// 	$('#tabelPmbWeek').dataTable();
		// });			

		// $('a[href="#2"]').on('shown.bs.tab', function(e){
		// 	$('#tabelPmbWeek').dataTable().fnDestroy();
		// });	

		// $('a[href="#3"]').on('shown.bs.tab', function(e){
		// 	$('#tabelPmbWeek').dataTable().fnDestroy();
		// });
	});

// $('#tabelPmbWeek').handsontable({			
// 	rowHeaders: true,
// 	stretchH: 'all',
// 	startCols: 9,	
// 	startRows: 10,
// 	columnSorting: false,
// 	enterBeginsEditing: false,
// 	readOnly: true,										
// 	colHeaders: [
// 		'Periode'
// 		,'Jumlah Pendaftar'
// 		,'Passing Grade'
// 		,'Jumlah Diterima'
// 		,'Jumlah Registrasi'
// 		,'Average score'
// 		,'Median score'
// 		,'Maximal score'
// 		,'Minimal score'								
// 	]			
// });	
</script>
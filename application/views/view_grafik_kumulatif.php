
<style>
	.grafik_kumulatif{
		visibility: initial;
		/*width: auto;*/
		/*height: 380px !important;*/
	}
	.select2-container{
		 width: 250px !important;
	}
</style>

<div class="row-fluid well row-grafik" hidden="true">
	<div class="col-xs-3 col-md-offset-4">
		<div class="form-group">
			<select id="inputJurusan2" name="inputJurusan2" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Silakan pilih jurusan yang ingin ditampilkan"></select>
		</div>		
	</div>
<!-- 	<button type="button" id="graphicShow" class="btn btn-primary btn-sm">
		<span class="glyphicon glyphicon-stats"></span> Tampilkan
	</button>			 		 -->
	<div id="grafik_kumulatif" class="row-fluid grafik_kumulatif" style="height: 400px; width: 1100px; margin: 0 auto"></div>
</div>

<script>
	$(document).on('ready', function(){		
		$('#inputJurusan2').select2({
			placeholder: " - Program Studi - ",
		});	

		$('#inputJurusan2').on('select2:select', function(e){
			getGraphic();																
		});

		$('.menuGrafik').on('click', function(){					
			$('.row-keseluruhan').hide();
			$('.row-grafik').show();	
			$('.row-kumulatif-nilai').hide();	
			getGraphic();													
			$('#menu3').removeClass('active');
			$('#menu2').addClass('active');
			$('#menu1').removeClass('active');
		});

		var options = {
			chart: {
				renderTo: 'grafik_kumulatif',
				type: 'area',				
				zoomType: 'x'	            
			},
			// colors: ['#5BC0EB', '#6EEB83', '#DDDF00', '#FF5714', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Grafik Kumulatif Penerimaan Mahasiswa '
			},
			legend: {
				enabled: false
			},
			subtitle: {
				// text: '<?php echo date('d M Y'); ?>'
			},
			plotOptions:{
				series: {
	                marker: {
	                    enabled: false,
	                    radius: 1,
	                    lineWidth: 1,	                    
	                    lineColor: null
	                },
	    //             pointStart: Date.UTC(2015, 1, 1),
					// pointInterval: 24 * 3600 * 1000
	            }
	        },
			xAxis: {
				title:{
					text: 'Tanggal Daftar'
				},
				type: 'datetime',	            
	            dateTimeLabelFormats: { // don't display the dummy year
	                month: '%e. %b',
	                year: '%b'
	            },				
			},
			yAxis: {				
				title: {
					text: 'Jumlah'
				}
			},
			tooltip: {
	            positioner: function () {
	                return { x: 70, y: 50 };
	            },				
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y} orang</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
		    series: []
		};				   	

		function getGraphic(){		
			$('body').loader('show');
			$.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_grafik_kumulatif',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	inputJurusan2: $('#inputJurusan2').val(),
		        	// inputTglUjian: $('#inputTglUjian').val()
		        },
		        success: function(response){		        	
		        	if(response !== 0){				        		
						options.xAxis.categories = response['tgl_daftar'];
						options.series[0] = response['jml_daftar'];
						options.series[1] = response['jml_diterima'];
						options.series[2] = response['jml_reg'];						
						options.series[3] = response['jml_mundur'];						
						chart = new Highcharts.Chart(options);								   	                	
		        	} else {
		        		alert('Data tidak ditemukan');
		        	}
					$('body').loader('hide');
		        }
			});
		}

		$('#graphicShow').on('click', function(){
			getGraphic();
		});	
	});
</script>
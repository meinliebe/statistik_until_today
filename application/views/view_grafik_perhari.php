<style>
	.grafik_perhari{
		height: 400px;
		margin: auto;
		/*min-width: 310px;*/
		width: auto;				
	}
</style>

<div id="grafik_perhari" class="grafik_perhari"></div>			
<script>
	$(document).on('ready', function(){
		
		getGraphic();

		var options = {
			chart: {
				renderTo: 'grafik_perhari',
				type: 'column'				
			},
			colors: ['#6EEB83', '#FF5714', '#5BC0EB', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Grafik Penerimaan Mahasiswa Baru'
			},
			legend: {
				enabled: false
			},
			subtitle: {
				// text: '<?php echo date('d M Y'); ?>'
			},
			xAxis: {
	            type: 'datetime',
	            dateTimeLabelFormats: { // don't display the dummy year
	                month: '%e. %b',
	                year: '%b'
	            },				
				title: {
					text: 'Tanggal'
				},
				categories: '',
				crosshair: false
			},
			yAxis: {
				min: 0,
				title: {
					text: 'Jumlah'
				}
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
				'<td style="padding:0"><b>{point.y:.1f} orang</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
		    series: []
		};				   	

		function getGraphic(){
			$.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_grafik_perhari',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	inputNamaJurusan: $('#inputNamaJurusan').val(),
		        	inputTglUjian: $('#inputTglUjian').val()
		        },
		        success: function(response){
		        	if(response !== 0){		            	
						// options.xAxis.categories = response['tgl_ujian'];
						options.series[0] = response['diterima'];
						options.series[1] = response['tidakDiterima'];
						options.series[2] = response['jumlahPendaftar'];						
						chart = new Highcharts.Chart(options);			 		    	    									 		    	                		
		        	} else {
		        		alert('Data tidak ditemukan');
		        	}
		        }
			});
		}

		$('#graphicShow').on('click', function(){
			getGraphic();			
		});	
	});
</script>


<div class="row-fluid well row-kumulatif-nilai" hidden="true">
	<div class="col-xs-3 col-md-offset-4">
		<div class="form-group">
			<select id="inputJurusan3" name="inputJurusan3" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Silakan pilih jurusan yang ingin ditampilkan"></select>
		</div>		
	</div>
	<button type="button" id="kumulatif_nilai_show" class="btn btn-primary btn-sm">
		<span class="glyphicon glyphicon-stats"></span> Tampilkan
	</button>
	<div id="kumulatif_nilai_reg" class="row-fluid grafik_keseluruhan" style="height: 400px; width: 1100px; margin: 0 auto"></div>	
	<div id="kumulatif_nilai_all" class="row-fluid grafik_keseluruhan" style="height: 400px; width: 1100px; margin: 0 auto"></div>	
</div>

<script>
	$(document).on('ready', function(){
		$('.menuKumulatif').on('click', function(){	
			get_nilai_kumulatif_reg();
			get_nilai_kumulatif_all();

			$('#inputJurusan3').select2({
				placeholder: "Silakan Pilih Jurusan",
			});				
			// $('.row-table').hide();													
			$('.row-keseluruhan').hide();
			$('.row-grafik').hide();										
			$('.row-kumulatif-nilai').show();

			// $('#menu1').addClass('active');
			$('#menu3').removeClass('active');
			$('#menu2').removeClass('active');
			$('#menu1').addClass('active');
		});	

		$('#kumulatif_nilai_show').on('click', function(){
			get_nilai_kumulatif_reg();		
			get_nilai_kumulatif_all();
		});

		option_4 = {
			chart: {
				renderTo: 'kumulatif_nilai_reg',
				type: 'spline',				
				zoomType: 'x'	            
			},
			// colors: ['#5BC0EB', '#6EEB83', '#DDDF00', '#FF5714', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Distribusi Nilai Kumulatif (<b>hanya yang registrasi</b>) '
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
	                }
	            }
	        },
			xAxis: {
				title:{
					text: 'Nilai'
				}				
			},
			yAxis: {				
				title: {
					text: 'Jumlah'
				}
			},
			tooltip: {
	            // positioner: function () {
	            //     return { x: 70, y: 50 };
	            // },
                formatter:function(){                    
                    return '<table><tr><td style="color:{series.color};padding:0">Jumlah Nilai &le; '+this.key+': </td><td style="padding:0"><b>'+this.y+'</b></td></tr></table>';
                },
				useHTML: true
			},
		    series: []				
		}

		function get_nilai_kumulatif_reg () {
			$('body').loader('show');
			$.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_nilai_kumulatif_reg',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	inputJurusan3: $('#inputJurusan3').val(),			        	
		        },
		        success: function(response){
		        	if(response !== ''){
						option_4.xAxis.categories = response['axis'];						
						option_4.series[0] = response['cummulative'];	
						option_4.series[0].step = 'right';
						option_4.series[0].type = 'line';						
						chart = new Highcharts.Chart(option_4);		        		
		        	}
					$('body').loader('hide');
		        }
			});			 
		}

		option_5 = {
			chart: {
				renderTo: 'kumulatif_nilai_all',
				type: 'spline',				
				zoomType: 'x'	            
			},
			// colors: ['#5BC0EB', '#6EEB83', '#DDDF00', '#FF5714', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Distribusi Nilai Kumulatif (<b>semua yang diterima</b>) '
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
	            }
	        },
			xAxis: {
				title:{
					text: 'Nilai'
				}				
			},
			yAxis: {				
				title: {
					text: 'Jumlah'
				}
			},
			tooltip: {
	            // positioner: function () {
	            //     return { x: 70, y: 50 };
	            // },				
                formatter:function(){                    
                    return '<table><tr><td style="color:{series.color};padding:0">Jumlah Nilai &le; '+this.key+': </td><td style="padding:0"><b>'+this.y+'</b></td></tr></table>';
                },				
				useHTML: true
			},
		    series: []				
		}

		function get_nilai_kumulatif_all() {
			$('body').loader('show');
			$.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_nilai_kumulatif_all',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	inputJurusan3: $('#inputJurusan3').val(),			        	
		        },	
		        success: function(response){
		        	if(response !== ''){
						option_5.xAxis.categories = response['axis'];
						option_5.series[0] = response['cummulative'];	
						option_5.series[0].step = 'right';
						option_5.series[0].type = 'line';											
						chart = new Highcharts.Chart(option_5);			        		
		        	}
					$('body').loader('hide');
		        }			
			})
		}
	});
</script>
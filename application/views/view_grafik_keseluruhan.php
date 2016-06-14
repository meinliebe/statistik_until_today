<style>
	.space-border{
		padding: 10px;		
	}
</style>
<div class="row-fluid row-keseluruhan">
	<select id="inputJurusan1" name="inputJurusan1" class="form-control input-sm" data-toggle="tooltip" data-placement="top" title="Silakan pilih jurusan yang ingin ditampilkan" style="width: 520px;"></select>
	<div class="col-md-offset-3">
		<div class="form-group">
		</div>		
	</div>
	<!--<button type="button" id="keseluruhanShow" class="btn btn-primary btn-sm">
		<span class="glyphicon glyphicon-stats"></span> Tampilkan
	</button> -->
	<div class="row-fluid well">
		<div id="grafik_keseluruhan" class="row-fluid grafik_keseluruhan" style="height: 400px; width: 1100px; margin: 0 auto"></div>
		<div id="kumulatif_nilai_reg" class="row-fluid grafik_keseluruhan" style="height: 400px; width: 1100px; margin: 0 auto"></div>			
	</div>
	<div class="space-border"></div>	
	<div class="row-fluid well">
		<div id="grafik_kumulatif_keseluruhan" class="row-fluid grafik_kumulatif_keseluruhan" style="height: 400px; width: 1100px; margin: 0 auto"></div>
		<div id="kumulatif_nilai_all" class="row-fluid kumulatif_nilai_all" style="height: 400px; width: 1100px; margin: 0 auto"></div>	
	</div>
</div>
<script>
	$(document).on("ready", function(){
		// $('body').loader('show');	

		get_grafik_keseluruhan();
		get_nilai_kumulatif_reg();

		get_grafik_keseluruhan_kumulatif();
		get_nilai_kumulatif_all();

		$('#inputJurusan1').select2({
			placeholder: " - Program Studi - ",
		});	

		$('#inputJurusan1').on("select2:select", function(e) { 			
		   // what you would like to happen
			get_grafik_keseluruhan();
			get_nilai_kumulatif_reg();

			get_grafik_keseluruhan_kumulatif();	
			get_nilai_kumulatif_all();		   
		});

		$('.menuKeseluruhan').on('click', function(){
			$('.row-keseluruhan').show();
			$('.row-grafik').hide();	
			$('.row-kumulatif-nilai').hide();											

			get_grafik_keseluruhan();
			get_nilai_kumulatif_reg();

			get_grafik_keseluruhan_kumulatif();
			get_nilai_kumulatif_all();

			$('#menu3').addClass('active');
			$('#menu2').removeClass('active');
			$('#menu1').removeClass('active');
		});			

		var options_2 = {
			chart: {
				renderTo: 'grafik_keseluruhan',			
	            type: 'column',
	            zoomType: 'x',
	            resetZoomButton: {
	            	relativeTo: 'chart'
	            }                     
	        },
	        title: {
	            text: 'Distribusi Nilai Mahasiswa (<b>hanya yang registrasi</b>)',
				style: {
					fontSize: '16px'					
				}  	            
	        },
			legend: {
				enabled: false
			},	        
	        xAxis: {
	        	title: {
	        		text: 'Nilai'
	        	},       	
	        	allowDecimals: true,       	
				plotLines: [{                                        
                    color: 'black',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,	                    
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                       	  
                    },
                    zIndex: 5
				}, {                    
                    color: 'blue',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,	                    
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                       	                 
                    },
                    zIndex: 5
                }, {                    
                    color: 'black',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,	                    
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                 	
                    },
                    zIndex: 5
                }, {                    
                    color: 'orange',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,	                    
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 20,                	
                    },
                    zIndex: 5
                }, {
                    color: 'green',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                	
                    },
                    zIndex: 5                	
                }, {
                    color: 'red',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                	
                    },
                    zIndex: 5                	                    
                }],	        	
				title: {
					text: 'Nilai'
				},
				categories: '',
			},
	        yAxis: {	            
	            title: {
	                text: 'Jumlah Registrasi'
	            }
	        },
	        tooltip: {
	            // positioner: function () {
	            //     return { x: 80, y: 50 };
	            // },	        
	            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	                '<td style="padding:0"><b>{point.y}</b></td></tr>',
	        },
	        plotOptions: {
	            series: {
	                pointWidth: 22,
					connectNulls: true,					
	            }
	        },
	        series: []			
		}

		function get_grafik_keseluruhan() {						
			return $.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_grafik_keseluruhan',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	jurusan: $('#inputJurusan1').val()		        	
		        },
		        success: function(response){
		        	if(response){						
						options_2.xAxis.plotLines[0].value = response['q1PlotLines'];
						options_2.xAxis.plotLines[0].label.text = '<b>Qb:</b> '+response['q1PlotLines'];

						options_2.xAxis.plotLines[1].value = response['medianPlotLines'];
						options_2.xAxis.plotLines[1].label.text = '<b>Median:</b> '+response['medianPlotLines'];

						options_2.xAxis.plotLines[2].value = response['q3PlotLines'];
						options_2.xAxis.plotLines[2].label.text = '<b>Qa:</b> '+response['q3PlotLines'];

						options_2.xAxis.plotLines[3].value = response['meanPlotLines'];
						options_2.xAxis.plotLines[3].label.text = '<b>Mean:</b> '+response['meanPlotLines'];				

						options_2.xAxis.plotLines[4].value = response['min_extreme'];
						options_2.xAxis.plotLines[4].label.text = '<b>Min:</b> '+response['min_extreme'];

						options_2.xAxis.plotLines[5].value = response['max_extreme'];
						options_2.xAxis.plotLines[5].label.text = '<b>Max:</b> '+response['max_extreme'];

						options_2.xAxis.categories = response['fgrade'];						
						options_2.series[0] = response['jml_mhs'];
						
						options_2.xAxis.min = response['min_extreme'];
						options_2.xAxis.max = response['max_extreme'];	
						chart = new Highcharts.Chart(options_2);	
						// var point = chart.series[0].points[8];
		        	}			        			              
					if(response['fgrade'] !== ''){
				        chart.renderer.label('Total Mahasiswa Yang Registrasi: <b>' + response['total_mhs'] + '</b>', 780, 70)
			            .css({
			                fontSize: '11px',
			                color: '#000',
			                border: '1px solid #E3E3E3',
			            })
			            .attr({
			                fill: 'rgb(180, 228, 248)',				                
			                padding: 8,
			                r: 5,
			                zIndex: 6,
			            })
			            .add();							
					}
					// $('.grafik_keseluruhan').loader('hide');							
		        }
			})
		}

		var options_3 = {
			chart: {
	            renderTo: 'grafik_kumulatif_keseluruhan',
	            type: 'column',
	            zoomType: 'x',	
	            resetZoomButton: {
	            	relativeTo: 'chart'
	            } 	                        
	        },
	        title: {
	            text: 'Distribusi Nilai Mahasiswa (<b>semua yang diterima</b>)',    
				style: {
					fontSize: '16px'					
				}	                  
	        },
			legend: {
				enabled: false
			},	        
	        xAxis: {
	        	title: {
	        		text: 'Nilai'
	        	},
				plotLines: [{                                        
                    color: 'black',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                       	  
                    },
                    zIndex: 5
				}, {                    
                    color: 'blue',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                       	                 
                    },
                    zIndex: 5
                }, {                    
                    color: 'black',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                 	
                    },
                    zIndex: 5
                }, {                    
                    color: 'orange',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 20,                	
                    },
                    zIndex: 5
                }, {
                    color: 'green',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                	
                    },
                    zIndex: 5                	
                }, {
                    color: 'red',
                    dashStyle: 'solid',
                    width: 1,
                    label: {
                		rotation: 0,
	                    text: 'Plot line',
	                    verticalAlign: 'top',
	                    textAlign: 'left',
	                    y: 10,                	
                    },
                    zIndex: 5                	
                }
                ],
	        },
	        yAxis: {	        	
	            title: {
	                text: 'Jumlah Nilai'
	            },
	        },
	        tooltip: {	 
	            // positioner: function () {
	            //     return { x: 80, y: 50 };
	            // },
	            pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
	                '<td style="style="color:{series.color}; padding:0"><b>{point.y}</b></td></tr>',	// pointFormat: 'Jumlah: {point.y}'
	        },
	        plotOptions:{
	            series: {
	                pointWidth: 22,
					connectNulls: true,					
	            }
	        },
	        series: [{
	        	
	        }]			
		}		

		function get_grafik_keseluruhan_kumulatif(){
			// $('#grafik_kumulatif_keseluruhan').loader('show');			
			return $.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_keseluruhan_kumulatif',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	jurusan: $('#inputJurusan1').val()
		        },
		        success: function(response){
		        	if(response){
						options_3.xAxis.plotLines[0].value = response['q1PlotLines'];
						options_3.xAxis.plotLines[0].label.text = '<b>Qb:</b> '+response['q1PlotLines'];

						options_3.xAxis.plotLines[1].value = response['medianPlotLines'];
						options_3.xAxis.plotLines[1].label.text = '<b>Median:</b> '+response['medianPlotLines'];

						options_3.xAxis.plotLines[2].value = response['q3PlotLines'];
						options_3.xAxis.plotLines[2].label.text = '<b>Qa:</b> '+response['q3PlotLines'];

						options_3.xAxis.plotLines[3].value = response['meanPlotLines'];
						options_3.xAxis.plotLines[3].label.text = '<b>Mean:</b> '+response['meanPlotLines'];					

						options_3.xAxis.plotLines[3].value = response['meanPlotLines'];
						options_3.xAxis.plotLines[3].label.text = '<b>Mean:</b> '+response['meanPlotLines'];

						options_3.xAxis.plotLines[4].value = response['min_extreme'];
						options_3.xAxis.plotLines[4].label.text = '<b>Min:</b> '+response['min_extreme'];

						options_3.xAxis.plotLines[5].value = response['max_extreme'];
						options_3.xAxis.plotLines[5].label.text = '<b>Max:</b> '+response['max_extreme'];

						options_3.xAxis[0] = response['axis'];
						options_3.series[0] = response['nilai'];
						// options_3.series[0].step = 'right';


						options_3.xAxis.min = response['min_extreme'];
						options_3.xAxis.max = response['max_extreme'];					
						charts = new Highcharts.Chart(options_3);		        	
		        	}
					if(response['axis'] !== ''){
				        charts.renderer.label('Total Mahasiswa Yang Diterima: <b>' + response['total_kumulatif'] + '</b>', 780, 70)
			            .css({
			                fontSize: '11px',
			                color: '#000',
			                border: '1px solid #E3E3E3',
			            })
			            .attr({
			                fill: 'rgb(180, 228, 248)',				                
			                padding: 8,
			                r: 5,
			                zIndex: 6,
			            })
			            .add();							
					}		        	
					// $('#grafik_kumulatif_keseluruhan').loader('hide');					
		        }
			})		
		}

		option_4 = {
			chart: {
				renderTo: 'kumulatif_nilai_reg',
				type: 'line',				
				zoomType: 'x',
	            resetZoomButton: {
	            	relativeTo: 'chart'
	            }					            
			},
			// colors: ['#5BC0EB', '#6EEB83', '#DDDF00', '#FF5714', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Distribusi Nilai Kumulatif (<b>hanya yang registrasi</b>)',
				style: {
					fontSize: '16px'					
				} 
			},
			legend: {
				enabled: false
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
			// $('#kumulatif_nilai_reg').loader('show');
			return $.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_nilai_kumulatif_reg',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	jurusan: $('#inputJurusan1').val(),			        	
		        },
		        success: function(response){
		        	if(response !== ''){
						option_4.xAxis.categories = response['axis'];						
						option_4.series[0] = response['cummulative'];	
						option_4.series[0].step = 'right';						
						chart = new Highcharts.Chart(option_4);		        		
		        	}
					// $('#kumulatif_nilai_reg').loader('hide');
		        }
			});			 
		}

		option_5 = {
			chart: {
				renderTo: 'kumulatif_nilai_all',
				type: 'line',				
				zoomType: 'x',
	            resetZoomButton: {
	            	relativeTo: 'chart'
	            }					            
			},
			// colors: ['#5BC0EB', '#6EEB83', '#DDDF00', '#FF5714', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
			title: {
				text: 'Distribusi Nilai Kumulatif (<b>semua yang diterima</b>)',
				style: {
					fontSize: '16px'					
				}				
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
			// $('#kumulatif_nilai_all').loader('show');
			return $.ajax({
		        url: '<?php echo base_url(); ?>index.php/statistik_pmb/get_nilai_kumulatif_all',
		        type: 'POST',
		        dataType: 'JSON',
		        data: {
		        	jurusan: $('#inputJurusan1').val(),			        	
		        },	
		        success: function(response){
		        	if(response !== ''){
						option_5.xAxis.categories = response['axis'];
						option_5.series[0] = response['cummulative'];	
						option_5.series[0].step = 'right';						
						chart = new Highcharts.Chart(option_5);			        		
		        	}
					// $('#kumulatif_nilai_all').loader('hide');
		        }			
			})
		}				

	})
</script>
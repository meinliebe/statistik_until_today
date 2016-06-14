<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?><!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Universitas Muhammadiyah Surakarta</title>	
	<link rel="shortcut icon" href="<?php echo base_url()?>assets/img/favicon.ico">	
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/bootstrap.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/bootstrap.min.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/bootstrap-datetimepicker.min.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/select2.css">		
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/handsontable.full.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/css/jquery.loader.css">
	<link rel="stylesheet" href="<?php echo base_url()?>assets/DataTables/datatables.css">

	<!-- <link href='https://fonts.googleapis.com/css?family=Oswald' rel='stylesheet' type='text/css'> -->
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/jquery-ui.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/bootstrap.js"></script>    
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/moment.js"></script>    
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/bootstrap-datetimepicker.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/inputmask.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.inputmask.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/inputmask.date.extensions.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/select2.js"></script>	
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/handsontable.full.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/bootbox.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/jquery.loader.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/DataTables/datatables.js"></script>	
	<style type="text/css">
		body {
			background-image: url('<?php echo base_url()?>/assets/img/vbox_gradient.png');
			background-repeat: repeat-x;			
			/*background-image: url('<?php echo base_url()?>/assets/img/gradient-background.jpg');*/
		}
		@font-face {
			font-family: Oswald;
			src: url('<?php echo base_url()?>/assets/fonts/Oswald-Regular.ttf');
		}
		.myChart {
			margin: auto 0;
		}

		.row-chart{
			margin-top: 25px;
		}
		.stat-logo{
			display: inline-block;
			position: static;		    
			text-align: center;    
		}
		.stat-name{
			font-family: 'Oswald';
			margin-left: 30px;
			vertical-align: middle;		    
			display: inline-block;
			color: #183861;
		}	
		.tab-content{
			margin-top: 20px;
		}	
		.nav-pills > li > a, .navbar-brand {
			padding-top: 4px !important; 
			padding-bottom: 0 !important;
			height: 28px;
		}
		.navbar {
			min-height: 28px !important;
		}
		.bootbox-alert{
			top: 30%;
		}		
	</style>	
</head>
<body>
	<div class="container">
		<header>
			<div class="row-fluid" style="margin: 25px;">
				<div class="stat-logo">
					<img src="<?php echo base_url()?>/assets/img/logoUmsPng.png" alt="" style="height: 10%; width: 10%;">
					<div class="stat-name">
						<h2>UNIVERSITAS MUHAMMADIYAH SURAKARTA</h2>
						<h5>Jln. A. Yani Tromol Pos 1 Pabelan Kartasura Surakarta Jawa Tengah 57162</h5>					
						<h5>Telp. +62 271 717417 Faks. +62 271 715448</h5>				
					</div>
				</div>
				<!-- <div class="header-title"></div> -->
			</div>
		</header>
		<nav class="navbar navbar-default">
			<div class="container-fluid">
				<div id="navbar" class="navbar-collapse collapse">
					<ul class="nav nav-pills">
						<!-- <li id="menu1" class="active"><a class="menuTabel" href="#tableperbulan">Tabel Per Bulan</a></li> -->
						<li id="menu3" class="active"><a class="menuKeseluruhan" href="#">Distribusi Nilai</a></li>
						<li id="menu2"><a class="menuGrafik" href="#">Distribusi Secara Tanggal</a></li>
						<!-- <li id="menu1"><a class="menuKumulatif" href="#">Distribusi Nilai Kumulatif</a></li> -->
						<!-- <li><a href="#">Contact</a></li> -->
					</ul>
				</div><!--/.nav-collapse -->
			</div><!--/.container-fluid -->
		</nav>		
<!-- 			<div class='col-xs-2'>
				<div class="form-group">
					<div class='input-group date' id='datetimepicker1'>
						<input id="inputTglUjian" name="inputTglUjian" type='text' class="form-control input-sm" value="06/05/2015" data-toggle="tooltip" data-placement="top" title="Silakan pilih tanggal ujian"/>
						<span class="input-group-addon">
							<span class="glyphicon glyphicon-calendar"></span>
						</span>
					</div>
				</div>
			</div> -->
			
			<?php $this->load->view('view_grafik_keseluruhan'); ?>			
			<?php $this->load->view('view_grafik_kumulatif'); ?>			
			<!-- <?php $this->load->view('view_kumulatif_nilai'); ?> -->			
		</div>		
		<script>
			$(document).on('ready', function(){				
				$('.menuTabel').on('click', function(){					
					// $('.row-table').hide();													
					$('.row-keseluruhan').show();
					$('.row-grafik').hide();										
					$('.row-kumulatif-nilai').hide();

					// $('#menu1').addClass('active');
					$('#menu3').addClass('active');
					$('#menu2').removeClass('active');
					$('#menu1').removeClass('active');
				});	

				$.ajax({
					url: '<?php echo base_url(); ?>index.php/statistik_pmb/loadJurusan',
					type: 'POST',
					dataType: 'JSON',
					data: '',
					success: function(response){						
						append_data = '';
						append_data = '<option value="0"> - Semua Jurusan - </option>';
						$.each(response, function(idx, val){
							append_data += '<option value="'+val.FJurID+'">'+val.FProgdi+'</option>';	        		
						});
						$('#inputJurusan1').append(append_data);
						$('#inputJurusan2').append(append_data);
						$('#inputJurusan3').append(append_data);
					}
				});			
			});
		</script> 	
		<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/highchart/js/highcharts.js"></script>
		<script language="javascript" type="text/javascript" src="<?php echo base_url()?>assets/js/highchart/js/highcharts-more.js"></script>	

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
</body>
</html>
<style>
	.grafik_kumulatif{
		height: 400px;
		margin: auto;
		/*min-width: 310px;*/
		width: auto;		
	}
</style>
<div id="grafik_kumulatif" class="grafik_kumulatif" hidden="false"></div>

<script>
	$(document).on('ready', function(){

		var option_kumulatif = {
	        chart: {
				renderTo: 'grafik_kumulatif',	        		            
	        },

	        title: {
	            text: 'Grafik Kumulatif Penerimaan Mahasiswa Baru'
	        },

	        legend: {
	            enabled: false
	        },

	        xAxis: {
	            categories: ['1', '2', '3', '4', '5'],
	            title: {
	                text: 'Experiment No.'
	            }
	        },

	        yAxis: {
	            title: {
	                text: 'Observations'
	            }
	        },

	        series: [{
	        	type: 'boxplot',
	            name: 'Observations',
	            pointWidth: 40,
	            data: [
	                [760, 801, 848, 895, 965],
	                [733, 853, 939, 980, 1080],
	                [714, 762, 817, 870, 918],
	                [724, 802, 806, 871, 950],
	                [834, 836, 864, 882, 910]	                

	                // [760, 801, 848, 895, 965],
	                // [733, 853, 939, 980, 1080],
	                // [714, 762, 817, 870, 918],
	                // [724, 802, 806, 871, 950],
	                // [834, 836, 864, 882, 910]
	            ],
	            tooltip: {
	                headerFormat: '<em>Experiment No {point.key}</em><br/>'
	            }
	        }, {
	            type: 'spline',
	            name: 'Passing Grade',
	            data: [760, 801, 848, 895, 965],
	            lineWidth: 0.8,
	            color: '#AA4643',
	            tooltip: {
	                // valueSuffix: '°C'
	            }	        	
	        }]
		}
		chart = new Highcharts.Chart(option_kumulatif);		
	});
</script>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class statistik_pmb extends CI_Controller {

	public function __construct(){
		parent::__construct();
		// $this->load->library('curl');
		// $this->load->library('rest');
		$this->load->model('statistik_pmb_model');					
	}

	public function index(){
		// phpinfo();die();				
		// $data['grafikPerHari'] = $this->load->view('view_grafik_perhari');
		// $data['grafikPerKumulatif'] = $this->load->view('view_grafik_kumulatif');
		$this->load->view('view_statistik_pmb');
	}

	function loadJurusan(){
		$inputs = $this->input->post();
		$data = $this->statistik_pmb_model->loadJurusanModel($inputs);			
		echo json_encode($data->result());
	}	

	function get_grafik_perhari(){
		$ret_data = array();
		$jml_diterima = array();		
		$jml_daftar = array();
		$tgl_ujian = array();
		$tanggal = '';

		$inputs = $this->input->post();
		$data = $this->statistik_pmb_model->get_grafik_perhari_model($inputs);
		$getTgl = str_replace('/', '-', $inputs['inputTglUjian']);

		for ($i=5; $i >= 0; $i--) { 
			$ret_data['tgl_ujian'][] = date('d/m/Y', strtotime('-'.$i.'days', strtotime($getTgl)));
		}

		if (!empty($data)) {			
			foreach ($data as $key => $value) {			
				$jml_diterima[] = !empty($value[0]['JML_DITERIMA']) ? $value[0]['JML_DITERIMA']: 0;
				$jml_gagal[] = !empty($value[0]['JML_GAGAL']) ? $value[0]['JML_GAGAL']: 0;
				$jml_daftar[] = !empty($value[0]['JML_DAFTAR']) ? $value[0]['JML_DAFTAR']: 0;
				$tgl_ujian[] = !empty($value[0]['TGL_UJIAN']) ? $value[0]['TGL_UJIAN']: 0;
			}
		}			
		$ret_data['diterima']['name'] = 'Diterima';
		$ret_data['diterima']['data'] = $jml_diterima;
		$ret_data['tidakDiterima']['name'] = 'Tidak Diterima';
		$ret_data['tidakDiterima']['data'] = $jml_gagal;
		$ret_data['jumlahPendaftar']['name'] = 'Jumlah Pendaftar';
		$ret_data['jumlahPendaftar']['data'] = $jml_daftar;			
		// $ret_data['tgl_ujian'] = $tgl_ujian;			
		echo json_encode($ret_data);
	}

	function getWeekDays($month = '', $year = '', $is_bulanan = ''){		
		if($is_bulanan == 0){
			$p = new DatePeriod(
		        DateTime::createFromFormat('!Y', "2015"),
		        new DateInterval('P1D'),
		        DateTime::createFromFormat('!Y', "2015")->add(new DateInterval('P1Y'))
		    );
		} else {
		    $p = new DatePeriod(
		        DateTime::createFromFormat('!Y-n-d', "$year-$month-01"),
		        new DateInterval('P1D'),
		        DateTime::createFromFormat('!Y-n-d', "$year-$month-01")->add(new DateInterval('P1M'))
		    );			
		}
	    $datesByWeek = array();
	    foreach ($p as $d) {	    	
	        $dateByWeek[ $d->format('W') ][] = $d;
	    }
	    return $dateByWeek;
	}

	function load_tabel(){		
		$datesByWeek = array();
		$weekPeriods = array();
		$array_statistik = '';

		$inputs = $this->input->post();
		$month = !empty($inputs['month']) ? $inputs['month']: '';
		// $year = date('Y');
		$year = '2015';
		$jurusan = !empty($inputs['jurusan']) ? $inputs['jurusan']: '';
		$datesByWeek = $this->getWeekDays($month, $year, 1);	

		foreach ($datesByWeek as $week => $dates) {			
		    $firstD = $dates[0];
		    $lastD = $dates[count($dates)-1];						

		    $where_day_1 = $firstD->format('m/d/Y');
		    $where_day_2 = $lastD->format('m/d/Y');		
		    
		    $getVal = $this->statistik_pmb_model->load_tabel_model($where_day_1, $where_day_2, $jurusan);		    
		    // print_r($getVal);
		    if($getVal['statistik'] !== ''){
			    $array_statistik = $this->calc_statistik(($getVal['statistik']));				    
		    } else {
				$array_statistik['median'] = 0;
				$array_statistik['max'] = 0;
				$array_statistik['min'] = 0;
				$array_statistik['q1'] = 0;
				$array_statistik['q3'] = 0;
		    }

		    $returnValue[] = array(
		    	'periode' => $firstD->format('d M') . ' - ' . $lastD->format('d M'),
		    	'passing_grade' => $getVal['jumlah']['passing_grade'],
				'jml_daftar' => $getVal['jumlah']['jml_daftar'],
				'jml_diterima' => $getVal['jumlah']['jml_diterima'],
				'jml_reg' => $getVal['jumlah']['jml_reg'],
				'average_score' => number_format((float)$getVal['jumlah']['avg_score'], 2, '.', ''),
				'median_score' => $array_statistik['median'],
				'maximal_score' => $array_statistik['max'],
				'minimal_score' => $array_statistik['min'],
				'q1' => $array_statistik['q1'],
				'q3' => $array_statistik['q3'],
	    	);
		} 	

		// print_r($returnValue);
		echo json_encode($returnValue);
	}

	function get_grafik_kumulatif(){
		$inputs = $this->input->post();

		$tglDaftar = array();
		$data = array();
		$ret_value = array();

		$dataTglDaftar = $this->statistik_pmb_model->get_tanggal_pendaftaran_model();
		$datesByWeek = $this->getWeekDays();		

		foreach ($dataTglDaftar as $key => $value) {			
			$tglDaftar[] = $value['TGL_DAFTAR'];

			$expTgl = explode('/', $value['TGL_DAFTAR']);
			$axisTgl[] = $expTgl[0].'/'.$expTgl[2];
		}			
		
		foreach ($datesByWeek as $week => $dates) {
		    $firstD = $dates[0];
		    $lastD = $dates[count($dates)-1];			
		    $tglDaftar_2[] = $firstD->format('m/d/Y').' - '.$lastD->format('m/d/Y');
		}

		$data = $this->statistik_pmb_model->get_grafik_kumulatif_model($tglDaftar, $inputs);			
		foreach ($data as $key => $value) {			
			$jml_daftar[] = !empty($value[0]['JML_DAFTAR']) ? intval($value[0]['JML_DAFTAR']): 0;
            $jml_diterima[] = !empty($value[0]['JML_DITERIMA']) ? intval($value[0]['JML_DITERIMA']): 0;
            $jml_reg[] = !empty($value[0]['JML_REG']) ? intval($value[0]['JML_REG']): 0;
            $jml_mundur[] = !empty($value[0]['JML_MUNDUR']) ? intval($value[0]['JML_MUNDUR']): 0;
		}			

		// $ret_data['tgl_daftar'] = array_unique($axisTgl);			
		$ret_data['tgl_daftar'] = $tglDaftar;
		$ret_data['jml_daftar']['name'] = 'Jumlah Pendaftar';
		$ret_data['jml_daftar']['data'] = $jml_daftar;
		$ret_data['jml_diterima']['name'] = 'Jumlah Diterima';
		$ret_data['jml_diterima']['data'] = $jml_diterima;
		$ret_data['jml_reg']['name'] = 'Jumlah Registrasi';
		$ret_data['jml_reg']['data'] = $jml_reg;
		$ret_data['jml_mundur']['name'] = 'Jumlah Mundur';
		$ret_data['jml_mundur']['data'] = $jml_mundur;		

		echo json_encode($ret_data);
	}

	function calc_statistik($array) {			
	    $return = array(
	        'lower_outlier' => 0,
	        'min' => 0,
	        'q1' => 0,
	        'median' => 0,
	        'q3' => 0,
	        'max' => 0,
	        'higher_outlier' => 0,
	    );
	    $array_count = count($array);	    
	    sort($array, SORT_NUMERIC);	    

	    $return['min'] = !empty($array) ? $array[0]: 0;
	    $return['lower_outlier'] = $return['min'];
	    $return['max'] = !empty($array) ? $array[$array_count - 1]: 0;
	    $return['higher_outlier'] = $return['max'];
	    $middle_index = !empty($array) ? floor($array_count / 2): 0;
	    $return['median'] = !empty($array) ? $array[$middle_index]: 0; // Assume an odd # of items
	    $lower_values = array();
	    $higher_values = array();	    
	    // If we have an even number of values, we need some special rules	    
	    if ($array_count % 2 == 0) {	    	
	        // Handle the even case by averaging the middle 2 items
	        $return['median'] = !empty($array) ? round(($return['median'] + $array[$middle_index - 1]) / 2): 0;
	        foreach ($array as $idx => $value) {
		        // We need to remove both of the values we used for the median from the lower values	            	
	            if ($idx < ($middle_index - 1)) {	            	
	            	$lower_values[] = $value; 
	            } elseif ($idx > $middle_index){	            	
					$higher_values[] = $value;
	            } elseif ($idx == $middle_index) {
		            $lower_values[] = 0;	            	
		            $higher_values[] = 0;	            	
	            }
	        }	    	
	    } else {			    	    	
	        foreach ($array as $idx => $value) {	        	
	            if ($idx < $middle_index){
	            	$lower_values[]  = $value;
	            } elseif ($idx > $middle_index){
	            	$higher_values[] = $value;
	            } elseif ($idx == $middle_index) {
		            $lower_values[] = 0;	            	
		            $higher_values[] = 0;	            	
	            }
	        }
	    }

	    $lower_values_count = count($lower_values);	
	    $lower_middle_index = floor($lower_values_count / 2);
		
	    $return['q1'] = !empty($array) ? $lower_values[$lower_middle_index]: 0;
	    if($lower_values_count % 2 == 0 && !empty($array)){
	    	if($lower_middle_index !== 0){
		        $return['q1'] = round(($return['q1'] + $lower_values[$lower_middle_index - 1]) / 2);	    		
	    	} 
	    }

	    $higher_values_count = count($higher_values); 
	    $higher_middle_index = floor($higher_values_count / 2);
	    $return['q3'] = !empty($array) ? $higher_values[$higher_middle_index]: 0;
	    if ($higher_values_count % 2 == 0 && !empty($array)){
	        $return['q3'] = round(($return['q3'] + $higher_values[$higher_middle_index - 1]) / 2);
	    }

	    // Check if min and max should be capped
	    // $iqr = $return['q3'] - $return['q1']; // Calculate the Inner Quartile Range (iqr)
	    // if ($return['q1'] > $iqr)                  $return['min'] = $return['q1'] - $iqr;
	    // if ($return['max'] - $return['q3'] > $iqr) $return['max'] = $return['q3'] + $iqr;	    	    

	    return $return;
	}

	function plot_line_value($array) {
	    $return = array(
	        'lower_outlier'  => 0,
	        'min'            => 0,
	        'q1'             => 0,
	        'median'         => 0,
	        'q3'             => 0,
	        'max'            => 0,
	        'higher_outlier' => 0,
	    );

	    $array_count = count($array);
	    sort($array, SORT_NUMERIC);

	    $return['min']            = $array[0];
	    $return['lower_outlier']  = $return['min'];
	    $return['max']            = $array[$array_count - 1];
	    $return['higher_outlier'] = $return['max'];
	    $middle_index             = floor($array_count / 2);
	    $return['median']         = $array[$middle_index]; // Assume an odd # of items
	    $lower_values             = array();
	    $higher_values            = array();

	    // If we have an even number of values, we need some special rules
	    if ($array_count % 2 == 0) {
	        // Handle the even case by averaging the middle 2 items
	        $return['median'] = round(($return['median'] + $array[$middle_index - 1]) / 2);

	        foreach ($array as $idx => $value)
	        {
	            if ($idx < ($middle_index - 1)) $lower_values[]  = $value; // We need to remove both of the values we used for the median from the lower values
	            elseif ($idx > $middle_index)   $higher_values[] = $value;
	        }
	    } else {
	        foreach ($array as $idx => $value)
	        {
	            if ($idx < $middle_index)     $lower_values[]  = $value;
	            elseif ($idx > $middle_index) $higher_values[] = $value;
	        }
	    }

	    $lower_values_count = count($lower_values);
	    $lower_middle_index = floor($lower_values_count / 2);
	    // $return['q1']       = $lower_values[$lower_middle_index];
	    $return['q1']       = !empty($array) ? $lower_values[$lower_middle_index]: 0;
	    if ($lower_values_count % 2 == 0)
	        $return['q1'] = round(($return['q1'] + $lower_values[$lower_middle_index - 1]) / 2);

	    $higher_values_count = count($higher_values);
	    $higher_middle_index = floor($higher_values_count / 2);
	    // $return['q3']        = $higher_values[$higher_middle_index];
	    $return['q3']        = !empty($array) ? $higher_values[$higher_middle_index]: 0;
	    if ($higher_values_count % 2 == 0)
	        $return['q3'] = round(($return['q3'] + $higher_values[$higher_middle_index - 1]) / 2);

	    // Check if min and max should be capped
	    $iqr = $return['q3'] - $return['q1']; // Calculate the Inner Quartile Range (iqr)
	    if ($return['q1'] > $iqr)                  $return['min'] = $return['q1'] - $iqr;
	    if ($return['max'] - $return['q3'] > $iqr) $return['max'] = $return['q3'] + $iqr;

	    return $return;
	}	

	function get_grafik_keseluruhan(){
		$return_value = '';
		$fgrade_val = '';
		$mean = 0;	
		$cummulative = 0;
		$total = array();
		$total_mhs = 0;
		$is_reg = 1;	

		$return_value['jml_mhs']['name'] = '';
		$return_value['jml_mhs']['data'] = '';
		$return_value['fgrade'] = '';					

		$return_value['q1PlotLines'] = '';
		$return_value['medianPlotLines'] = '';
		$return_value['q3PlotLines'] = '';
		$return_value['min_extreme'] = '';
		$return_value['max_extreme'] = '';		
		$return_value['total_mhs'] = '';		
		$jml_mhs_fix = '';

		$inputs = $this->input->post();
		$data = $this->statistik_pmb_model->get_grafik_keseluruhan_model($inputs);
		// $data_kumulatif = $this->statistik_pmb_model->get_nilai_kumulatif_reg_model($inputs, $is_reg);			
		
		if(!empty($data['ret1']) && !empty($data['ret2'])){
			foreach ($data['ret1'] as $key => $value) {
				$jml_mhs[] = !empty($value[0]['JML_MHS']) ? intval($value[0]['JML_MHS']): null;
				$fgrade[] = !empty($value[0]['FGRADE']) ? intval($value[0]['FGRADE']): null;
				$total_mhs+=!empty($value[0]['JML_MHS']) ? intval($value[0]['JML_MHS']): 0;
				// $jml_mhs[] = !empty($value['JML_MHS']) ? $value['JML_MHS']: null;
				// $fgrade[] = !empty($value['FGRADE']) ? $value['FGRADE']: null;				
			}			
			
			foreach ($data['ret2'] as $key => $value) {
				$fgrade_val[] = $value['FGrade'];
			}

			$return_value['jml_mhs']['name'] = 'Jumlah Registrasi';
			$return_value['jml_mhs']['data'] = $jml_mhs;
			$return_value['fgrade'] = $fgrade;		

			$plotLines = $this->plot_line_value($fgrade_val);			
			foreach ($jml_mhs as $key => $value) {
				$jml_mhs_fix[] = $value * $key;
			}						
			$mean = array_sum($jml_mhs_fix) / array_sum($jml_mhs);			
			$return_value['q1PlotLines'] = $plotLines['q1'];
			$return_value['meanPlotLines'] = number_format($mean, 2);
			$return_value['medianPlotLines'] = $plotLines['median'];
			$return_value['q3PlotLines'] = $plotLines['q3'];
			$return_value['min_extreme'] = min($fgrade_val);
			$return_value['max_extreme'] = max($fgrade_val);
			$return_value['total_mhs'] = $total_mhs;
		}

		// foreach ($data_kumulatif as $key => $value) {
		// 	 $fgrade[] = !empty($value['FGRADE']) ? $value['FGRADE']: '';
		// 	 $cummulative += !empty($value['JML_MHS']) ? $value['JML_MHS']: '';
		// 	 $total[] = $cummulative;
		// }
		
		// $return_value['cummulative']['name'] = 'Kumulatif Nilai';
		// $return_value['cummulative']['data'] = $total;

		echo json_encode($return_value);
	}

	function get_keseluruhan_kumulatif(){
		$inputs = $this->input->post();
		// $dataTglDaftar = $this->statistik_pmb_model->get_tanggal_pendaftaran_model();	
		$retKumulatif = '';		
		$fgrade = '';
		$mean = 0;
		$dataKumulatif = '';
		$total_kumulatif = 0;
		$axis = '';
		$plotLines['q1'] = '';
		$plotLines['q3'] = '';
		$plotLines['median'] = '';
		$retKumulatif['min_extreme'] = '';
		$retKumulatif['max_extreme'] = '';	
		$retKumulatif['total_kumulatif'] = '';
		
		$data = $this->statistik_pmb_model->get_keseluruhan_kumulatif_model($inputs);	

		if(!empty($data['data1']) && !empty($data['data2'])){
			foreach ($data['data1'] as $key => $value) {					
				$dataKumulatif[] = !empty($value[0]['JML_NILAI']) ? intval($value[0]['JML_NILAI']): null;				
				$axis[] = !empty($value[0]['FGRADE']) ? intval($value[0]['FGRADE']): null;
				$total_kumulatif += !empty($value[0]['JML_NILAI']) ? intval($value[0]['JML_NILAI']): 0;
			}	

			foreach ($data['data2'] as $key => $value) {
				$fgrade[] = $value['FGrade'];
			}

			$plotLines = $this->plot_line_value($fgrade);	
			// $mean = array_sum($jml_mhs_fix) / array_sum($jml_mhs);						
			$mean = array_sum($fgrade) / array_sum($dataKumulatif);
		}
		
		$retKumulatif['nilai']['name'] = 'Jumlah nilai';
		$retKumulatif['nilai']['data'] = $dataKumulatif;
		$retKumulatif['axis'] = $axis;	
		$retKumulatif['q1PlotLines'] = $plotLines['q1'];
		$retKumulatif['meanPlotLines'] = number_format($mean, 2);
		$retKumulatif['medianPlotLines'] = $plotLines['median'];	
		$retKumulatif['q3PlotLines'] = $plotLines['q3'];
		$retKumulatif['min_extreme'] = !empty($fgrade) ? min($fgrade): 0;
		$retKumulatif['max_extreme'] = !empty($fgrade) ?  max($fgrade): 0;				
		$retKumulatif['total_kumulatif'] = $total_kumulatif;
		echo json_encode($retKumulatif);
	}

	function get_nilai_kumulatif_reg(){
		$inputs = $this->input->post();
		$is_reg = 1;	
		$total = array();
		$fgrade = array();
		$return = '';		

		$cummulative = 0;

		$data = $this->statistik_pmb_model->get_nilai_kumulatif_reg_model($inputs, $is_reg);

		foreach ($data as $key => $value) {
			 $fgrade[] = !empty($value['FGRADE']) ? $value['FGRADE']: '';
			 $cummulative += !empty($value['JML_MHS']) ? $value['JML_MHS']: '';
			 $total[] = $cummulative;
		}
		$return['axis'] = $fgrade;
		$return['cummulative']['name'] = 'Kumulatif Nilai';
		$return['cummulative']['data'] = $total;

		echo json_encode($return);
	}

	function get_nilai_kumulatif_all(){
		$inputs = $this->input->post();	
		$is_reg = 0;	
		$total = array();
		$fgrade = array();
		$cummulative = 0;		
		$return = '';	

		$data = $this->statistik_pmb_model->get_nilai_kumulatif_reg_model($inputs, $is_reg);

		foreach ($data as $key => $value) {
			 $fgrade[] = !empty($value['FGRADE']) ? $value['FGRADE']: '';
			 $cummulative += !empty($value['JML_MHS']) ? $value['JML_MHS']: '';
			 $total[] = $cummulative;			
		}
		$return['axis'] = $fgrade;
		$return['cummulative']['name'] = 'Kumulatif Nilai';
		$return['cummulative']['data'] = $total;

		echo json_encode($return);		

	}

	function get_nilai(){
		$config = array('server' => 'https://127.0.0.1/',
        //'api_key'         => 'Setec_Astronomy'
        //'api_name'        => 'X-API-KEY'
        //'http_user'       => 'username',
        //'http_pass'       => 'password',
        //'http_auth'       => 'basic',
        //'ssl_verify_peer' => TRUE,
        //'ssl_cainfo'      => '/certs/cert.pem'
			);

		$response = $this->rest->get('index.php/grafik_api/diterima');
		// print_r($response);
	}


	// function get_data(){		
	// 	$data = array();		

	// 	$dataDiterima = [49.9, 71.5, 106.4, 129.2, 144.0, 176.0, 135.6, 148.5, 216.4, 194.1, 95.6, 54.4];

	// 	$dataTidakDiterima = [83.6, 78.8, 98.5, 93.4, 106.0, 84.5, 105.0, 104.3, 91.2, 83.5, 106.6, 92.3];

	// 	$jumlahPendaftar = [48.9, 38.8, 39.3, 41.4, 47.0, 48.3, 59.0, 59.6, 52.4, 65.2, 59.3, 51.2];

	// 	for ($i=11; $i >= 0; $i--) { 
	// 		$data['x_axis'][$i] = date('d M Y', strtotime("-".$i."days"));
	// 	}

	// 	$data['diterima']['name'] = 'Diterima';
	// 	$data['diterima']['data'] = $dataDiterima;
	// 	$data['tidakDiterima']['name'] = 'Tidak Diterima';
	// 	$data['tidakDiterima']['data'] = $dataTidakDiterima;
	// 	$data['jumlahPendaftar']['name'] = 'Jumlah Pendaftar';
	// 	$data['jumlahPendaftar']['data'] = $jumlahPendaftar;
	// 	echo json_encode($data);
	// }
}

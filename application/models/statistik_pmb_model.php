<?php 

class Statistik_pmb_model extends CI_Model {

	function __construct() {       
		parent::__construct();
	}

	function loadJurusanModel($inputs){
		$query = $this->db->query("
			SELECT FJurID, FProgdi FROM AJurusan
			WHERE FJurID != '00'
			ORDER BY FProgdi ASC
		");
		return $query;
	}

	function get_grafik_perhari_model($inputs){

		$query = array();
		$inputNamaJurusan = !empty($inputs['inputNamaJurusan']) ? $inputs['inputNamaJurusan']: '';
		$inputTglUjian = !empty($inputs['inputTglUjian']) ? $inputs['inputTglUjian']: '';
		$getTgl = str_replace('/', '-', $inputTglUjian);

		$addWhere = '';
		$addSelect = '';
		$addGroup = '';

		if($inputNamaJurusan != 0){
			$addWhere .= "AND JURUSAN = '".$inputNamaJurusan."'";
			$addSelect .= ',JURUSAN';
			$addGroup .= ',JURUSAN';
		}

		// print_r($inputTglUjian);
		for ($i=5; $i >= 0; $i--) { 			
			$tanggal[$i] = date('m/d/Y', strtotime('-'.$i.'days', strtotime($getTgl)));				

			$query[$i] = $this->db->query("
				SELECT ISNULL(SUM(C.JML_DITERIMA), 0) AS JML_DITERIMA
					,ISNULL(SUM(C.JML_GAGAL), 0) AS JML_GAGAL
					,ISNULL(SUM(C.JML_DAFTAR), 0) AS JML_DAFTAR
					,ISNULL(TGL_UJIAN, 0) TGL_UJIAN	
					".$addSelect."
				FROM (
					SELECT CASE 
							WHEN FRECID IS NOT NULL
								AND FSTATUSTESTID = '12'
								THEN 1
							ELSE 0
							END AS JML_DITERIMA
						,CASE 
							WHEN FRECID IS NOT NULL
								AND FSTATUSTESTID = '9'
								THEN 1
							ELSE 0
							END AS JML_GAGAL
						,CASE 
							WHEN FRECID IS NOT NULL
								THEN 1
							ELSE 0
							END AS JML_DAFTAR
						,CONVERT(VARCHAR(10), FTanggalUjian, 103) TGL_UJIAN
						,A.FLulusJurusanID JURUSAN
					FROM ACAMA A
				   	--LEFT JOIN AJURUSAN B ON B.FJurID = A.FLulusJurusanID
					) AS C
				WHERE TGL_UJIAN = '".$tanggal[$i]."'
					".$addWhere."
				GROUP BY TGL_UJIAN ".$addGroup."
			")->result_array(); 
			// print_r($this->db->last_query());
		}				
		return $query;		
	}	

	function load_tabel_model($where_day_1, $where_day_2, $jurusan){
		$addWhere = '';
		if (!empty($jurusan)) {
			$addWhere = "AND FLulusJurusanID = '".$jurusan."'";
		}
		
		$query = $this->db->query("
			SELECT JML_DAFTAR
				,JML_DITERIMA
				,JML_REG
				,GRADE*2 AS GRADE
				,SCORE / JML_DAFTAR AVG_SCORE
				,MAX_SCORE
				,MIN_SCORE
			FROM (
				SELECT SUM(CASE 
							WHEN A.FRecID IS NOT NULL
								THEN 1
							ELSE 0
							END) AS JML_DAFTAR
					,SUM(CASE 
							WHEN FStatusTestID = '12'
								THEN 1
							ELSE 0
							END) AS JML_DITERIMA
					,SUM(CASE 
							WHEN FRegistrasi = '1'
								THEN 1
							ELSE 0
							END) AS JML_REG
					,(
						SELECT FGrade*2
						FROM ACama
						WHERE FTglDaftar = '$where_day_1'
						) GRADE
					,SUM(B.FGrade*2) SCORE
					,MAX(B.FGrade*2) MAX_SCORE
					,MIN(B.FGrade*2) MIN_SCORE
				FROM ACama A
				LEFT JOIN AHasiltes B ON B.FMhsID = A.FMhsID
				WHERE FTglDaftar BETWEEN '$where_day_1' AND '$where_day_2' $addWhere	
			) C			
		")->result_array();
		
		$getStatistik = $this->db->query("
			SELECT 
			B.FGRADE*2 AS FGRADE
			,SUM(CASE 
				WHEN B.FMhsID IS NOT NULL
					THEN 1 ELSE 0 END) JML_MHS
			FROM (
				SELECT A.FMhsID				
					,A.FGrade
					,A.FLulusJurusanID
					,A.FTglDaftar
				FROM ACama A
				WHERE A.FRegistrasi = 1
				GROUP BY A.FMhsID
					,A.FTglDaftar
					,A.FGrade
					,A.FLulusJurusanID
			) B
			WHERE B.FGRADE IS NOT NULL
				AND B.FTglDaftar BETWEEN '$where_day_1' AND '$where_day_2' $addWhere
			GROUP BY B.FGRADE
				ORDER BY B.FGRADE ASC
	  	")->result_array();						
	  	
		foreach ($query as $key => $value) {
			$ret_val['jumlah'] = array(
				'jml_daftar' => $value['JML_DAFTAR'],
				'passing_grade' => $value['GRADE'],
				'jml_diterima' => $value['JML_DITERIMA'],
				'jml_reg' => $value['JML_REG'],
				'avg_score' => $value['AVG_SCORE'],
			);
		}

		$ret_val['statistik'] = array();

		if(!empty($getStatistik)){
			foreach ($getStatistik as $key => $value) {
				$ret_val['statistik'][] = $value['FGRADE'];
			}			
		}

		return $ret_val;
	}	

	function get_tanggal_pendaftaran_model(){
		$query = $this->db->query("
			SELECT DATE_FORMAT(FTglDaftar,'%Y/%m/%d') TGL_DAFTAR FROM ACama 
				GROUP BY DATE_FORMAT(FTglDaftar,'%Y/%m/%d')
					ORDER BY DATE_FORMAT(FTglDaftar,'%Y/%m/%d') ASC
		")->result_array();		
		return $query;
	}

	function get_grafik_kumulatif_model($tglDaftar, $inputs){
		$inputJurusan2 = !empty($inputs['inputJurusan2']) ? $inputs['inputJurusan2']: '';
		$addWhere = '';
		if(!empty($inputJurusan2)){
			$addWhere = "AND FLulusJurusanID = '".$inputJurusan2."'";
		}
		for ($i = 0; $i < count($tglDaftar); $i++) {
			$query[$i] = $this->db->query("
				SELECT COUNT(A.NoDaftar) JML_DAFTAR
					,SUM(CASE 
							WHEN FStatusTestID = '12'
								THEN 1
							ELSE 0
							END) AS JML_DITERIMA
					,SUM(CASE 
							WHEN FRegistrasi = '1'
								THEN 1
							ELSE 0
							END) AS JML_REG
					,SUM(CASE 
							WHEN FStatusTestID = '11'
								THEN 1
							ELSE 0
							END) AS JML_MUNDUR			
				FROM ACama A 
				WHERE A.FTglDaftar BETWEEN '2015/01/01' 
					AND '".$tglDaftar[$i]." 23:59:59'				
				$addWhere
			")->result_array();			
		}		
		return $query;   
	}

	function getMaxGradeReg($addWhere){
		$query = $this->db->query("
			SELECT A.FGrade*2 FGrade
			FROM ACama A
			WHERE A.FRegistrasi = 1
				AND A.FGRADE IS NOT NULL
				$addWhere
			ORDER BY A.FGRADE ASC			
		")->result_array();		
		return $query;
	}	

	function getMaxGradeNilaiKumulatif($addWhere){
		$query = $this->db->query("
			SELECT A.FGrade*2 FGrade
			FROM ACama A
			WHERE A.FGRADE IS NOT NULL
				$addWhere			
			ORDER BY A.FGRADE ASC			
		")->result_array();

		return $query;
	}

	function get_grafik_keseluruhan_model($inputs){
		$jurusan = !empty($inputs['jurusan']) ? $inputs['jurusan']: '';
		$retValue = array();
		$addWhere = '';
		$query = '';
		$retValue['ret1'] = array();
		$retValue['ret2'] = array();
		if($jurusan){
			$addWhere .= "AND A.FLulusJurusanID = '".$jurusan."'";
		}

		$getMaxGrade = $this->getMaxGradeReg($addWhere);

		$getFirstTgl = $this->db->query("
			SELECT DATE_FORMAT(FTglDaftar, '%d/%m/%Y') TANGGAL
			FROM ACAMA
			ORDER BY FTglDaftar ASC
			LIMIT 1			
		")->result_array();

		$firstDay = '';
		$today = date('Y/m/d');
		$yesterday = date('Y/m/d',strtotime("-1 days"));

		foreach ($getFirstTgl as $key => $value) {
			$firstDay = $value['TANGGAL'];
		}
		//this is first day

		if($getMaxGrade){
			foreach ($getMaxGrade as $key => $value) {
				$maxAxis = max($value);
				$minAxis = min($value);
			}						
			// $query = $this->db->query("
			// 	SELECT B.FGrade FGRADE
			// 		,SUM(CASE 
			// 				WHEN B.FMhsID IS NOT NULL
			// 					THEN 1
			// 				ELSE 0
			// 				END) AS JML_MHS
			// 	FROM (
			// 		SELECT ISNULL(A.FMhsID, '') FMhsID
			// 			,ISNULL(A.FGrade*2, '') FGrade
			// 			,ISNULL(A.FLulusJurusanID, '') FLulusJurusanID
			// 		FROM ACama A
			// 		WHERE A.FRegistrasi = 1
			// 			AND A.FGrade <> 0
			// 			$addWhere
			// 		) B
			// 	WHERE B.FGRADE IS NOT NULL					
			// 	GROUP BY FGRADE
			// 	ORDER BY FGRADE ASC
			// ")->result_array();
			for ($i=0; $i <= $maxAxis; $i++) {
				$query[] = $this->db->query("
					SELECT $i as FGRADE
						,SUM(CASE 
								WHEN B.FMhsID IS NOT NULL
									THEN 1
								ELSE 0
								END) AS JML_MHS
					FROM (
						SELECT IFNULL(A.FMhsID, '') FMhsID
							,IFNULL(A.FGrade*2, '') FGrade
							,IFNULL(A.FLulusJurusanID, '') FLulusJurusanID
						FROM ACama A
						WHERE A.FRegistrasi = 1
							AND A.FGrade <> 0
							$addWhere
						) B
					WHERE B.FGRADE IS NOT NULL
						AND B.FGRADE = $i
			  	")->result_array();	
			  	// print_r($this->db->last_query());
			}			
		}

		$retValue['ret1'] = $query;
		$retValue['ret2'] = $getMaxGrade;
		return $retValue;
	}

	function get_keseluruhan_kumulatif_model($inputs){
		$jurusan = !empty($inputs['jurusan']) ? $inputs['jurusan']: '';
		$grade = '';
		$ret_val = array();
		$addWhere = '';		
		$query = '';		
		if($jurusan){
			$addWhere .= "AND A.FLulusJurusanID = '".$jurusan."'";
		}

		$grade = $this->getMaxGradeNilaiKumulatif($addWhere);

		if($grade){
			foreach ($grade as $key => $value) {
				$maxGrade = max($value);
			}			

			for ($i=0; $i <= $maxGrade; $i++) { 
				$query[$i] = $this->db->query("
					SELECT $i AS FGRADE
						,CASE
							WHEN SUM(NILAI) IS NULL
								THEN 0
							ELSE SUM(NILAI) END AS JML_NILAI
					FROM (
						SELECT FGRADE*2 FGRADE
							,CASE 
								WHEN FGRADE IS NOT NULL
									THEN 1
								ELSE 0
								END AS NILAI
						FROM ACama A
						WHERE A.FGrade <> 0
							AND A.FGrade*2 = $i
							$addWhere
						) C
					ORDER BY FGRADE ASC
				")->result_array();		
				// print_r($this->db->last_query());
			}
		}
		$ret_val['data1'] = $query;
		$ret_val['data2'] = $grade;
		return $ret_val;
	}

	function get_nilai_kumulatif_reg_model($inputs, $is_reg){
		$jurusan = !empty($inputs['jurusan']) ? $inputs['jurusan']: '';
		$addWhere = '';
		if($jurusan){
			$addWhere .= "AND A.FLulusJurusanID = '".$jurusan."'";
		}
		if($is_reg == 1){
			$addWhere .= "AND A.FRegistrasi = '".$is_reg."'";
		}

		$query = $this->db->query("
			SELECT B.FGRADE * 2 AS FGRADE
				,SUM(CASE 
						WHEN B.FMhsID IS NOT NULL
							THEN 1
						ELSE 0
						END) JML_MHS
			FROM (
				SELECT A.FMhsID
					,A.FGrade
					,A.FLulusJurusanID
					,A.FTglDaftar
				FROM ACama A
				WHERE A.FGrade IS NOT NULL
					$addWhere
				GROUP BY A.FMhsID
					,A.FTglDaftar
					,A.FGrade
					,A.FLulusJurusanID		
				) B
			WHERE B.FGRADE IS NOT NULL
			GROUP BY B.FGRADE
			ORDER BY B.FGRADE ASC				
		")->result_array();
		return $query;
	}

}
?>
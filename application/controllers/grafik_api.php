<?php 
require APPPATH . '/libraries/REST_Controller.php';
/**
* 
*/
class grafik_api extends REST_Controller {

	function __construct() {       
		parent::__construct();
		$this->load->model('statistik_pmb_model');
	}

	public function diterima_get() {
		$get_terima = $this->statistik_pmb_model->diterima_get_model();

		if (!empty($get_terima)) {
            $this->set_response($get_terima, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        } else {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Data Tidak Ditemukan'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
	}
}
?>
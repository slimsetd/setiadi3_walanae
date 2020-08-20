<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//http://stackoverflow.com/questions/18382740/cors-not-working-php
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
}

// Access-Control headers are received during OPTIONS requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
        header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

class Setiadi extends CI_Controller {

	public function __construct() {
		parent::__construct();
		//error_reporting(1);
		date_default_timezone_set("Asia/Jakarta"); 
		$postdata = file_get_contents("php://input");
		$this->request = json_decode($postdata);
		// $this->load->model('Model_api');
	}

	public function index() {
		echo "string";
	}
	//Get All Repository 
	public function getAll(){
		
		$buku = $this->db->select('A.*, B.item_code, A.location')
			->join('item B', 'A.biblio_id = B.biblio_id', 'left')
			->join('loan C', 'B.item_code = C.item_code', 'left')
			->group_by('A.title')
			->get('search_biblio A')->result();

		foreach ($buku as $row) {
			$url = 'http://demosetiadi.slimsetd.id/images/docs/'.$row->image;
			$resp = get_headers($url);
			if ($resp[0] == 'HTTP/1.1 404 Not Found' || $row->image == '') {
				$row->image = 'http://demosetiadi.slimsetd.id/lib/minigalnano/createthumb.php?filename=..%2F..%2Fimages%2Fdocs%2Faspek_hukm.JPG&width=120';
			} else {
				$row->image = $url;
			}
			$item = $this->db->get_where('item', array('biblio_id' => $row->biblio_id))->result();
			$row->list_item = $item; 
		}
		echo json_encode($buku);
	}

	//Get Repo with LIMIT PAGE 
	public function getRepository($page) {
		$limit = 6;
		$offset = ($page - 1) * $limit; 
		$buku = $this->db->select('A.*, B.item_code, A.location')
			->join('item B', 'A.biblio_id = B.biblio_id', 'left')
			->join('loan C', 'B.item_code = C.item_code', 'left')
			->group_by('A.title')
			->get('search_biblio A', $limit, $offset)->result();

		foreach ($buku as $row) {
			$url = 'http://demosetiadi.slimsetd.id/images/docs/'.$row->image;
			$resp = get_headers($url);
			if ($resp[0] == 'HTTP/1.1 404 Not Found' || $row->image == '') {
				$row->image = 'http://demosetiadi.slimsetd.id/lib/minigalnano/createthumb.php?filename=..%2F..%2Fimages%2Fdocs%2Faspek_hukm.JPG&width=120';
			} else {
				$row->image = $url;
			}
			$item = $this->db->get_where('item', array('biblio_id' => $row->biblio_id))->result();
			$row->list_item = $item; 
		}
		echo json_encode($buku);
	}
// Seacrh Title
	public function search()
    {
        $biblio_id = $this->get('biblio_id');
		$title = $this->get('title');
		if ($biblio_id != null || $biblio_id !=''){
            $list = $this->db->get_where('search_biblio', array('biblio_id'=>$biblio_id))->result();
            $buku = $this->db->get_where('biblio', array('biblio_id'=>$biblio_id))->result();
            $this->response($buku);
			
		} else if ($title != null || $title !=''){
            $buku = $this->db->select('A.*, B.item_code, A.location, C.is_lent')
			//->where('C.is_return', '1')->where('D.item_code IS NULL')
			->join('item B', 'A.biblio_id = B.biblio_id', 'left')
			->join('loan C', 'B.item_code = C.item_code', 'left')
            ->group_by('A.title')
            ->like('title', $title)
			->order_by('A.biblio_id', 'DESC')
			->get('search_biblio A')->result();

		foreach ($buku as $row) {
			$url = 'http://localhost/perpus/images/docs/'.$row->image;
			$resp = get_headers($url);
			if ($resp[0] == 'HTTP/1.1 404 Not Found' || $row->image == '') {
				$row->image = 'http://localhost/perpus/lib/minigalnano/createthumb.php?filename=..%2F..%2Fimages%2Fdocs%2Faspek_hukm.JPG&width=120';
			} else {
				$row->image = $url;
			}
			$item = $this->db->get_where('item', array('biblio_id' => $row->biblio_id))->result();
			$row->list_item = $item; 
		}
			
            
            $this->response($buku);
		

		}else {
            $buku = $this->db->get('biblio')->result();	
            $this->response($buku);
	}
}
	
	
}
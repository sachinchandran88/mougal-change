<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Programs extends My_Controller {
	
	public function __construct() {
		parent::__construct();
		
		if( !$this->session->userdata('logged_in') ) {
			redirect('admin/login');
		}
	}
	
	public function index() {
		
		$this->data['page_title'] = 'Programs';
		$this->data['menu'] = 'programs';
		$this->data['submenu'] = '';
		
		$this->data['css_files'] = array(
			//base_url('assets/plugins/datatables/datatables.min.css'),
		);
		
		$this->data['js_files'] = array(
			base_url('assets/plugins/datatables/datatables.min.js'),
			base_url('assets/js/admin/programs.js?ver=1.0.0'),
		);
		
		$this->load->view('admin/templates/header',$this->data);
		$this->load->view('admin/programs/index',$this->data);
		$this->load->view('admin/templates/footer',$this->data);
	}
	
	
	public function get_all()	{
		
		$keyword = '';
		if( isset( $_REQUEST['search']['value'] ) && $_REQUEST['search']['value'] != '' ) {
			$keyword = $_REQUEST['search']['value'];
		}
		
		$join_arr_left = [
			'weighted_types t1' => 't1.id = t.weighted_type',
			'gender_types t2' => 't2.id = t.gender_type',
		];
		
		$condition = '';
		$iTotalRecords = $this->common->get_total_count( 'programs t', $condition, $join_arr_left );

		$iDisplayLength = intval($_REQUEST['length']);
		$iDisplayLength = $iDisplayLength < 0 ? $iTotalRecords : $iDisplayLength;
		$iDisplayStart = intval($_REQUEST['start']);
		$sEcho = intval($_REQUEST['draw']);

		$records = array();
		$records["data"] = array();

		$limit = $iDisplayLength;
		$offset = $iDisplayStart;

		$columns = array(
			0 => 't.title',			 
			1 => 't1.title',
			2 => 't2.title',
			3 => 't.days',
		);

		$order_by = $columns[$_REQUEST['order'][0]['column']];
		$order = $_REQUEST['order'][0]['dir'];
		$sort = $order_by.' '.$order;
		$result = $this->common->get_all( 'programs t', $condition, 't.*, t1.title w_type, t2.title g_type', $sort, $limit, $offset, $join_arr_left);
		 
		 foreach( $result as $row ) {

			$records["data"][] = array(
				$row->title,
				$row->w_type,
				$row->g_type,
				$row->days,
				'<a href="javascript:;" class="delete-program" data-url="'.site_url('admin/programs/delete/'.$row->id).'">delete</a> | 
				<a href="'.site_url('admin/programs/edit/'.$row->id).'">edit</a>'
			);
		}

		$records["draw"] = $sEcho;
		$records["recordsTotal"] = $iTotalRecords;
		$records["recordsFiltered"] = $iTotalRecords;

		header('Content-type: application/json');
		echo json_encode($records);
	}
	
	public function create( ) {
		$this->data['page_title'] = 'Programs';
		$this->data['menu'] = 'programs';
		$this->data['submenu'] = '';
		
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		if( $_POST ) {
			if( $this->form_validation->run() ) {
				//save logic
				$data_arr = [
					'title' 			=> trim( $this->input->post('title') ),
					'weighted_type' 	=> trim( $this->input->post('weighted_type') ),
					'gender_type' 		=> trim( $this->input->post('gender_type') ),
					'days' 				=> trim( $this->input->post('days') ),
				];
				$programId = $this->common->insert( 'programs', $data_arr);

				for( $i = 1; $i < $this->input->post('days') + 1; $i++ ) {
					$days_arr = [
						'program_id' => $programId,
						'day' => $i
					];
					$this->common->insert( 'program_days', $days_arr);
				}
				
				$this->session->set_flashdata( 'success', 'Program Created!' );
				redirect('/admin/programs/day/1/'.$programId);
			}
		}
		
		$this->load->view('admin/templates/header',$this->data);
		$this->load->view('admin/programs/create',$this->data);
		$this->load->view('admin/templates/footer',$this->data);
	}

	public function edit( $id = 0 ) {
		$this->data['page_title'] 	= 'Programs';
		$this->data['menu'] 		= 'programs';
		$this->data['submenu'] 		= '';
		
		$row = $this->common->get('programs', ['id' => $id], 'array');
		if( empty($row) ) {
			redirect('programs');
		}
		$this->data['row'] = $row;

		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		if( $_POST ) {
			if( $this->form_validation->run() ) {
				
				$data_arr = [
					'title' 			=> trim( $this->input->post('title') ),
					'weighted_type' 	=> trim( $this->input->post('weighted_type') ),
					'gender_type' 		=> trim( $this->input->post('gender_type') ),
					'days' 				=> trim( $this->input->post('days') ),
				];
				$this->common->update( 'programs', $data_arr, ['id' => $id]);

				if( $row['days'] > $this->input->post('days') ) {
					$arr_days = $this->common->get_all( 'program_days', ['program_id' => $id, 'days >' => $this->input->post('days')] );
					foreach( $arr_days as $day ) {
						$this->common->delete('program_days', ['id' => $day->id]);
					}
				}	else if( $row['days'] < $this->input->post('days') ) {
					for( $i = $row['days'] + 1; $i < $this->input->post('days') + 1; $i++ ) {
						$days_arr = [
							'program_id' => $id,
							'day' => $i
						];
						$this->common->insert( 'program_days', $days_arr);
					}
				}
				
				$this->session->set_flashdata('success', 'Program updated successfully');
				redirect('/admin/programs/day/1/'.$id);
			} else {
				$this->data['row'] = array_merge($this->data['row'], $_POS);
			}
		}

		$this->load->view('admin/templates/header',$this->data);
		$this->load->view('admin/programs/edit',$this->data);
		$this->load->view('admin/templates/footer',$this->data);
	}

	public function day( $day = 1, $programId = 0 ) {
		$this->data['page_title'] 	= 'Programs';
		$this->data['menu'] 		= 'programs';
		$this->data['submenu'] 		= '';
 
		$this->data['js_files'] = array(
			base_url('assets/plugins/nestable/nestable.js'),
			base_url('assets/js/admin/day_workout.js?ver=1.0.2'),
		);

		$arr_join = [
			'weighted_types t1' => 't1.id = t.weighted_type',
			'gender_types t2' 	=> 't2.id = t.gender_type',
		];
		$model = $this->common->get('programs t', ['t.id' => $programId], 'object', 't.*, t1.title w_type, t2.title g_type', $arr_join );

		$this->data['day'] = $day;
		$this->data['model'] = $model;

		$next_day = 0;
		if( $model->days > $day ) {
			$next_day = $day + 1;
		}
		$this->data['next_day'] = $next_day;

		$row =  $this->common->get( 'program_days', ['program_id' => $programId, 'day' => $day] );
		if( empty($row) ) {
			redirect('/admin/programs');
		}
		$this->data['row'] = $row;
		$arr_join = [
			'workouts t1' => 't1.id = t.workout_id',
		];
        
	 	$this->data['arr_exercises'] = $this->common->get_all( 'program_exercises t', ['t.day_id' => $row->id], 't.*, t1.title', 'sort_order', '', '',  $arr_join);
		$this->data['exercises'] = $this->common->get_all( 'workouts', '', '', 'title asc');

		$this->load->view('admin/templates/header',$this->data);
		$this->load->view('admin/programs/days',$this->data);
		$this->load->view('admin/templates/footer',$this->data);
	}

	public function save_day( $programId = 0, $dayId = 0 ) {
		$up_arr = [
			'title' => trim($this->input->post('title'))
		];
		$this->common->update( 'program_days', $up_arr, ['id' => $dayId] );
		$data_arr = [
			'day_id' 		=> $dayId,
			'workout_id' 	=> $this->input->post('workout_id'),
			'steps'	 		=> $this->input->post('steps'),
			'reps' 			=> $this->input->post('reps'),
			'time' 			=> $this->input->post('time'),
		];
		$this->common->insert( 'program_exercises', $data_arr);

		$arr_join = [
			'workouts t1' => 't1.id = t.workout_id',
		];
		$this->data['arr_exercises'] = $this->common->get_all( 'program_exercises t', 
			['t.day_id' => $dayId], 't.*, t1.title', '', '', '',  $arr_join);

		$response = [
			'html' => $this->load->view('admin/programs/_exercise', $this->data, true)
		];			
		header('Content-type: application/json');	
		die(json_encode($response));
	}

	public function delete($id = 0) {
		$this->common->delete('programs', ['id' => $id]);
		$this->common->delete('program_days', ['program_id' => $id]);
	}
	
	public function delete_exercises( $id = 0 ) {
	    $this->common->delete('program_exercises', ['id' => $id]);
	}

	public function addsortvalue($sort_order = 0)
	{
      
       if($_POST)
	    {
	    	$data = json_decode($_POST['value']);
	        foreach ($data as $row) {
	        	$sort_order++;
	        	$this->common->update('program_exercises',['sort_order'=>$sort_order],['id'=>$row->id]);
	        }
	        $res =  "<label style='color:green font:15px'> Updated successfully </label>";
	    }else{
	    	$res = "<label style='color:red font:15px'> Error occured Try again later </label>";
	    } 

	    header('Content-Type: application/json');
        echo json_encode(array($res));
	}
}
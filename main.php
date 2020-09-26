<?php
use \Model\DataLinkModel;
use \Model\HospitalListModel;
use \Model\DRGListModel;
class Controller_Main extends Controller_Template
{

	public function action_index()
	{
		$data = array();
		$this->template->title = 'Home';
		$this->template->main_css = 'main.css';
		$this->template->content = View::forge('milestone/empty', $data);
	}
	public function get_index()
	{
		if(isset($_GET['direction'])){
			$direction = $_GET['direction'];
			if($direction == 'DRG'){
				$this->template->hospital_css = 'DRG.css';
			} else if($direction == 'hlist'){
				$this->template->hospital_css = 'hospitals.css';
			}
			else if($direction == 'DRG Details'){
				$this->template->hospital_css = 'DRG.css';
			}
			else if($direction == 'Hospitals Details'){
				$this->template->hospital_css = 'hospitals.css';
			}
			else if($direction == 'about'){
				$this->template->hospital_css = 'about.css';
			}

		}
		$data = array();
		$this->template->title = 'Home';
		$this->template->main_css = 'main.css';
		$this->template->content = View::forge('milestone/empty', $data);
	}

		public function action_drg()
	{
		if(!isset($_GET['page'])){
						$page = 1;
				}
				else{
						$page = $_GET['page'];
				}

				$limit_result = ($page - 1) * 20;
		$data = array(
		'hospitalInfo'=> DRGListModel::DRGListData($limit_result, 20),
		'ResultCount' => DRGListModel::DRGListDataCount()
		);
		$this->template->title = 'DRG';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'DRG.css';
		$this->template->content = View::forge('milestone/DRG', $data);
	}

	public function action_DRGDetails()
	{
		if(!isset($_GET['page'])){
						$page = 1;
				}
				else{
						$page = $_GET['page'];
				}
		$limit_result = ($page - 1) * 20;
		if(!isset($_GET['uri'])){
					if(Uri::segment(3)!="DRGDetails"){
							$seg = Uri::segment(3);
					}
					else{
						$seg = "";
					}
		}
		else{
					$seg =  $_GET['uri'];
		}
		if($seg == ""){
			$seg=1;
			$data = array(
			'hospitalInfo'=> DataLinkModel::DRGDetailData($limit_result, 20, $seg),
			'ResultCount' => DataLinkModel::DRGDetailDataCount($seg),
			'TargetInfo' => DataLinkModel::TargetDRGInfo($seg),
			'segment' => $seg
			);
		}else{
			$data = array(
			'hospitalInfo'=> DataLinkModel::DRGDetailData($limit_result, 20, $seg),
			'ResultCount' => DataLinkModel::DRGDetailDataCount($seg),
			'TargetInfo' => DataLinkModel::TargetDRGInfo($seg),
			'segment' => $seg
			);
		}

		$this->template->title = 'DRG Details';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'DRG.css';
		$this->template->content = View::forge('milestone/DRGDetails', $data);
	}


		public function action_hlist()
	{

		  if(!isset($_GET['page'])){
		          $page = 1;
		      }
		      else{
		          $page = $_GET['page'];
		      }

		      $limit_result = ($page - 1) * 20;
		  $data = array(
		  'hospitalInfo'=> HospitalListModel::HospitalListData($limit_result, 20),
			'ResultCount' => HospitalListModel::HospitalListDataCount()
		  );
		$this->template->title = 'Hospitals';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'hospitals.css';
		$this->template->content = View::forge('milestone/hlist', $data);
	}

	public function action_hospitalDetails()
	{
		if(!isset($_GET['page']) || $_GET['page'] == 1){
						$page = 1;

				}
				else{
						$page = $_GET['page'];
				}
		if(!isset($_GET['uri'])){
					if(Uri::segment(3) != "hospitalDetails"){
							$seg = Uri::segment(3);
					}
					else{
						$seg = "";
					}
		}
		else{
					$seg =  $_GET['uri'];
		}
		$limit_result = ($page - 1) * 20;

		if($seg == ""){
			$seg = 10033;
			$data = array(
			'hospitalInfo'=> DataLinkModel::HospitalDetailData($limit_result, 20, $seg),
			'ResultCount' => DataLinkModel::HospitalDetailDataCount($seg),
			'TargetInfo' => DataLinkModel::TargetHospitalInfo($seg),
			'unit_info' => HospitalListModel::fetch_comment($seg),
			'sub_comments' => HospitalListModel::fetch_sub_comment($seg),
			'segment' => $seg,
			);
		}
		else{
			$data = array(
			'hospitalInfo'=> DataLinkModel::HospitalDetailData($limit_result, 20, $seg),
			'ResultCount' => DataLinkModel::HospitalDetailDataCount($seg),
			'TargetInfo' => DataLinkModel::TargetHospitalInfo($seg),
			'unit_info' => HospitalListModel::fetch_comment($seg),
			'sub_comments' => HospitalListModel::fetch_sub_comment($seg),
			'segment' => $seg,
			);
		}
		$this->template->title = 'Hospitals Details';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'hospitals.css';
		$this->template->content = View::forge('milestone/hospitalDetails', $data);
	}
	public function action_DRGInfo()
	{
		if(!isset($_GET['drg']) || !isset($_GET['id'])){
						$drg = 001;
						$id = 010033;
				}
				else{
						$drg = $_GET['drg'];
						$id = $_GET['id'];
		}
		$data = array(
			'DRGInfo'=> DataLinkModel::DRGInfo($id,$drg),
			'TargetInfo' => DataLinkModel::TargetHospitalInfo($id),
		);
		$this->template->title = 'Hospitals Details';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'hospitals.css';
		$this->template->content = View::forge('milestone/DRGInfo', $data);
	}

	public function post_new_comment(){
	        session_start();
	        if(strlen(Input::post('comment_content')) > 0 && isset($_SESSION['username'])){
	            $user= $_SESSION['username'];
	            $id= HospitalListModel::uid($user);
	            HospitalListModel::add_new_hospital_comment($id[0]['id'], Input::post('MPN'), $user, 0, 0, Input::post('comment_content'));
	        }
	        return Response::redirect('/index/main/hospitalDetails/'.Input::post('MPN'));
	}
	public function post_edit_comment(){
		session_start();
		if(strlen(Input::post('comment_content')) > 0 && isset($_SESSION['username'])){
				$user= $_SESSION['username'];
				$id= HospitalListModel::uid($user);
				HospitalListModel::edit_Pcomment(Input::post('CID'), Input::post('comment_content'));
		}
		return Response::redirect('/index/main/hospitalDetails/'.Input::post('MPN'));

	}
	public function post_sub_comment(){
			        session_start();
			        if(strlen(Input::post('comment_content')) > 0 && isset($_SESSION['username'])){
			            $user= $_SESSION['username'];
			            $id= HospitalListModel::uid($user);
			            HospitalListModel::add_new_hospital_subcomment($id[0]['id'], Input::post('MPN'), Input::post('CID'), $user, 0, 0, Input::post('comment_content'));
			        }
			        return Response::redirect('/index/main/hospitalDetails/'.Input::post('MPN'));
	}
		public function action_about()
	{
		$data = array();
		$this->template->title = 'About Us';
		$this->template->main_css = 'main.css';
		$this->template->hospital_css = 'about.css';
		$this->template->content = View::forge('milestone/about', $data);
	}

	public function action_login()
{
			session_start();
			if (Input::post()) {
					$username = Input::post('username');
					$pass = Input::post('password');
					$res = Auth::instance()->login($username, $pass);
					if ($res) {
							$_SESSION['username'] = $username;
							$cur_user = $_POST['username'];
							return Response::redirect('index/main');
					}
					else {
							return Response::forge(View::Forge('milestone/outline', array(
									'contents' => View::forge('milestone/login'),
									'alerts' => View::forge('milestone/failalert', array('message' => 'The user name or password is incorrect'))
							)));
					}
			}
			return Response::forge(View::Forge('milestone/outline', array(
					'contents' => View::forge('milestone/login')
			)));
}
	public function action_signup()
{
			session_start();
			if (Input::post()) {
					$val = Validation::forge();
					$val->add_field('username', 'Your username', 'required');
					$val->add_field('password', 'Your password', 'required');
					$val->add_field('email', 'Your email', 'required|valid_email');
					if ($val->run()){
							try {
									Auth::create_user(
											Input::post('username'),
											Input::post('password'),
											Input::post('email')
									);
									$_SESSION['username'] = Input::post('username');
									return Response::redirect('index/main');
							} catch (SimpleUserUpdateException $e) {
									return Response::forge(View::Forge('milestone/outline', array(
											'contents' => View::forge('milestone/signup'),
											'alerts' => View::forge('milestone/failalert', array('message' => $e->getMessage()))
									)
									));
							}

					} else {
							return Response::forge(View::Forge('milestone/outline', array(
									'contents' => View::forge('milestone/signup'),
									'alerts' => View::forge('milestone/failalert', array('message' => 'Missing one or more fields.'))
							)
							));
					}
			}
			return Response::forge(View::Forge('milestone/outline', array('contents' => View::forge('milestone/signup'))));
	//return Response::forge(View::forge('milestone/signup'));
}
	public function action_logout() {
			session_start();
			$_SESSION["user_id"] = "";
			session_destroy();
			return Response::redirect('index/main');
	}

}
?>

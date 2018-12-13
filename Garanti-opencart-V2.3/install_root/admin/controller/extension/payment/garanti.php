<?php
ini_set("display_errors", "on");

class ControllerExtensionPaymentgaranti extends Controller {
	private $error = array();
	
	public function install () {
		$this->load->model('extension/payment/garanti');

		$this->model_extension_payment_garanti->install();
	}
	

	public function index() {
		$this->load->language('payment/garanti');
		
		$this->document->setTitle('Kredi Kartı İle Ödeme');

		$this->load->model('setting/setting');
		

		if (isset($this->request->post['garanti_submit'])) {
			$this->model_setting_setting->editSetting('garanti', $this->request->post);			
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('extension/payment/garanti', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}
		
		if (isset($this->request->post['confirm_garanti_register'])) {
		
			$this->response->redirect($this->url->link('extension/payment/garanti', 'token=' . $this->session->data['token'] . '&type=payment', true));
		}


		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['help_total'] = $this->language->get('help_total');
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
	
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		if($this->config->get('garanti_userId') == null)
			$data['error_warning'] .= 'wirecard User Code Boş<br/>';
			
		if($this->config->get('garanti_provuserid') == null)
			$data['error_warning'] .= 'wirecard Pin Boş<br/>';
		
		

		
		
		if (isset($this->request->post['garanti_3d_mode'])) {
			$data['garanti_3d_mode'] = $this->request->post['garanti_3d_mode'];
			
		} else {
			$data['garanti_3d_mode'] = $this->config->get('garanti_3d_mode');
		}
		
		if (isset($this->request->post['garanti_userId'])) {
			$data['garanti_userId'] = $this->request->post['garanti_userId'];
		} else {
			$data['garanti_userId'] = $this->config->get('garanti_userId');
		}
		if (isset($this->request->post['garanti_merchantid'])) {
			$data['garanti_merchantid'] = $this->request->post['garanti_merchantid'];
		} else {
			$data['garanti_merchantid'] = $this->config->get('garanti_merchantid');
		}
		if (isset($this->request->post['garanti_teminalid'])) {
			$data['garanti_teminalid'] = $this->request->post['garanti_teminalid'];
		} else {
			$data['garanti_teminalid'] = $this->config->get('garanti_teminalid');
		}
		if (isset($this->request->post['garanti_password'])) {
			$data['garanti_password'] = $this->request->post['garanti_password'];
		} else {
			$data['garanti_password'] = $this->config->get('garanti_password');
		}
		if (isset($this->request->post['garanti_baseurl'])) {
			$data['garanti_baseurl'] = $this->request->post['garanti_baseurl'];
		} else {
			$data['garanti_baseurl'] = $this->config->get('garanti_baseurl');
		}
		if (isset($this->request->post['garanti_env_tab'])) {
			$data['garanti_env_tab'] = $this->request->post['garanti_env_tab'];
			echo s;
		} else {
			$data['garanti_env_tab'] = $this->config->get('garanti_env_tab');
		}
		if (isset($this->request->post['garanti_3dsec_tab'])) {
			$data['garanti_3dsec_tab'] = $this->request->post['garanti_3dsec_tab'];
		} else {
			$data['garanti_3dsec_tab'] = $this->config->get('garanti_3dsec_tab');
		}
		if (isset($this->request->post['garanti_ins_tab'])) {
			$data['garanti_ins_tab'] = $this->request->post['garanti_ins_tab'];
		} else {
			$data['garanti_ins_tab'] = $this->config->get('garanti_ins_tab');
		}
		
		if (isset($this->request->post['garanti_provuserid'])) {
			$data['garanti_provuserid'] = $this->request->post['garanti_provuserid'];
		} else {
			$data['garanti_provuserid'] = $this->config->get('garanti_provuserid');
		}
		if (isset($this->request->post['garanti_status'])) {
			$data['garanti_status'] = $this->request->post['garanti_status'];
		} else {
			$data['garanti_status'] = $this->config->get('garanti_status');
		}
		if (isset($this->request->post['garanti_order_status_id'])) {
			$data['garanti_order_status_id'] = $this->request->post['garanti_order_status_id'];
		} else {
			$data['garanti_order_status_id'] = $this->config->get('garanti_order_status_id');
		}
		$this->load->model('localisation/order_status');
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();


		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_payment'),
			'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payment/garanti', 'token=' . $this->session->data['token'], 'SSL')
		);
		$data['action'] = $this->url->link('extension/payment/garanti', 'token=' . $this->session->data['token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');
		$this->response->setOutput($this->load->view('payment/garanti.tpl', $data));
	}
	
	
	private function curlPostExt($data, $url){
		$ch = curl_init();    // initialize curl handle
		curl_setopt($ch, CURLOPT_URL,$url); // set url to post to
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1); // return into a variable
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
		curl_setopt($ch, CURLOPT_POST, 1); // set POST method
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
		if($result = curl_exec($ch)) { // run the whole process
			curl_close($ch); 
			return $result;
		}
	}

	protected function validate() {
		
		if (!$this->user->hasPermission('modify', 'payment/garanti')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return true;
		
		$this->load->model('localisation/language');

		$languages = $this->model_localisation_language->getLanguages();

		foreach ($languages as $language) {
			if (empty($this->request->post['garanti_bank' . $language['language_id']])) {
				$this->error['bank' .  $language['language_id']] = $this->language->get('error_bank');
			}
		}

		return !$this->error;
	}
}
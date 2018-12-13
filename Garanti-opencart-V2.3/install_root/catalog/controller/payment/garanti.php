<?php

class ControllerPaymentgaranti extends Controller
{

	public function index()
	{
		$this->load->language('payment/garanti');
		$this->document->addStyle('catalog/view/theme/default/stylesheet/garanti_form.css');
		$data['button_confirm'] = $this->language->get('button_confirm');
		$data['text_instruction'] = $this->language->get('text_instruction');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_payment'] = $this->language->get('text_payment');
		$data['text_loading'] = $this->language->get('text_loading');

	//	$data['bank'] = nl2br($this->config->get('garanti_bank' . $this->config->get('config_language_id')));

		$data['continue'] = $this->url->link('checkout/success');

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/garanti.tpl')) {
			return $this->load->view($this->config->get('config_template') . '/garanti.tpl', $data);
		} else {
			return $this->load->view('/payment/garanti.tpl', $data);
		}
	}

	public function paymentform()
	{

		$this->load->model('checkout/order');
		$this->load->model('setting/setting');	
		require_once(DIR_APPLICATION . 'controller/payment/includes/restHttpCaller.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/helper.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Sale3DOOSPayRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/SaleOOSPayRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Settings3D.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Settings.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/SalesRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/VPOSRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Sale3DSecureRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Secure3DSuccessRequest.php');
		
		if(!isset($this->session->data['order_id']) OR !$this->session->data['order_id'])
			die('Sipariş ID bulunamadı');
		$orderid =$this->session->data['order_id'];
		
		$order_info = $this->model_checkout_order->getOrder($orderid);
		
		
		$error_message = false;
		$cc_form_key = md5($order_info['order_id'] . $order_info['store_url']);
		$isInstallment = $this->config->get('garanti_ins_tab');
		$total_cart = $order_info['total'];

			if(isset($this->request->post['cc_form_key']) AND $this->request->post['cc_form_key'] == $cc_form_key ) { //form ile direk ödeme
			
				$record = $this->pay($orderid);
				// if($record["shared_payment_url"] != 'null') // Ortak ödemeye yönlen 
				// {	
					// $this->saveRecord($record);
					// header('Location: '.$record["shared_payment_url"]);
					// exit;
				// }
				$this->saveRecord($record);
				if($record['status_code'] == 'Approved' ) {//Başarılı işlem
			
					$this->session->data['payment_method']['code'] = 'garanti';
						$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('garanti_order_status_id'), "");
						$this->response->redirect($this->url->link('checkout/success', '', 'ssl'));
						$this->saveRecord($record);
				}
				else { //Başarısız işlem
					$error_message = 'Ödeme başarısız oldu: Bankanızın cevabı: ('. $record['result_message'] . ') ' . $record['result_message'];
				}	
								
			}	
		    
			elseif (isset($_POST['clientid']) && isset($_POST['companyname'])) { //Ortak ödemeden gelirse. 
				
		$record =$this->getRecordByOrderId($this->request->post['orderid']);
				if(!empty($_POST['mdstatus']))
				{
					$record['status_code'] = $_POST['mdstatus'];
				}
				else
				{
				$record['status_code'] = '';
				}
				if(!empty($_POST['mderrormessage']))
				{
					$record['result_code'] = $_POST['errmsg'];
				}
				else
				{
				$record['result_code'] = '';
				}
				$record['result_message'] =$_POST['response'];		
				$record['amount']=$_POST['txnamount'] / 100;
				$this->saveRecord($record);	
					if($record['result_message'] == 'Approved' ) {//Başarılı işlem
					$hash=$_POST["hash"];
					$hashParamsVal="";
					$storeKey= "12345678";
					$hashParams=$_POST["hashparams"];
					$valid=false;
			//Validasyon 
				if (!empty($hashParams)) 
				{
		   $result= explode(":",$hashParams);
		  
			foreach ($result as $key) 
			{
				if(!empty($key))
				$hashParamsVal .= $_POST[$key];
				
			
			}
			$hashParamsVal .= $storeKey;
			
			$valid=helper::Validate3DReturn($hashParamsVal,$hash);
	   }

   if($valid==true){
						
						
						$this->session->data['payment_method']['code'] = 'garanti';
						$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('garanti_order_status_id'), "");
						$this->response->redirect($this->url->link('checkout/success', '', 'ssl'));
						$this->saveRecord($record);
	   
					
   }
   else
	   $error_message = 'Ödeme başarısız oldu: Validasyon Hatalı';
				}
				else { //Başarısız işlem
				$error_message = 'Ödeme başarısız oldu: Kart Bankası İşlem Cevabı: ('. $record['result_code'] . ') ' . $record['result_message'];
					
				}
			}
		
		elseif(isset($_POST['clientid']) && !isset($_POST['companyname'])){//3dform
				
				$hash=$_POST["hash"];
				$hashParamsVal="";
				$storeKey= "12345678";
				$hashParams=$_POST["hashparams"];
				$valid=false;
				//Validasyon 
	   if (!empty($hashParams))
	   {
		   $result= explode(":",$hashParams);
		  
			foreach ($result as $key) 
			{
				if(!empty($key))
				$hashParamsVal .= $_POST[$key];
				
			
			}
			$hashParamsVal .= $storeKey;
			
			$valid=helper::Validate3DReturn($hashParamsVal,$hash);
	   }
	if($valid==true)
	{
		
	   
		 if(($_POST["mdstatus"]=="1") or ($_POST["mdstatus"]=="2") || ($_POST["mdstatus"]=="3") || ($_POST["mdstatus"]=="4"))
			{
			

			$request=new Secure3DSuccessRequest();
			$settings=new Settings(); //Garanti tarafından sağlanan bilgiler ile değiştirilmelidir.
			$settings->Version="V0.1";
			$settings->Mode="Test";
			$settings->BaseUrl="https://sanalposprovtest.garanti.com.tr/VPServlet";
			$settings->Password="123qweASD/";
			
			$request->Mode=$_POST["mode"];
			$request->Version=$_POST["apiversion"];
		
			$request->Terminal= new Terminal();

			$request->Terminal->ID=$_POST["clientid"];
			$request->Terminal->MerchantID=$_POST["terminalmerchantid"];
			$request->Terminal->ProvUserID=$_POST["terminalprovuserid"];
			$request->Terminal->UserID=$_POST["terminaluserid"];
			
				
			$request->Card = new Card();

			$request->Card->CVV2="";
			$request->Card->ExpireDate="";
			$request->Card->Number="";
			
			$request->Customer = new Customer();

			$request->Customer->EmailAddr=$_POST["customeremailaddress"];
			$request->Customer->IPAddress=$_POST["customeripaddress"];
			
			$request->AuthenticationCode=$_POST["cavv"];
			$request->Md=$_POST["md"];
			$request->SecurityLevel=$_POST["eci"];
			$request->TxnID=$_POST["xid"];
			
			$request->Order = new Order();

			$request->Order->OrderID=$_POST["orderid"];
			$request->Order->Description="";
			
			$request->Transaction = new Transaction();

			$request->Transaction->Amount=$_POST["txnamount"];
			$request->Transaction->Type=$_POST["txntype"];
			$request->CurrencyCode=$_POST["txncurrencycode"];
			$request->InstallmentCnt=$_POST["txninstallmentcount"];
			$request->MotoInd="N";
			$request->CardholderPresentCode=13;
			
				$request->Hash=helper::ComputeHash($request,$settings);

				 $response = Secure3DSuccessRequest::execute($request,$settings);
				 
		 $sxml = new SimpleXMLElement( $response);
			
				 
				 $statusOk =$sxml->Transaction[0]->Response->Message;
				$result_message = helper::turkishreplace( $sxml->Transaction[0]->Response->ErrorMsg);
				$cardnumber = $sxml->Transaction[0]->CardNumberMasked;
				$formrefretnumber =$sxml->Transaction[0]->RetrefNum;
				
				 if($statusOk=='Approved')
				 {
					 
				$this->session->data['payment_method']['code'] = 'garanti';
						$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('garanti_order_status_id'), "");
						$this->response->redirect($this->url->link('checkout/success', '', 'ssl'));
						$this->saveRecord($record);
				}
				else { //Başarısız işlem
					$error_message = 'Ödeme başarısız oldu: Bankanızın cevabı: ('. $result_message . ') ';
				}
			}else { //Başarısız işlem
					$error_message = 'Ödeme başarısız oldu!' ;
				}
	}
		   else { //Başarısız işlem
					$error_message = 'Ödeme başarısız oldu: Validasyon Hatalı:';
				}
			}
		
		
		
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$data['isInstallment'] = $isInstallment;
		$data['cc_form_key'] = $cc_form_key;
		$data['error_message'] = $error_message;
		$data['mode'] = $this->config->get('garanti_3d_mode');
		$data['cart_id'] = $this->session->data['order_id'];
		$data['form_link'] = $this->url->link('payment/garanti/paymentform', '', 'SSL');


		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/garanti_ccform.tpl')) {
			$this->response->setOutput($this->load->view($this->config->get('config_template') . '/garanti_ccform.tpl', $data));
		} else {
			$this->response->setOutput($this->load->view('/payment/garanti_ccform.tpl', $data));
		}
	}


	function pay()
	{
		$this->load->model('checkout/order');
		require_once(DIR_APPLICATION . 'controller/payment/includes/restHttpCaller.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/helper.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Sale3DOOSPayRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/SaleOOSPayRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Settings3D.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Settings.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/SalesRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/VPOSRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Sale3DSecureRequest.php');
		require_once(DIR_APPLICATION . 'controller/payment/includes/Secure3DSuccessRequest.php');
			
		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		$prices =$order_info['total'];

		$record = array(
			'result_code' => '0',
			'result_message' => '',
			'result' => false
		);

	
		$amount = (float) $order_info['total'];
		$order_id = $this->session->data['order_id'];
		$garanti_userId  = $this->config->get('garanti_userId');
		$garanti_provuserid  = $this->config->get('garanti_provuserid');
		$garanti_merchantid  = $this->config->get('garanti_merchantid');
		$garanti_teminalid  = $this->config->get('garanti_teminalid');
		$garanti_password  = $this->config->get('garanti_password');
		$garanti_baseurl  = $this->config->get('garanti_baseurl');
		$mode_env = $this->config->get('garanti_env_tab');
		$version = "V0.1";
		$garanti_3dsec_tab  = $this->config->get('garanti_3dsec_tab');
		

		
		
		$mode = $this->config->get('garanti_3d_mode');
		$installment = isset($this->request->post['garanti-installment-count']) ? (int) $this->request->post['garanti-installment-count']:0; 
		$user_id=$this->session->data['user_id'];
		$expire_date = isset($this->request->post['cc_expiry']) ? explode('/', $this->request->post['cc_expiry']):00;

	
			$record = array(
					'order_id' => $order_id,
					'customer_id' => $user_id,
					'garanti_id' => $order_id,
					'amount' => $amount,
					'amount_paid' => $amount,
					'installment' => $installment,
					'cardholdername' =>   isset($this->request->post['cc_name'])?$this->request->post['cc_name']:'',
					'cardexpdate' => str_replace(' ', '', $expire_date[0]) . str_replace(' ', '', $expire_date[1]),
					'cardnumber' => isset($this->request->post['cc_number']) ? substr($this->request->post['cc_number'], 0, 6) . 'XXXXXXXX' . substr($this->request->post['cc_number'], -2):'',
					'createddate' =>date("Y-m-d h:i:s"), 
					'ipaddress' =>  helper::get_client_ip(),
					'status_code' => 1, //default başarısız
					'result_code' => '', 
					'result_message' => '',
					'mode' =>  $mode,
					'shared_payment_url' => 'null'
				);
		

		
				
			if ($mode == 'form')
			{
			
							
				$settings=new Settings();
				
				$request = new SalesRequest();
				$request->Version = $version;
				$request->Mode = $mode_env;
				
				$settings->Version=$version;
				$settings->Mode=$mode_env;
				$settings->BaseUrl=$garanti_baseurl;
				$settings->Password=$garanti_password;
				
				$request->Customer = new Customer();
				$request->Customer->EmailAddr="fatih@codevist.com";
				if(helper::get_client_ip()=='::1')
			{
				$request->Customer->IPAddress = "127.0.0.1";
				
			}
			else
			{
				$request->Customer->IPAddress = helper::get_client_ip();
			}
				$request->Card = new Card();
				$request->Card->CVV2=$this->request->post['cc_cvc'];
				$request->Card->ExpireDate=str_replace(' ', '', $expire_date[0]) . str_replace(' ', '', $expire_date[1]);
				$request->Card->Number=str_replace(' ', '', $this->request->post['cc_number']);
				
				$request->Order = new Order();
				$request->Order->OrderID=str_replace('-', '',helper::GUID());
				$request->Order->Description="";
				
			 
				$request->Terminal= new Terminal();
				$request->Terminal->ProvUserID=$garanti_provuserid;
				$request->Terminal->UserID=$garanti_userId;
				$request->Terminal->ID=$garanti_teminalid;
				$request->Terminal->MerchantID=$garanti_merchantid;
				
				$request->Transaction = new Transaction();
				$request->Transaction->Amount=$amount* 100;
				$request->Transaction->Type="sales";
				$request->CurrencyCode="949";
				$request->MotoInd="N";
				
				$request->Hash=helper::ComputeHash($request,$settings);
				try {
				
				$response = SalesRequest::execute($request,$settings);
					
				} catch (Exception $e) {
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
				$sxml = new SimpleXMLElement($response);
				$record['status_code'] = $sxml->Transaction[0]->Response->Message;
				$record['result_code'] = $sxml->Transaction[0]->Response->ReasonCode;
				$record['result_message'] = helper::turkishreplace( $sxml->Transaction[0]->Response->ErrorMsg);
				$record['cardnumber'] = $sxml->Transaction[0]->CardNumberMasked;
				$error_message = false;
	
				return $record;
			}
			elseif ($mode =='shared3d') //shared 3d ortak ödeme sayfası 3d 
			{
 			
				$settings3D=new Settings3D();
				$settings3D->mode=$mode_env;
				$settings3D->apiversion=$version;
				$settings3D->BaseUrl=$garanti_baseurl;
				$settings3D->Password=$garanti_password;
				
				
				$request = new Sale3DOOSPayRequest();
				$request->apiversion = $version;
				$request->mode = $mode_env;
			
			
				$request->terminalid=$garanti_teminalid;
				$request->terminaluserid=$garanti_userId;
				$request->terminalprovuserid =$garanti_provuserid;
				$request->terminalmerchantid = $garanti_merchantid;
			
			
			
				$request->successurl = $this->url->link('payment/garanti/paymentform');
				$request->errorurl = $this->url->link('payment/garanti/paymentform');
				$request->customeremailaddress = "fatih@codevist.com";
			
			if(helper::get_client_ip()=='::1')
			{
				$request->customeripaddress = "127.0.0.1";
				
			}
			else
			{
				$request->customeripaddress = helper::get_client_ip();
			}
				
				$request->secure3dsecuritylevel =$garanti_3dsec_tab;
				$request->orderid = str_replace('-', '',helper::GUID());
				$request->txnamount = $amount* 100;
				$request->txntype = "sales";
				$request->txninstallmentcount = "";
				$request->txncurrencycode = "949";
				$request->storekey = "12345678";
				$request->txntimestamp = date("d-m-Y H:i:s");
				$request->lang = "tr";
				$request->refreshtime = "10";
				$request->companyname = "deneme";

				
				$request->secure3dhash=Sale3DOOSPayRequest::Compute3DHash($request,$settings3D);
				
				
				
				try {
				$response = Sale3DOOSPayRequest::execute($request,$settings3D);
				print $response;
			
			
			
				 
				
				} catch (Exception $e) {
			
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement($response);
				$record['status_code'] = $sxml->Transaction[0]->Response->Message;
				$record['result_code'] = $sxml->Transaction[0]->Response->ReasonCode;
				$record['result_message'] = helper::turkishreplace( $sxml->Transaction[0]->Response->ErrorMsg);
				$record['cardnumber'] = $sxml->Transaction[0]->CardNumberMasked;
				$record['formrefretnumber'] =$sxml->Transaction[0]->RetrefNum;
				return $record;
			}
			elseif ($mode =='shared') //shared  ortak ödeme sayfası  
			{
 			
			 
				$settings3D=new Settings3D();
				$settings3D->mode=$mode_env;
				$settings3D->apiversion=$version;
				$settings3D->BaseUrl=$garanti_baseurl;
				$settings3D->Password=$garanti_password;
				
				
				$request = new SaleOOSPayRequest();
				$request->apiversion = $version;
				$request->mode = $mode_env;
			
			
				$request->terminalid=$garanti_teminalid;
				$request->terminaluserid=$garanti_userId;
				$request->terminalprovuserid =$garanti_provuserid;
				$request->terminalmerchantid = $garanti_merchantid;
			
			
			
				$request->successurl = $this->url->link('payment/garanti/paymentform');
				$request->errorurl = $this->url->link('payment/garanti/paymentform');
				$request->customeremailaddress = "fatih@codevist.com";
			
			if(helper::get_client_ip()=='::1')
			{
				$request->customeripaddress = "127.0.0.1";
				
			}
			else
			{
				$request->customeripaddress = helper::get_client_ip();
			}
				
				$request->secure3dsecuritylevel ="OOS_PAY";
				$request->orderid = str_replace('-', '',helper::GUID());
				$request->txnamount = $amount* 100;
				$request->txntype = "sales";
				$request->txninstallmentcount = "";
				$request->txncurrencycode = "949";
				$request->storekey = "12345678";
				$request->txntimestamp = date("d-m-Y H:i:s");
				$request->lang = "tr";
				$request->refreshtime = "10";
				$request->companyname = "deneme";

				
				$request->secure3dhash=SaleOOSPayRequest::Compute3DHash($request,$settings3D);
				
				
				
				try {
				$response = SaleOOSPayRequest::execute($request,$settings3D);
				print $response;
			
			
				} catch (Exception $e) {
			
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement($response);
				$record['status_code'] = $sxml->Transaction[0]->Response->Message;
				$record['result_code'] = $sxml->Transaction[0]->Response->ReasonCode;
				$record['result_message'] = helper::turkishreplace( $sxml->Transaction[0]->Response->ErrorMsg);
				$record['cardnumber'] = $sxml->Transaction[0]->CardNumberMasked;
				$record['formrefretnumber'] =$sxml->Transaction[0]->RetrefNum;
				return $record;
			}
			else 
			{ //form3d
				$settings3D=new Settings3D();
				$settings3D->mode=$mode_env;
				$settings3D->apiversion=$version;
				$settings3D->BaseUrl=$garanti_baseurl;
				$settings3D->Password=$garanti_password;		
												
				$request = new Sale3DSecureRequest();
				$request->apiversion = $version;
				$request->mode = $mode_env;
			
			
				$request->terminalid=$garanti_teminalid;
				$request->terminaluserid=$garanti_userId;
				$request->terminalprovuserid =$garanti_provuserid;
				$request->terminalmerchantid = $garanti_merchantid;
			
			
				$request->successurl = $this->url->link('payment/garanti/paymentform');
				$request->errorurl = $this->url->link('payment/garanti/paymentform');
				$request->customeremailaddress = "fatih@codevist.com";
			
				if(helper::get_client_ip()=='::1')
				{
					$request->customeripaddress = "127.0.0.1";
					
				}
				else
				{
					$request->customeripaddress = helper::get_client_ip();
				}
				
				$request->secure3dsecuritylevel ="3D";
				$request->orderid = str_replace('-', '',helper::GUID());
				$request->txnamount = $amount* 100;
				$request->txntype = "sales";
				$request->txninstallmentcount = "";
				$request->txncurrencycode = "949";
				$request->storekey = "12345678";
				$request->txntimestamp = date("d-m-Y H:i:s");
				$request->cardnumber = str_replace(' ', '', $this->request->post['cc_number']);
				$request->cardexpiredatemonth = str_replace(' ', '', $expire_date[0]);
				$request->cardexpiredateyear = str_replace(' ', '', $expire_date[1]);
				$request->cardcvv2 = $this->request->post['cc_cvc'];
				$request->lang = "tr";
				$request->refreshtime = "10";
				
				
				
				$request->secure3dhash=Sale3DSecureRequest::Compute3DHash($request,$settings3D);
				
				
				
				try {
				$response = Sale3DSecureRequest::execute($request,$settings3D);
				print($response); 
				
				} catch (Exception $e) {
			
					$record['result_code'] = 'ERROR';
					$record['result_message'] = $e->getMessage();
					$record['status_code'] = 1;
					return $record;
				}
	
				$sxml = new SimpleXMLElement($response);
				$record['status_code'] = $sxml->Transaction[0]->Response->Message;
				$record['result_code'] = $sxml->Transaction[0]->Response->ReasonCode;
				$record['result_message'] = helper::turkishreplace( $sxml->Transaction[0]->Response->ErrorMsg);
				$record['cardnumber'] = $sxml->Transaction[0]->CardNumberMasked;
				
				return $record;
			}	

	}


	private function addRecord($record)
	{
		return $this->db->query($this->insertRowQuery('garanti_payment', $record));
	}

	private function updateRecordByOrderId($record)
	{
		return $this->db->query($this->updateRowQuery('garanti_payment', $record, array('order_id' => (int) $record['order_id'])));
	}

	
	public function saveRecord($record)
	{
		$record['createddate'] = date("Y-m-d h:i:s");
		if (isset($record['order_id'])
				AND $record['order_id']
				AND $this->getRecordByOrderId($record['order_id']))
			return $this->updateRecordByOrderId($record);

			return $this->addRecord($record);
	}

	public function getRecordByOrderId($order_id)
	{
		$row = $this->db->query('SELECT * FROM `' . DB_PREFIX . 'garanti_payment` '
				. 'WHERE `order_id` = ' . (int) $order_id);
		return $row->num_rows == 0 ? false : $row->row;
	}

	
	private function updateRowQuery($table, $array, $where, $what = null, $deb = false)
	{
		$q = "UPDATE `" . DB_PREFIX . "$table` SET ";
		$i = count($array);
		foreach ($array as $k => $v) {
			$q .= '`' . $k . '` = ' . "'" . $this->escape($v) . "'";
			$i--;
			if ($i > 0)
				$q .=" ,\n";
		}
		$q .= ' WHERE ';
		if (is_array($where)) {
			$i = count($where);
			foreach ($where as $k => $v) {
				$i--;
				$q .= '`' . $k . '` = \'' . $this->escape($v) . '\' ';
				if ($i != 0)
					$q .= ' AND ';
			}
		} else
			$q .= "`$where` = '" . $this->escape($what) . "' LIMIT 1";
		if ($deb)
			echo $q;
		return $q;
	}

	private function insertRowQuery($table, $array, $deb = false)
	{
		$f = '';
		$d = '';
		$q = "INSERT INTO `" . DB_PREFIX . "$table` ( ";
		$i = count($array);
		foreach ($array as $k => $v) {
			if (is_array($v))
				print_r($v);
			$f .= "`" . $k . "`";
			$d .= "'" . $this->escape($v) . "'";
			$i--;
			if ($i > 0) {
				$f .=", ";
				$d .=", ";
			}
		}
		$q .= $f . ') VALUES (' . $this->escape($d) . ' )';
		if ($deb)
			echo $q;
		return $q;
	}

	private function escape($var)
	{
		return $var;
	}
}

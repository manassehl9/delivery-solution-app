<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jit extends CI_Controller {

    function __construct()
    {
        parent::__construct();
		$this->load->library('session'); 
		
        $this->load->model('jit_model');
    }
	public function index()
	{
        $transaction_id = 'SPCK'.date("Y").mt_rand(1000000, 9999999);  
		$_SESSION['order_id'] = $transaction_id;
		$data['order_id'] = $_SESSION['order_id'];
        $data['states'] = $this->jit_model->get_state();
        $data['couriers'] = $this->jit_model->get_courier();

		if(isset($_REQUEST['create'])){
			$post = array();
			var_dump($post);
			die;
		}else{
			$this->load->view('layouts/header');
			$this->load->view('jit', $data);
			$this->load->view('layouts/footer');
		}
		
    }

    public function get_order_details()
    {
        $data['item_name'] = $this->input->post('item_name');
        $data['item_quantity'] = $this->input->post('item_quantity');
        $data['item_weight'] = $this->input->post('item_weight');
        $data['item_price'] = $this->input->post('item_price');
        $data['merchant_name'] = $this->input->post('merchant_name');
        $data['merchant_contact'] = $this->input->post('merchant_contact');
		$data['merchant_email'] = $this->input->post('merchant_email');
		$data['merchant_address'] = $this->input->post('merchant_address');
        $data['customer_name'] = $this->input->post('customer_name');
        $data['customer_contact'] = $this->input->post('customer_contact');
		$data['customer_email'] = $this->input->post('customer_email');
		$data['customer_address'] = $this->input->post('customer_address');
		$data['merchant_state'] = $this->input->post('merchant_state');
		$data['merchant_lga'] = $this->input->post('merchant_lga');
		$data['customer_state'] = $this->input->post('customer_state');
		$data['customer_lga'] = $this->input->post('customer_lga');
        
        $item_count = count($data['item_name']);
		$data['weight'] = 0;
		$data['total_item_cost'] = 0;
		for($i=0; $i<$item_count; $i++)
		{
			$item_name[] = $data['item_name'][$i];
			$item_quantity[] = $data['item_quantity'][$i];
			$item_price[] = $data['item_price'][$i];
			$item_weight[] = $data['item_weight'][$i];
			$data['weight'] += $data['item_weight'][$i];
			$data['total_item_cost'] += (int)$data['item_price'][$i] * (int)$data['item_quantity'][$i];
		}
		
		$_SESSION['item_name'] = $item_name;
		$_SESSION['item_price'] = $item_price;
		$_SESSION['item_quantity'] = $item_quantity;
		$_SESSION['item_weight'] = $item_weight;

	
		$item_price = array_sum($item_price);
		$item_quantity = array_sum($item_quantity);
        
        $data['total_item_price'] = $item_price;
		$_SESSION['total_amount'] = $data['total_item_cost'];


		$_SESSION['transaction'] = $data;

		// Store merchant details into the database
		$merchant_details['transaction_id'] = $_SESSION['order_id'];
		$merchant_details['merchant_name'] = $data['merchant_name'];
		$merchant_details['merchant_contact'] = $data['merchant_contact'];
		$merchant_details['merchant_email'] = $data['merchant_email'];
		$merchant_details['merchant_address'] = $data['merchant_address'];
		$merchant_details['merchant_state'] = $data['merchant_state'];
		$merchant_details['merchant_lga'] = $data['merchant_lga'];

		// Checks if the transaction id already exists in the database
		$transaction = $this->jit_model->get_transaction($_SESSION['order_id']);
		if(!$transaction)
		{
			$this->jit_model->store_merchant($merchant_details);
		}

    }
    
    public function merchant_delivery_lga()
    {
       
        $state = $this->input->post('state');
        $lga =  $this->jit_model->get_lga($state);

    }

     public function customer_delivery_lga()
    {
        $state = $this->input->post('state');
        $lga =  $this->jit_model->get_lga($state);
    }


    public function get_courier_name()
    {
        $courier_name =  $this->input->post('courier');
        $_SESSION['courier'] = $courier_name;
      
		return $courier_name;
	}
	
	public function get_coupon()
	{
		$date = date("Y-m-d");
		$coupon_code = $this->input->post('coupon_code');
		$coupon = $this->jit_model->get_coupon_value($date, $coupon_code);
		
		if($coupon){
			$coupon_value = (float)$coupon->coupon_value;
			echo json_encode(['coupon_value'=>$coupon_value]);
		}else{
			echo json_encode(['coupon_value' => 'Invalid coupon']);
			
		}
	}


    public function shipping_method_price()
    {
		$courier =  $this->jit_model->get_courier_id($_SESSION['courier']);
	
		if($courier) {
			$courier_id = $courier->courier_id;
		}else{
			$courier_id = '';
		}

		$item_price = $_SESSION['transaction']['item_price'];
		$pickup_state = $_SESSION['transaction']['merchant_state'];
		$delivery_state = $_SESSION['transaction']['customer_state'];
		$pickup_lga =$_SESSION['transaction']['merchant_lga'];
		$delivery_lga = $_SESSION['transaction']['customer_lga'];
		$weight = $_SESSION['transaction']['weight'];
		$quantity = $_SESSION['transaction']['item_quantity'];
		$courier_id = $courier_id;

		$_SESSION['select_courier'] = $courier_id;

		$weight = (int)$weight * (int)$quantity;
		$item_price = (int)$item_price * (int)$quantity;

		$url = 'http://new.saddleng.com/api/v2/shipping_price';
		$token = $this->get_token();
		$body = json_encode(array('delivery_state' => $delivery_state, 'pickup_state' => $pickup_state, 'delivery_lga'=> $delivery_lga, 'pickup_lga' => $pickup_lga, 'weight' => $weight, 'courier_id' => $courier_id));
		$header = array('Content-Type: application/json', 
		'Authorization: Bearer '.$token);


		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$price = curl_exec($ch);


		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch);
		if ($httpcode == 200 && $price > 0) {
			$shippingPrice = $price;
			$_SESSION['shipping_price'] = $shippingPrice;
			//$_SESSION['total_amount'] = $shippingPrice + $item_price;

		} else if($httpcode == 404) {
			$shippingPrice = 0;
			
		}else{
			$shippingPrice = 0;
		}
		echo json_encode(['shipping_price'=>$shippingPrice]);
	}

	public function get_token()
	{
		$url = 'http://new.saddleng.com/api/token';
		//$body = json_encode(array('login' => 'DapoA', 'password' => 'password'));
		$body = json_encode(array('login' => 'sendpackage', 'password' => 'password'));
		$header = array('Content-Type: application/json', 
		'Content-Length: ' . strlen($body));
																					

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		$token = curl_exec($ch);
		$value = json_decode($token);
		return $value->token;
		}

	public function update_transactions()
	{
		$order_id = $this->input->post('order_id');
		$transaction_id =  $this->jit_model->update_transaction($order_id);
	}

	public function download() {
		// database record to be exported
		$db_record = 'transactions';
		$where = 'WHERE `transaction_time` > (NOW() - INTERVAL 2 DAY)';
		// filename for export
		$csv_filename = 'db_export_'.$db_record.'_'.date('Y-m-d').'.csv';

		// database variables
		$hostname = "localhost";
		$user = "jitsaddleuser";
		$password = "j!tU53r";
		$database = "jitsaddle";
		$port = 3306;
	
		$conn = mysqli_connect($hostname, $user, $password, $database, $port);
		if (mysqli_connect_errno()) {
    		die("Failed to connect to MySQL: " . mysqli_connect_error());
		}

		$csv_export = '';

		// query to get data from database
		$query = mysqli_query($conn, "SELECT * FROM ".$db_record." ".$where);
		$field = mysqli_field_count($conn);

		// create line with field names
		for($i = 0; $i < $field; $i++) {
			$csv_export.= mysqli_fetch_field_direct($query, $i)->name.',';
		}

		$csv_export.= '
		';

		while($row = mysqli_fetch_array($query)) {
			for($i = 0; $i < $field; $i++) {
				$csv_export.= '"'.$row[mysqli_fetch_field_direct($query, $i)->name].'",';
			}
			$csv_export.= '
			';
		}

		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=".$csv_filename."");
		echo($csv_export);
	}

	public function courier()
	{
		$order_id = $_SESSION['order_id'];
		$pickup_type = 'mercht-loc';
		$data['transaction_id'] = $order_id;
		$courier_id = $_SESSION['select_courier'];

		$item_name = $_SESSION['transaction']['item_name'];
		$item_price = $_SESSION['transaction']['item_price'];
		$pickup_state = $_SESSION['transaction']['merchant_state'];
		$delivery_state = $_SESSION['transaction']['customer_state'];
		$pickup_lga =$_SESSION['transaction']['merchant_lga'];
		$delivery_lga = $_SESSION['transaction']['customer_lga'];
		$weight = $_SESSION['transaction']['item_weight'];
		$quantity = $_SESSION['transaction']['item_quantity'];
		$merchant_name = $_SESSION['transaction']['merchant_name'];
        $merchant_contact = $_SESSION['transaction']['merchant_contact'];
		$merchant_email = $_SESSION['transaction']['merchant_email'];
		$merchant_address = $_SESSION['transaction']['merchant_address'];
        $customer_name =$_SESSION['transaction']['customer_name'];
		$customer_contact = $_SESSION['transaction']['customer_contact'];
		$customer_address = $_SESSION['transaction']['customer_address'];
		$customer_email =$_SESSION['transaction']['customer_email'];
		$delivery_cost = $_SESSION['shipping_price'];

		$item_count = count($item_name);

		for($i=0; $i<$item_count; $i++)
		{
			$items[] = array(
				'item_cost' 	=>  $item_price[$i],
				'item_name' 	=>  $item_name[$i],
				'item_size' 	=> '0',
				'item_weight' 	=> $weight[$i],
				'item_color' 	=>  'NULL',
				'item_quantity' =>$quantity[$i],
				'image_location' => 'NULL',
				'fragile' 		=> 0,
				'perishable' 	=> 0,
			);
		
		}

		$data['courier_id'] = $courier_id;
		$data['orders'] = array('items' => $items);
		$data['pickup_handling'] = $pickup_type;
		$data['delivery_handling'] = "to_customer";
		$data['pickup'] = array(
			'merchant_contactname' 	=> $merchant_name,
			'merchant_phone' 		=> $merchant_contact,
			'merchant_email' 		=> $merchant_email,
			'merchant_address' 		=> $merchant_address,
			'merchant_lga' 			=> $pickup_lga,
			'merchant_state' 		=> $pickup_state,
			'country' 				=> 'Nigeria',
		);

		$data['delivery'] = array(
			'customer_name'		=> $customer_name,
			'customer_email' 	=> $customer_email,
			'customer_phone'	=> $customer_contact,
			'customer_address'  => $customer_address,
			'customer_lga'		=> $delivery_lga,
			'customer_state'	=> $delivery_state,
			'country'			=> 'Nigeria',
		);
		$data['POD'] = 0;
		$is_pre_auth = 0;
		$payment_type = 3;
		$data['delivery_cost'] = $delivery_cost;
		$data['payment_type'] = $payment_type;
		$data['pre_auth'] = $is_pre_auth;

		$post = json_encode(['transaction' => $data]);

		$url = "http://new.saddleng.com/api/v2/delivery";
		$token = $this->get_token();

		$header = array('Content-Type: application/json', 'Authorization: Bearer '.$token);
		


		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS,$post);
		curl_setopt($ch,CURLOPT_HTTPHEADER, $header); 
		$result = curl_exec($ch);
		

		
		$res = json_decode($result);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

		curl_close($ch); 
		if($httpcode == 200)
		{
			if($courier_id == 'SAfceb761'){
				$courier_name = 'FEDEX';
				$courier_email = 'tibadu@redstarplc.com';
			}else if($courier_id == 'SA493a731'){
				$courier_name = "Courier Plus";
				$courier_email = 'o.osideko@courierplus-ng.com';
			}else if ($courier_id == 'SAf9fac5e'){
				$courier_name = 'UPS';
				$courier_email = 'onweke@ups.com';
			}else if($courier_id == 'SA505f6e8') {
				$courier_name = 'DHL';
				$courier_email = 'nginquiry@dhl.com';
			}else if($courier_id == 'SAa28a764'){
				$courier_name = 'Skynet';
				$courier_email = 'dare.onigbinde@skynetworldwide.com.ng';
			}else if($courier_id == ''){
				$courier_name = 'Muve';
				$courier_name = 'kareem_oritola@yahoo.com';
			}else{
				$courier_name = 'Courier';
				$courier_email = '';
			};

			$merchant_email_message = '<!DOCTYPE html>
			<html>
			<head lang="en">
			    <meta charset="UTF-8">
			    <title></title>
			</head>
			<body style="background-color: #f3f3f3; padding: 0; margin: 0;font-family: \'Calibri\', Arial, sans-serif;">
			<div class="outer-div" style="margin: 0 auto; width:600px;background-color: #fff;">
			    <table width="100%">
			        <tr>
			            <td colspan="6">
			                <div style="background-color: #f3f3f3;
			                        width:100%; height:100px; border-bottom: solid 3px #000000; padding:20px 0">
			                    <table>
			                        <tr>
			                            <td width="5%"></td>
			                            <td width="60%">
			                                <h1 style="color:#fff; font-size:36px; font-weight: normal"></h1>
			                            </td>
			                            <td width="30%">
			                                <img src="'.base_url().'/saddleng/img/logo.png" />
			                            </td>
			                            <td width="5%"></td>
			                        </tr>
			                    </table>
			                </div>
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			                <p>
			                    Dear <strong> '.$merchant_name.' </strong>,<br /><br />
			                    Your SendPackage request has been received and is being processed
								Our rep, will contact you to pick up your item(s) within 24 hours. You can track your package with ID: '.$order_id.' on Saddle  http://new.saddleng.com .
			                </p>
			            </td>
					</tr>


			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			              YOUR ORDER DETAILS
			            </td>
					</tr>
					
					<tr>
						<td colspan="6" style="padding:10px 20px;">
						Delivery Information:
						</td>
					</tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
							<table width="100%">
								<tr>
									<td width="40%" height="30">Name</td>
									<td width="60%">
										<div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
											'.$customer_name.'
										</div>
									</td>
								
								</tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$customer_address.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$customer_contact.'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$delivery_state.'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$delivery_lga.'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>
					<tr>

					</tr>
						<td colspan="6" style="padding:10px 20px;">
							<div style="border: solid 1px #ccc; background-color: #f3f3f3; padding: 15px;">
							<p><strong>Product(s) Information:</strong></p>
							<table width="100%" border="0" cellpadding="3" cellspacing="0" style="border-collapse: unset; font-size: 12px;">
								<tr style="color: #fff;background-color: #ccc;">
									<td style="padding-left: 15px">ITEM</td>
									<td><strong>ITEM PRICE</strong></td>
									<td><strong>QUANTITY</strong></td>
									<td><strong>WEIGHT</strong></td>
									<td>&nbsp;</td>
								</tr>';

								
								for($i=0; $i<$item_count; $i++)
								{	
								$merchant_email_message .='<tr>
									<td>'.$items[$i]['item_name'].'</td>
									<td>&#x20A6;'.$items[$i]['item_cost'].'</td>
									<td>'.$items[$i]['item_quantity'].'</td>
									<td>'.$items[$i]['item_weight'].'</td>
								</tr>';
								
								}
								$merchant_email_message .= '<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="2"><strong>Delivery Amount:</strong></td>
									<td>&#x20A6; '.$delivery_cost.'</td>
							 	 </tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="2"><strong>Grand Total: </strong></td>
									<td>&#x20A6; '.$delivery_cost.'</td>
							  </tr>
							</table>
						</td>
						<tr>
							<td colspan="6" style="padding:10px 20px;">
								<p style="text-align: center">
								You can also reach us on 08099990660 or email us at saddle@netplusadvisory.com
								</p>
							</td>
						</tr>
			        <tr>
			            <td colspan="6" style="padding:10px 20px; border-top: solid 1px #ccc;">
			                <table width="100%">
			                    <tr>
			                        <td width="60%">
			                            <p style="font-size:13px; color: #333;line-height: 30px">
			                                &copy; 2018, All rights reserved. Saddle
			                            </p>
			                        </td>

			                        <td width="40%">
			                            <a target="_blank" href="https://twitter.com/saddle"><img src="'.base_url().'assets/img/twitter.jpg" style="float:right; margin-left: 5px;" /></a>
			                            <a target="_blank" href="https://www.facebook.com/saddle"><img src="'.base_url().'assets/img/facebook.jpg" style="float:right; margin-left: 5px;" /></a>
			                        </td>
			                    </tr>
			                </table>
			            </td>
			        </tr>
			    </table>
			</div>
			</body>
			</html>
		   ';

		   $message = ($merchant_email_message);
		   $this->load->library('email');
		    $config['protocol']    = 'smtp';
			$config['smtp_host']    = 'ssl://smtp.gmail.com';
			$config['smtp_port']    = '465';
			$config['smtp_timeout'] = '7';
			$config['smtp_user']    = AdminEmail;
			$config['smtp_pass']    = 'Saddle7890';
			$config['charset']    = 'utf-8';
			$config['newline']    = "\r\n";
			$config['mailtype'] = 'text'; // or html
			$config['validation'] = TRUE; // bool whether to validate email or not    

			$config['mailtype'] = "html";

			$this->email->initialize($config);

			// $this->email->from(AdminEmail, AdminEmailName);

			$this->email->from(AdminEmail, AdminEmailName);
								 
			//$this->email->to($shippingDetail->email);
			$this->email->to($merchant_email, $merchant_name);


			$this->email->subject('Order on Saddle Send Package');

			$sent  = $this->email->message($message);
			$this->email->send($sent);


			$courier_email_message = '<!DOCTYPE html>
			<html>
			<head lang="en">
			    <meta charset="UTF-8">
			    <title></title>
			</head>
			<body style="background-color: #f3f3f3; padding: 0; margin: 0;font-family: \'Calibri\', Arial, sans-serif;">
			<div class="outer-div" style="margin: 0 auto; width:600px;background-color: #fff;">
			    <table width="100%">
			        <tr>
			            <td colspan="6">
			                <div style="background-color: #bcc0c6;
			                        width:100%; height:100px; border-bottom: solid 3px #000000; padding:20px 0">
			                    <table>
			                        <tr>
			                            <td width="5%"></td>

			                            <td width="30%">
			                                <img src="'.base_url().'/saddleng/img/logo.png" />
			                            </td>
			                            <td width="5%"></td>
			                        </tr>
			                    </table>
			                </div>
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			                <p>
			                    Dear <strong> '.$courier_name.' </strong>,<br /><br />
			                    You have an order on Saddle. 
			                </p>
			            </td>
			        </tr>
			        <tr>
			            <td colspan="" style="padding:10px 20px;">
			                Transaction Refrence: 
			            </td>

			            <td colspan="" style="padding:10px 20px;"> '.$order_id.'
			            </td>
					</tr>
					

					<tr>
			            <td colspan="6" style="padding:10px 20px;">
			               Pickup Information
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
							<table width="100%">
								<tr>
									<td width="40%" height="30">Name</td>
									<td width="60%">
										<div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
											'.$merchant_name.'
										</div>
									</td>
								
								</tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$merchant_address.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$merchant_contact.'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_state.'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_lga.'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>



			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			               Delivery Information
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
							<table width="100%">
								<tr>
			                        <td width="40%" height="30">Name</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$customer_name.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$customer_address.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$customer_contact.'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$delivery_state.'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$delivery_lga.'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>
					<tr>

					</tr>
						<td colspan="6" style="padding:10px 20px;">
							<div style="border: solid 1px #ccc; background-color: #f3f3f3; padding: 15px;">
							<p><strong>Product(s) Information:</strong></p>
							<table width="100%" border="0" cellpadding="3" cellspacing="0" style="border-collapse: unset; font-size: 12px;">
								<tr style="color: #fff;background-color: #ccc;">
									<td style="padding-left: 15px">ITEM</td>
									<td><strong>ITEM PRICE</strong></td>
									<td><strong>QUANTITY</strong></td>
									<td><strong>WEIGHT</strong></td>
									<td>&nbsp;</td>
								</tr>';

								
								for($i=0; $i<$item_count; $i++)
								{	
								$courier_email_message .='<tr>
									<td>'.$items[$i]['item_name'].'</td>
									<td>&#x20A6;'.$items[$i]['item_cost'].'</td>
									<td>'.$items[$i]['item_quantity'].'</td>
									<td>'.$items[$i]['item_weight'].'</td>
								</tr>';
								
								}
								$courier_email_message .= '<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="2"><strong>Delivery Amount:</strong></td>
									<td>&#x20A6; '.$delivery_cost.'</td>
							 	 </tr>
								<tr>
									<td>&nbsp;</td>
									<td>&nbsp;</td>
									<td colspan="2"><strong>Grand Total: </strong></td>
									<td>&#x20A6; '.$delivery_cost.'</td>
							  </tr>
							</table>
						</td>
						<tr>
							<td colspan="6" style="padding:10px 20px;">
								<p style="text-align: center">
								You can also reach us on 08099990660 or email us at saddle@netplusadvisory.com
								</p>
							</td>
						</tr>
			        <tr>
			            <td colspan="6" style="padding:10px 20px; border-top: solid 1px #ccc;">
			                <table width="100%">
			                    <tr>
			                        <td width="60%">
			                            <p style="font-size:13px; color: #333;line-height: 30px">
			                                &copy; 2018, All rights reserved. Saddle
			                            </p>
			                        </td>

			                        <td width="40%">
			                            <a target="_blank" href="https://twitter.com/saddle"><img src="'.base_url().'assets/img/twitter.jpg" style="float:right; margin-left: 5px;" /></a>
			                            <a target="_blank" href="https://www.facebook.com/saddle"><img src="'.base_url().'assets/img/facebook.jpg" style="float:right; margin-left: 5px;" /></a>
			                        </td>
			                    </tr>
			                </table>
			            </td>
			        </tr>
			    </table>
			</div>
			</body>
			</html>
		   ';

		    $courier_message = ($courier_email_message);


			$conf['mailtype'] = "html";

			$cemail = $this->email->initialize($config);

			$femail = $this->email->from(AdminEmail, AdminEmailName);

			//$femail = $this->email->from("manieabiodun@gmail.com", "Manie Joh");
								 
			//$this->email->to($shippingDetail->email);
			$temail = $this->email->to($courier_email, $courier_name);


			$semail = $this->email->subject('Order on Saddle Send Package');

			$sent  = $this->email->message($courier_message);
			$this->email->send($sent);


			$receiver_email_message = '<!DOCTYPE html>
			<html>
			<head lang="en">
			    <meta charset="UTF-8">
			    <title></title>
			</head>
			<body style="background-color: #f3f3f3; padding: 0; margin: 0;font-family: \'Calibri\', Arial, sans-serif;">
			<div class="outer-div" style="margin: 0 auto; width:600px;background-color: #fff;">
			    <table width="100%">
			        <tr>
			            <td colspan="6">
			                <div style="background-color: #bcc0c6;
			                        width:100%; height:100px; border-bottom: solid 3px #000000; padding:20px 0">
			                    <table>
			                        <tr>
			                            <td width="5%"></td>

			                            <td width="30%">
			                                <img src="'.base_url().'/saddleng/img/logo.png" />
			                            </td>
			                            <td width="5%"></td>
			                        </tr>
			                    </table>
			                </div>
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			                <p>
			                    Dear <strong> '.$customer_name.' </strong>,<br /><br />
								Please be informed that a package is on its way to you. 
								
			                </p>
			            </td>
			        </tr>
			        <tr>
			            <td colspan="" style="padding:10px 20px;">
			                Order No: 
			            </td>

			            <td colspan="" style="padding:10px 20px;"> '.$order_id.'
			            </td>
					</tr>
					<tr>
			            <td colspan="6" style="padding:10px 20px;">
			                <p>
								Visit http://new.saddleng.com to track your package.
			                </p>
			            </td>
			        </tr>
					

					
			      
					<tr>
						<td colspan="6" style="padding:10px 20px;">
							<p style="text-align: center">
							You can also reach us on 08099990660 or email us at saddle@netplusadvisory.com
							</p>
						</td>
					</tr>
			        <tr>
			            <td colspan="6" style="padding:10px 20px; border-top: solid 1px #ccc;">
			                <table width="100%">
			                    <tr>
			                        <td width="60%">
			                            <p style="font-size:13px; color: #333;line-height: 30px">
			                                &copy; 2018, All rights reserved. Saddle
			                            </p>
			                        </td>

			                        <td width="40%">
			                            <a target="_blank" href="https://twitter.com/saddle"><img src="'.base_url().'assets/img/twitter.jpg" style="float:right; margin-left: 5px;" /></a>
			                            <a target="_blank" href="https://www.facebook.com/saddle"><img src="'.base_url().'assets/img/facebook.jpg" style="float:right; margin-left: 5px;" /></a>
			                        </td>
			                    </tr>
			                </table>
			            </td>
			        </tr>
			    </table>
			</div>
			</body>
			</html>
		   ';

		    $customer_message = ($receiver_email_message);


			$conf['mailtype'] = "html";

			$conemail = $this->email->initialize($config);

			$frmemail = $this->email->from(AdminEmail, AdminEmailName);

			//$femail = $this->email->from("manieabiodun@gmail.com", "Manie Joh");
								 
			//$this->email->to($shippingDetail->email);
			$toemail = $this->email->to($customer_email, $customer_name);


			$subemail = $this->email->subject('Order on Saddle Send Package');

			$sent  = $this->email->message($customer_message);
			$this->email->send($sent);

			$this->load->view('layouts/header');
			$this->load->view('success');
			$this->load->view('layouts/footer');
			
		}
		
	}

  
}

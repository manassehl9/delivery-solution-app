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
		$data['states'] = $this->jit_model->get_state();
		$data['order_id'] = $this->get_order_id();
		$_SESSION['order_id'] = $data['order_id'];

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
    
    public function merchant_delivery_lga()
    {
        $state = $this->input->post('state');
        $lga =  $this->jit_model->get_lga($state);

	}
	
	public function fetch_shipping_details()
	{
		$details['weight'] = $this->input->post('weight');
		$details['merchant_state'] = $this->input->post('merchant_state');
		$details['merchant_lga'] = $this->input->post('merchant_lga');
		$details['customer_state'] = $this->input->post('customer_state');
		$details['customer_lga'] = $this->input->post('customer_lga');
		$_SESSION['details'] = $details;
		$this->shipping_method_price($details);
	}

	public function get_couriers()
	{
		$courier = $this->jit_model->get_courier();
		return $courier;
		
	}

	public function get_courier_id()
	{
		$_SESSION['courier_id'] = $this->input->post('courier_id');
		$_SESSION['shipping_price'] = $this->input->post('shipping_price');
	}

	public function get_order_id()
	{
		$order_id = 'SPCK'.date("Y").mt_rand(1000000, 9999999);  
		return  $order_id;
	}

	public function store_transaction_details()
	{
		$merchant_details['transaction_id'] = $_SESSION['order_id'];
		$merchant_details['merchant_name'] = $this->input->post('merchant_contactname');
		$merchant_details['merchant_contact'] = $this->input->post('merchant_phone');
		$merchant_details['merchant_email'] = $this->input->post('merchant_email');
		$merchant_details['merchant_address'] = $this->input->post('merchant_address');
		$merchant_details['merchant_state'] = $_SESSION['details']['merchant_state'];
		$merchant_details['merchant_lga'] = $_SESSION['details']['merchant_lga'];
		$transaction = $this->jit_model->get_transaction($merchant_details['transaction_id']);
		if(!$transaction)
		{
			$this->jit_model->store_merchant($merchant_details);
		}else{
			var_dump("update transaction table");
		}
		return $merchant_details;
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
		$order_id =  $_SESSION['order_id'];
		$this->jit_model->update_transaction($order_id);
	}

	public function shipping_method_price($data)
	{
		$couriers = $this->get_couriers();
		$total_couriers =  count($couriers);
		foreach($couriers as $courier)
		{
			$pickup_state = $data['merchant_state'];
			$pickup_lga = $data['merchant_lga'];
			$weight = $data['weight'];
			$delivery_state = $data['customer_state'];
			$delivery_lga = $data['customer_lga'];
			$url = 'http://new.saddleng.com/api/v2/shipping_price';
			$token = $this->get_token();
			$body = json_encode(array('delivery_state' => $delivery_state, 'pickup_state' => $pickup_state, 'delivery_lga'=> $delivery_lga, 'pickup_lga' => $pickup_lga, 'weight' => $weight, 'courier_id' => $courier->courier_id));
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
			} else if($httpcode == 404) {
				$shippingPrice = 0;
				
			}else{
				$shippingPrice = 0;
			}
			$array[] = array(
						'shipping_price'=>$shippingPrice,
						'courier_id'=>$courier->courier_id);
			
			
			}
			
			echo json_encode($array);

	}

	public function post_shipping()
	{
		$data['transaction_id'] = $_SESSION['order_id'];
		$pickup_type = 'mercht-loc';
		$data['courier_id'] = $_SESSION['courier_id'];
		$item_name = 'Sendpackage Item';
		$item_price = '0';
		$weight = $_SESSION['details']['weight'];
		
		$quantity = '0';
		
		$merchant_name = $this->input->post('merchant_name');
		$merchant_contact = $this->input->post('merchant_phone'); 
		$merchant_email =  $this->input->post('merchant_email');
		$merchant_address = $this->input->post('merchant_address');
		$merchant_state = $_SESSION['details']['merchant_state'];
		$merchant_lga = $_SESSION['details']['merchant_lga'];
		$customer_name = $this->input->post('customer_name');
		$customer_contact = $this->input->post('customer_phone'); 
		$customer_address = $this->input->post('customer_address');
		$customer_email =  $this->input->post('customer_email');
		$customer_state = $_SESSION['details']['customer_state'];
		$customer_lga = $_SESSION['details']['customer_lga'];
		$delivery_cost = $_SESSION['shipping_price'];
		
		$items[] = array(
			'item_cost' => $item_price,
			'item_name' => $item_name,
			'item_size' => '0',
			'item_weight' => $weight,
			'item_color' => 'NULL',
			'item_quantity' => $quantity,
			'image_location' => 'NULL',
			'fragile' => 0,
			'perishable' => 0,
		);

		$data['orders'] = array('items' => $items);
		$data['pickup_handling'] = $pickup_type;
		$data['delivery_handling'] = "to_customer";
		$data['pickup'] = array(
			'merchant_contactname' 	=> $merchant_name,
			'merchant_phone' 		=> $merchant_contact,
			'merchant_email' 		=> $merchant_email,
			'merchant_address' 		=> $merchant_address,
			'merchant_lga' 			=> $merchant_lga,
			'merchant_state' 		=> $merchant_state,
			'country' 				=> 'Nigeria',
		);
		$data['delivery'] = array(
			'customer_name'		=> $customer_name,
			'customer_email' 	=> $customer_email,
			'customer_phone'	=> $customer_contact,
			'customer_address'  => $customer_address,
			'customer_lga'		=> $customer_lga,
			'customer_state'	=> $customer_state,
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
			
			//First update transaction
			$this->update_transactions();

			// Get selected courier details from the Database
			$courier =  $this->jit_model->get_courier_details($data['courier_id']);
			$courier_name =  $courier->courier_name;
			$courier_email = $courier->email;

			$merchant_email_message =  $this->merchant_email($data);

		    $message = ($merchant_email_message);
		    $this->load->library('email');
		    $config['protocol']    = 'smtp';
			$config['smtp_host']    = 'ssl://smtp.gmail.com';
			$config['smtp_port']    = '465';
			$config['smtp_timeout'] = '7';
			$config['smtp_user']    = ADMIN_EMAIL;
			$config['smtp_pass']    = ADMIN_PASSWORD;
			$config['charset']    = 'utf-8';
			$config['newline']    = "\r\n";
			$config['mailtype'] = 'text'; // or html
			$config['validation'] = TRUE; // bool whether to validate email or not    

			$config['mailtype'] = "html";

			$this->email->initialize($config);
			$this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$this->email->to($merchant_email, $merchant_name);
			$this->email->subject('Order on Saddle Send Package');

			$send_merchant_email  = $this->email->message($message);
			$this->email->send($send_merchant_email);

			//Send email to courier
			$courier_email_message = $this->courier_email($data, $courier_name);
		    $courier_message = ($courier_email_message);


			$conf['mailtype'] = "html";
			$cemail = $this->email->initialize($config);
			$femail = $this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);

								 
			$temail = $this->email->to($courier_email, $courier_name);
			$semail = $this->email->subject('Order on Saddle Send Package');
			$send_courier_email  = $this->email->message($courier_message);
			$this->email->send($send_courier_email);


			// Reciever email 
			$receiver_email_message = $this->reciever_email($data);
		    $customer_message = ($receiver_email_message);


			$conf['mailtype'] = "html";

			$conemail = $this->email->initialize($config);

			$frmemail = $this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$toemail = $this->email->to($customer_email, $customer_name);
			$subemail = $this->email->subject('Order on Saddle Send Package');

			$send_customer_email  = $this->email->message($customer_message);
			$this->email->send($send_customer_email);

			//Email sent to Netplus
			$netplus_email_message = $this->netplus_email($courier_name, $data);

		    $netplus_message = ($netplus_email_message);


			$conf['mailtype'] = "html";

			$cemail = $this->email->initialize($config);
			$femail = $this->email->from(ADMIN_EMAIL, ADMIN_EMAIL_NAME);
			$temail = $this->email->to(ADMIN_EMAIL, 'Sendpackage');
			$semail = $this->email->subject('Order on Saddle Send Package');

			$send_netplus_email  = $this->email->message($netplus_message);
			$this->email->send($send_netplus_email);
			
			$this->load->view('layouts/header');
			$this->load->view('success');
			$this->load->view('layouts/footer');
			$this->session->sess_destroy();
			
		}else{
			$this->session->sess_destroy();
		}
	}




	public function merchant_email($data)
	{
		return '<!DOCTYPE html>
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
			                    Dear <strong> '.$data['pickup']['merchant_contactname'].' </strong>,<br /><br />
			                    Your SendPackage request has been received and is being processed
								Our rep, will contact you to pick up your item(s) within 24 hours. You can track your package with ID: '.$data['transaction_id'].' on Saddle  http://new.saddleng.com .
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
											'.$data['delivery']['customer_name'].'
										</div>
									</td>
								
								</tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$data['delivery']['customer_address'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$data['delivery']['customer_phone'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_lga'].'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_state'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>
					<tr>

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
	}

	public function courier_email($data, $courier_name) 
	{
		return '<!DOCTYPE html>
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

			            <td colspan="" style="padding:10px 20px;"> '.$data['transaction_id'].'
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
											'.$data['pickup']['merchant_contactname'].'
										</div>
									</td>
								
								</tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$data['pickup']['merchant_address'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$data['pickup']['merchant_phone'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['pickup']['merchant_state'].'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['pickup']['merchant_lga'].'
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
			                                '.$data['delivery']['customer_name'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$data['delivery']['customer_address'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$data['delivery']['customer_phone'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_state'].'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_lga'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>
					<tr>

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
	}

	public function reciever_email($data)
	{
		return '<!DOCTYPE html>
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
			                    Dear <strong> '.$data['delivery']['customer_name'].' </strong>,<br /><br />
								Please be informed that a package is on its way to you. 
								
			                </p>
			            </td>
			        </tr>
			        <tr>
			            <td colspan="" style="padding:10px 20px;">
			                Order No: 
			            </td>

			            <td colspan="" style="padding:10px 20px;"> '.$data['transaction_id'].'
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
	}

	public function netplus_email($courier_name, $data)
	{
		return '<!DOCTYPE html>
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
			                    Dear <strong> Sendpackage </strong>,<br /><br />
			                    An order has been place by a Merchant. Kindly reach out to <strong> '.$courier_name.' </strong>
			                </p>
			            </td>
			        </tr>
			        <tr>
			            <td colspan="" style="padding:10px 20px;">
			                Transaction Refrence: 
			            </td>

			            <td colspan="" style="padding:10px 20px;"> '.$data['transaction_id'].'
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
											'.$data['pickup']['merchant_contactname'].'
										</div>
									</td>
								
								</tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$data['pickup']['merchant_address'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$data['pickup']['merchant_phone'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['pickup']['merchant_state'].'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['pickup']['merchant_lga'].'
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
			                                '.$data['delivery']['customer_name'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$data['delivery']['customer_address'].'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$data['delivery']['customer_phone'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_state'].'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#bcc0c6; color: #000; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$data['delivery']['customer_lga'].'
			                            </div>
			                        </td>
			                        
			                    </tr>
			                </table>
			            </td>
			        </tr>
					<tr>

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
	}

	

	

}

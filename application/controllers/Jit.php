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
        $transaction_id = 'JIT'.date("Y").mt_rand(1000000, 9999999);  
        $_SESSION['order_id'] = $transaction_id;
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
        
        $data['total_item_price'] = (int)$data['item_price'] * (int)$data['item_quantity'];
		$_SESSION['total_amount'] = $data['total_item_price'];
		
        $_SESSION['transaction'] = $data;
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


    public function shipping_method_price()
    {
		$courier =  $this->jit_model->get_courier_id($_SESSION['courier']);
	
		if($courier) {
			$courier_id = $courier->courier_id;
		}else{
			$courier_id = '';
		}
		

		$item_price = $_SESSION['transaction']['item_price'];
		$delivery_state = $_SESSION['transaction']['merchant_state'];
		$pickup_state = $_SESSION['transaction']['customer_state'];
		$delivery_lga =$_SESSION['transaction']['merchant_lga'];
		$pickup_lga = $_SESSION['transaction']['customer_lga'];
		$weight = $_SESSION['transaction']['item_weight'];
		$quantity = $_SESSION['transaction']['item_quantity'];
		$courier_id = $courier_id;

		$_SESSION['select_courier'] = $courier_id;

		$weight = (int)$weight * (int)$quantity;
		$item_price = (int)$item_price * (int)$quantity;

		$url = 'http://new.saddleng.com/api/v2/shipping_price';
		$token = $this->get_token();
		$body = json_encode(array('delivery_state' => $delivery_state, 'pickup_state' => $pickup_state, 'pickup_lga' => $pickup_lga, 'delivery_lga'=> $delivery_lga, 'weight' => $weight, 'courier_id' => $courier_id));
		$header = array('Content-Type: application/json', 
		'Authorization: Bearer '.$token);

		//var_dump($body);
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
		$body = json_encode(array('login' => 'DapoA', 'password' => 'password'));
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
	public function netpluspay()
	{
		
		$returnUrl ='http://sendpackage.saddleng.com/jit/netpluspay_success';
		$merchantId = 'TEST5a81735b2a429';
		$merchant_name = $_SESSION['transaction']['merchant_name'];

		//https://netpluspay.com/testpayment/paysrc/
		?>
		<body onload="document.getElementById('netpluspay_form').submit();">
		<form method="POST" id="netpluspay_form" name="netpluspay_form" action="https://netpluspay.com/pay/test/" >
			<input type="hidden" name="full_name" value="<?php echo $merchant_name; ?>">
			<input type="hidden" name="email" value="manassehl9@gmial.com">
			<input type="hidden" name="merchantid" value="<?php echo $merchantId;?>">
			<input type="hidden" name="currency" value="NGN">
			<input type="hidden" name="narration" value="Order from Send Package">
			<input type="hidden" name="orderid" value="<?php echo $_SESSION['order_id']; ?>">
			<input type="hidden" name="amount" value="<?php echo $_SESSION['shipping_price']; ?>">
			<input type="hidden" name="return_url" value="http://sendpackage.saddleng.com/jit/netpluspay_success">
			<input type="hidden" name="recurring" value="no">
		</form>
		</body>
		<?php
	}

	public function netpluspay_success()
	{
		$transaction_id = $_POST['trans_id'];
		
	
		$merchantId = 'TEST5a81735b2a429';
		$url = 'http://api-test.netpluspay.com/transactions/requery/'.$merchantId.'/'.$transaction_id.'';
		

		$json_xml = file_get_contents($url);
        $response_object = json_decode($json_xml);
        $status_code = $response_object->code;
		if($status_code == '00')
		{
			$this->courier();
		}else{

			echo 'payment failed';
		}
		
	}

	public function courier()
	{
		$order_id = $_SESSION['order_id'];
		$pickup_type = 'mercht-loc';
		$data['transaction_id'] = $order_id;
		$courier_id = $_SESSION['select_courier'];

		$item_name = $_SESSION['transaction']['item_name'];
		$item_price = $_SESSION['transaction']['item_price'];
		$delivery_state = $_SESSION['transaction']['merchant_state'];
		$pickup_state = $_SESSION['transaction']['customer_state'];
		$delivery_lga =$_SESSION['transaction']['merchant_lga'];
		$pickup_lga = $_SESSION['transaction']['customer_lga'];
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
		$delivery_cost = $_SESSION['total_amount'];

		$items[] = array(
			'item_cost' 	=> $item_price,
			'item_name' 	=> $item_name,
			'item_size' 	=> '',
			'item_weight' 	=> $weight,
			'item_color' 	=> '',
			'item_quantity' => $quantity,
			'image_location' => '',
			'fragile' 		=> 0,
			'perishable' 	=> 0,
		);

		$data['courier_id'] = $courier_id;
		$data['orders'] = array('items' => $items);
		$data['pickup_handling'] = $pickup_type;
		$data['delivery_handling'] = "to_customer";
		$data['pickup'] = array(
			'merchant_contactname' 	=> $merchant_name,
			'merchant_phone' 		=> $merchant_contact,
			'merchant_email' 		=> $merchant_email,
			'merchant_address' 		=> $merchant_address,
			'merchant_lga' 			=> $delivery_lga,
			'merchant_state' 		=> $delivery_state,
			'country' 				=> 'Nigeria',
		);

		$data['delivery'] = array(
			'customer_name'		=> $customer_name,
			'customer_email' 	=> $customer_email,
			'customer_phone'	=> $customer_contact,
			'customer_address'  => $customer_address,
			'customer_lga'		=> $pickup_lga,
			'customer_state'	=> $pickup_state,
			'country'			=> 'Nigeria',
		);
		$data['POD'] = 0;
		$is_pre_auth = 1;
		$payment_type = 2;
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
				$courier = 'FEDEX';
			}else if($courier_id == 'SAed7352a'){
				$courier = 'EDCR Courier';
			}else if($courier_id == 'SA493a731'){
				$courier = "Courier Plus";
			}else{
				$courier = 'Courier';
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
			                <div style="background-color: #2d0700;
			                        width:100%; height:100px; border-bottom: solid 3px #000000; padding:20px 0">
			                    <table>
			                        <tr>
			                            <td width="5%"></td>
			                            <td width="60%">
			                                <h1 style="color:#fff; font-size:36px; font-weight: normal">Congratulations!</h1>
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
			                    Dear <strong> Merchant </strong>,<br /><br />
			                    you have completed a “send package” request on Saddle. You can track your order using your transaction ID: '.$order_id.'. You can expect your product(s) delivered within the next 24hours. Your order details are as follows;.
			                </p>
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
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$customer_address.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$customer_contact.'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_state.'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_lga.'
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
								</tr>
								<tr>
									<td>'.$items[0]['item_name'].'</td>
									<td>&#x20A6;'.$items[0]['item_cost'].'</td>
									<td>'.$items[0]['item_quantity'].'</td>
									<td>'.$items[0]['item_weight'].'</td>
								</tr>
								<tr>
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
								For more information and support, please call 08099990660
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
			$config['smtp_pass']    = 'Netmanie93';
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
			                <div style="background-color: #2d0700;
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
			                    Dear <strong> '.$courier.' </strong>,<br /><br />
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
			               Delivery Information
			            </td>
			        </tr>

			        <tr>
			            <td colspan="6" style="padding:10px 20px;">
			                <table width="100%">
			                    <tr>
			                        <td width="40%" height="30">Address</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px; padding: 0 10px;">
			                                '.$customer_address.'
			                            </div>
			                        </td>
			                      
			                    </tr>
			                    <tr>
			                        <td width="40%" height="30">Phone</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                               '.$customer_contact.'
			                            </div>
			                        </td>
			                        
			                    </tr>
		                        <tr>
			                        <td width="40%" height="30">State</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_state.'
			                            </div>
			                        </td>
			                        
								</tr>
								<tr>
			                        <td width="40%" height="30">LGA</td>
			                        <td width="60%">
			                            <div style="background-color:#2d0700; color: #fff; height:30px; line-height:30px;
			                            padding: 0 10px;">
			                                '.$pickup_lga.'
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
								</tr>
								<tr>
									<td>'.$items[0]['item_name'].'</td>
									<td>&#x20A6;'.$items[0]['item_cost'].'</td>
									<td>'.$items[0]['item_quantity'].'</td>
									<td>'.$items[0]['item_weight'].'</td>
								</tr>
								<tr>
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
								For more information and support, please call  08099990660
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
			$temail = $this->email->to($merchant_email, $merchant_name);


			$semail = $this->email->subject('Order on Saddle Send Package');

			$sent  = $this->email->message($courier_message);
			$this->email->send($sent);

			$this->load->view('layouts/header');
			$this->load->view('success');
			$this->load->view('layouts/footer');
			
		}
		
	}

  
}

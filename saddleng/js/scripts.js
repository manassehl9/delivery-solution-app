jQuery(document).ready(function() {
	window.onbeforeunload = function() { return "You may loose all data. Kindly click on the previous button"; };
	$('#loader').hide();

	$("#netplus-pay").click(function (e) {
		var order_id = $('#order_id').val();
		var total_amount = $('#total_amount').val();
		var merchant_id = "TEST5b0c3742ac4ce";
		//var merchant_id = "MID5b0c3ac715d976.33987466";
		var merchant_name = $('#merchant_name').val();
		var merchant_email = $('#merchant_email').val();

		e.preventDefault();
		netpluspayPOP.setup(
			{
				merchant: merchant_id,
				customer_name: merchant_name,
				email: merchant_email,
				amount: total_amount,
				currency_code:"NGN",
				narration: "Order from Send Package",
				order_id: order_id,
				container: "paymentFrame",
				onClose  : function() {
					this.closeIframe();
					window.location = window.location.origin;
				},
				callBack: function (resp) {
					var func = function() {
						$.ajax({
							type: "GET",
							url: "/jit/courier/",
							success: function(data){
								//window.location = window.location.origin;
							}
						});
					}

					if (resp.code == '00') {
						func();
					}
				}
				
			}
			
		);
		netpluspayPOP.prepareFrame();
    });

	$('#merchantDeliverystate').change(function(){
		$states = $(this).val();
		var state = $('#merchantDeliverystate option:selected').text();
		$.ajax({
			type: "POST",
			url: "/jit/merchant_delivery_lga",
			data: {"state": state},
			success: function(data){
				$('#merchantDeliverylga').html(data);
			}
		});
	});



	$('#customerDeliverystate').change(function(){
		$states = $(this).val();
		var state = $('#customerDeliverystate option:selected').text();
		$.ajax({
			type: "POST",
			url: "/jit/customer_delivery_lga",
			data: {"state": state},
			success: function(data){
				$('#customerDeliverylga').html(data);
			}
		});
	});


	$('#selectCourier').change(function(){

		$("#coupon_code").prop('disabled', false);
		$('#coupon_code').val("");		
		$('#loader').show();
		hideInputField();
		$('#coupon_text').hide();
		$('#coupon_code').hide();

		var item_name = new Array();
		var item_price = new Array();
		var item_quantity = new Array();
		var item_weight = new Array();


		$('input[name^="item_name"]').each(function() {
			item_name.push($(this).val());
		});
		$('input[name^="item_quantity"]').each(function() {
			item_quantity.push($(this).val());
		});
		$('input[name^="item_price"]').each(function() {
			item_price.push($(this).val());
		});
		$('input[name^="item_weight"]').each(function() {
			item_weight.push($(this).val());
		});

		var total_price = 0;
		for (var i = 0; i < item_name.length; i++) {
			total_price += parseInt(item_price[i]) * parseInt(item_quantity[i]);
		}

		var courier = $('#selectCourier option:selected').text();
		var merchant_name = $('#merchant_name').val();
		var merchant_contact = $('#merchant_contact').val();
		var merchant_email = $('#merchant_email').val();
		var merchant_address = $('#merchant_address').val();
		var merchant_state = $('#merchantDeliverystate').val();
		var merchant_lga = $('#merchantDeliverylga').val();
		var customer_contact = $('#customer_contact').val();
		var customer_email = $('#customer_email').val();
		var customer_address = $('#customer_address').val();
		var customer_name = $('#customer_name').val();
		var customer_state = $('#customerDeliverystate').val();
		var customer_lga = $('#customerDeliverylga').val();

		$.ajax({
			type: "POST",
			url: "/jit/get_order_details",
			data: {"item_name": item_name, "item_quantity": item_quantity, "item_price": item_price, "item_quantity": item_quantity,
					"item_weight": item_weight, "merchant_name": merchant_name, "merchant_contact": merchant_contact, "merchant_email": merchant_email,
					"merchant_address": merchant_address, "merchant_state": merchant_state, "merchant_lga": merchant_lga, "customer_name": customer_name,
					"customer_contact":customer_contact,"customer_email": customer_email, "customer_address": customer_address, "customer_state": customer_state, 
					"customer_lga": customer_lga},
			success: function(data){
				$.ajax({
					type: "POST",
					url: "/jit/get_courier_name",
					data: {"courier": courier},
					success: function(data){
						$.ajax({
							type: "GET",
							url: "/jit/shipping_method_price/",
							success: function(shipping_price)
							{
								var price = JSON.parse(shipping_price);
								$('#item_cost').val(total_price);
								var item_amount = $('#item_cost').val();
								if(price.shipping_price > 0){
									showInputField();
									$('#coupon_text').show();
									$('#coupon_code').show();
									$('#netplus-pay').show();
									$('#shipping_cost').val(price.shipping_price);
									$('#total_amount').val(price.shipping_price);
			
								}else{
									alert("No delivery quotes!!! Select a different shipping location");
									showInputField();
									$('#coupon_text').show();
									$('#coupon_code').show();
									$('#netplus-pay').hide();
									$('#item_cost').val(total_price);
									$('#shipping_cost').val('0.00');
									$('#total_amount').val('0.00');
								}
							},
							complete: function() {
								$('#loader').hide();
							},
							error: function() {
								alert("There was an error. Try again please!");
								showInputField();
								$('#coupon_text').show();
								$('#coupon_code').show();
							}
						});
					}
				});
			}

		});
		
	});


	//Coupon code
	$('#coupon_code').on("input", function(e){
		var coupon_code = this.value;
		var coupon_len = coupon_code.length;
		console.log(coupon_len);

		var selected_courier  = $('#selectCourier option').filter(':selected').text();
		if(coupon_len < 10){

		}else if(coupon_len == 10){
			
			if(selected_courier.toLowerCase() === 'Select Courier'.toLowerCase()){
				alert("Select a courier");
			}else{
				$('#loader').show();
				hideInputField();
				$.ajax({
					type: "GET",
					url: "/jit/shipping_method_price/",
					success: function(shipping_price)
					{
						var price = JSON.parse(shipping_price);
						var item_amount = $('#item_cost').val();
						if(price.shipping_price > 0){	

							$.ajax({
								type: "POST",
								url: "/jit/get_coupon/",
								data: {"coupon_code": coupon_code},
								success: function(coupon_value){
									var coupon = JSON.parse(coupon_value);
									if(coupon.coupon_value > 0){
										var coupon_value = coupon.coupon_value;
										var delivery_fee = $('#shipping_cost').val();
										var discount = parseFloat(delivery_fee) * parseFloat(coupon_value);
										var new_delivery_fee = parseFloat(delivery_fee) - parseFloat(discount);
										$("input").prop('disabled', true);
										showInputField();
										$('#shipping_cost').val(new_delivery_fee.toFixed(2));
										$('#total_amount').val(new_delivery_fee.toFixed(2));
										$('#netplus-pay').show();	
									}else{
										showInputField();
										alert("invalid coupon");
									}
								
								},
								error: function(coupon_value)
								{
									alert("Error while loading");
								}
							});
							
						}else{
							alert("No delivery quotes!!! Select a courier");
							showInputField();
							
							$('#netplus-pay').hide();
							$('#item_cost').val(item_amount);
							$('#shipping_cost').val('0.00');
							$('#total_amount').val('0.00');
						}
					},
					complete: function() {
						$('#loader').hide();
						$('#shipping_cost').val();
						$('#total_amount').val();
						//$('#netplus-pay').show();
	
					},
					error: function() {
						alert("There was an error. Try again please!");
					}
				});
			}
			
		}else{
			console.log("invalid coupon");
			alert("Invalid Coupon");
		}	
	});

	function hideInputField() {
		$('#item_cost').hide();
		$('#shipping_cost').hide();
		$('#total_amount').hide();
		$('#item_cost_label').hide();
		$('#shipping_cost_label').hide();
		$('#total_amount_label').hide();
	}

	function showInputField() {
		$('#item_cost_label').show();
		$('#shipping_cost_label').show();
		$('#total_amount_label').show();
		$('#item_cost').show();
		$('#shipping_cost').show();
		$('#total_amount').show();
	}

	
    /*
        Fullscreen background
    */
    $.backstretch("assets/img/backgrounds/1.jpg");
    
    $('#top-navbar-1').on('shown.bs.collapse', function(){
    	$.backstretch("resize");
    });
    $('#top-navbar-1').on('hidden.bs.collapse', function(){
    	$.backstretch("resize");
    });
    
    /*
        Form
    */
    $('.registration-form fieldset:first-child').fadeIn('slow');
    
    $('.registration-form input[type="text"], .registration-form input[type="number"], .registration-form input[type="email"], .registration-form input[type="password"], .registration-form textarea').on('focus', function() {
    	$(this).removeClass('input-error');
    });
    
    // next step
    $('.registration-form .btn-next').on('click', function() {
    	var parent_fieldset = $(this).parents('fieldset');
		var next_step = true;
		$('#netplus-pay').hide();
		$("#selectCourier option:selected").prop("selected", false);
		
		function ValidateEmail(email) {
			var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			return expr.test(email);
		};

		function ValidateNumber(number) {
			var exp = /^[0]\d{10}$/;
			return exp.test(number);
		}
		

		parent_fieldset.find('input[type="email"]').each(function() {
			
				if (!ValidateEmail($("#customer_email").val()) && (!ValidateEmail($("#merchant_email").val()))) {
					$("#customer_email").addClass('input-error');
					$("#merchant_email").addClass('input-error');
					next_step = false;
				}else if(!ValidateEmail($("#customer_email").val())){
					$("#customer_email").addClass('input-error');
				}
				else if(!ValidateEmail($("#merchant_email").val())){
					$("#merchant_email").addClass('input-error');
					next_step = false;
				}else{

				}

		});
		

    	parent_fieldset.find('input[type="text"], input[type="number"], select[name="merchantDeliverystate"],   select[name="customerDeliverystate"],  select[name="merchantDeliverylga"], select[name="customerDeliverylga"],  input[type="password"], textarea').each(function() {
    		if( $(this).val() == "" ) {
				$(this).addClass('input-error');
				// implement the contact hereif()
    			next_step = false;
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    	
    	if( next_step ) {
    		parent_fieldset.fadeOut(400, function() {
	    		$(this).next().fadeIn();
	    	});
    	}
    	
    });
    
    // previous step
    $('.registration-form .btn-previous').on('click', function() {
    	$(this).parents('fieldset').fadeOut(400, function() {
    		$(this).prev().fadeIn();
    	});
    });
    
    // submit
    $('.registration-form').on('submit', function(e) {
		
    	$(this).find('input[type="text"],  input[type="number"],  input[type="email"], input[type="password"], textarea').each(function() {
    		if( $(this).val() == "" ) {
    			e.preventDefault();
    			$(this).addClass('input-error');
    		}
    		else {
    			$(this).removeClass('input-error');
    		}
    	});
    	
    });
    
    
});

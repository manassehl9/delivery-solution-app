$(document).ready(function() {
	$('.btn-next').on('click', function() {
		var parent_section = $(this).parents('section');
		
		var next_step = true;

		parent_section.find('select[name="item_weight"], select[name="merchantState"],   select[name="customerState"], select[name="merchantLga"], select[name="customerLga"]').each(function() {
			if($(this).val() == "") {
				$(this).addClass('input-error');
				next_step = false;
			}else{
				$(this).removeClass('input-error');
			}
		});

		if( next_step ) {
			// Get shipping price for each courier
			$('.btn-prev').hide();
			$('.btn-next').hide();
			$('div.span_1_of_5').hide();
			var weight = $('#item_weight').val();
			var merchant_state = $('#merchantState').val();
			var merchant_lga = $('#merchantLga').val();
			var customer_state = $('#customerState').val();
			var customer_lga = $('#customerLga').val();
			$.ajax({
				type: "POST",
				url: "/jit/fetch_shipping_details",
				data: {"weight": weight, "merchant_state": merchant_state, 
					"merchant_lga": merchant_lga, "customer_state": customer_state,
					"customer_lga": customer_lga},
				success: function(data)
				{
					var value = JSON.parse(data);
					console.log(value);
					
					var j = value.length;
					var fedex_price = value[0].shipping_price;
					if(fedex_price > 0){
						$('h3#courier0').html('&#8358;'+fedex_price);
					}else{
						$('h3#courier0').html('N/A');
					}
					$('h3#courierid0').val(value[0].courier_id);
					$("h4.courier-one").text("SELECT");
					$('.courier').css('box-shadow', '');
					$('.courier').css('border', '');

					var ups_price = value[1].shipping_price;
					if(ups_price > 0){
						$('h3#courier1').html('&#8358;'+ups_price);
					}else{
						$('h3#courier1').html('N/A');
					}
					$('h3#courierid1').val(value[1].courier_id);
					$("h4.courier-two").text("SELECT");
					$('.courier1').css('box-shadow', '');
					$('.courier1').css('border', '');
					
					var dhl_price = value[2].shipping_price;
					if(dhl_price > 0){
						$('h3#courier2').html('&#8358;'+dhl_price);
					}else{
						$('h3#courier2').html('N/A');
					}
					$('h3#courierid2').val(value[2].courier_id);
					$("h4.courier-three").text("SELECT");
					$('.courier2').css('box-shadow', '');
					$('.courier2').css('border', '');

					var skynet_price = value[3].shipping_price;
					if(skynet_price > 0){
						$('h3#courier3').html('&#8358;'+skynet_price);
					}else{
						$('h3#courier3').html('N/A');
					}
					$('h3#courierid3').val(value[3].courier_id);
					$("h4.courier-four").text("SELECT");
					$('.courier3').css('box-shadow', '');
					$('.courier3').css('border', '');

					var muve_price = value[4].shipping_price;
					if(muve_price > 0){
						$('h3#courier4').html('&#8358;'+muve_price);
					}else{
						$('h3#courier4').html('N/A');
					}
					$('h3#courierid4').val(value[4].courier_id);
					$("h4.courier-five").text("SELECT");
					$('.courier4').css('box-shadow', '');
					$('.courier4').css('border', '');
				},
				complete: function(){
					$('.btn-prev').show();
					$("#loader").css("display", "none");
					$('div.span_1_of_5').show();

				}
			});

    		parent_section.fadeOut(400, function() {
				$("#loader").css("display", "block");
				$(this).next().fadeIn();
	    	});
    	}
		
	});


	//previous step
	$('.btn-prev').on('click', function() {
		$('.btn-next').show();
		$(this).parents('section').fadeOut(400, function() {
		
		var page = $(this).parents('section').attr('class');
		if('first-page' == page){
			$('.btn-next').show();
			$("#loader").css("display", "block");
		}else if('second-page' == page){
			$('.btn-next').hide();
		
		}else if('third-page' ==  page){
			$(this).prev().fadeIn();
			$("#loader").css("display", "none");
			
		}else{
			$(this).prev().fadeIn();
		}
    	});
	});

	//pay button
	$('#netplus-pay').on('click', function(e) {
		e.preventDefault();
		next_step = true;
		var parent_section = $(this).parents('section');

		function ValidateEmail(email) {
			var expr = /^([\w-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([\w-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$/;
			return expr.test(email);
		};

		parent_section.find('input[type="email"]').each(function() {
			var customer_email = $("#customer_email").val();
			var merchant_email = $("#merchant_email").val();
			if (!ValidateEmail(customer_email)) {
				$("#customer_email").addClass('input-error');
				next_step = false;
			  } else {
				$("#customer_email").removeClass('input-error');
				
			  }


			  if (!ValidateEmail(merchant_email)) {
				$("#merchant_email").addClass('input-error');
				next_step = false;
			  } else {
				$("#merchant_email").removeClass('input-error');
				
			  }
	

		});
		

		parent_section.find('input[type="text"]', 'input[type="email"]').each(function() {
			
			if($(this).val() == "") {
				$(this).addClass('input-error');
				next_step = false;
			}else{
				$(this).removeClass('input-error');
			}
		});

		if(next_step) {
			// var item_weight = $('#item_weight').val();
			var merchant_contactname = $('#merchant_contactname').val();
			var merchant_phone = $('#merchant_phone').val();
			var merchant_email= $('#merchant_email').val();
			var merchant_address = $('#merchant_address').val();
			var merchant_state = $('#merchantState').val();
			var merchant_lga = $('#merchantLga').val();
			var customer_name = $('#customer_name').val();
			var customer_email = $('#customer_email').val();
			var customer_phone = $('#customer_phone').val();
			var customer_address = $('#customer_address').val();
			var customer_state = $('#customerState').val();
			var customer_lga = $('#customerLga').val();
			
			

			$.ajax({
				type: "POST",
				url: "/jit/store_transaction_details",
				data: {"merchant_contactname": merchant_contactname,
						"merchant_phone": merchant_phone, "merchant_email": merchant_email, "merchant_address": merchant_address,
						"merchant_state": merchant_state, "merchant_lga": merchant_lga},
				success: function() {
					var order_id = $('#order_id').val();
					console.log(order_id);
					//var merchant_id = "TEST5b0c3742ac4ce";
					var merchant_id = "MID5b0c3ac715d976.33987466";
					netpluspayPOP.setup(
						{
							merchant: merchant_id,
							customer_name: merchant_contactname,
							email: merchant_email,
							amount: shipping_price,
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
										type: "POST",
										url: "/jit/post_shipping",
										data: {"merchant_contactname": merchant_contactname,
										"merchant_phone": merchant_phone, "merchant_email": merchant_email, "merchant_address": merchant_address, "customer_name": customer_name,"customer_phone": customer_phone, "customer_address": customer_address, 
										"customer_email": customer_email, "customer_state": customer_state, "customer_lga": customer_lga},
										success: function(data){
											
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

				}
			});
			
		}
	});





	// Fedex
	$('.courier').on('click', function() {
		var parent_section = $(this).parents('section');
	
		$("h4.courier-one").text("SELECTED");
		$("h4.courier-two").text("SELECT");
		$("h4.courier-three").text("SELECT");
		$("h4.courier-four").text("SELECT");
		$("h4.courier-five").text("SELECT");

		$(this).parent().find('.courier').css('box-shadow', '1px 1px 1px 1px #999');
		$(this).parent().find('.courier').css('border', '2px solid #f69147');
		$('.courier1').css('box-shadow', '');
		$('.courier1').css('border', '');
		$('.courier2').css('box-shadow', '');
		$('.courier2').css('border', '');
		$('.courier3').css('box-shadow', '');
		$('.courier3').css('border', '');
		$('.courier4').css('box-shadow', '');
		$('.courier4').css('border', '');
		$('.courier5').css('box-shadow', '');
		$('.courier5').css('border', '');

		var price = $('h3#courier0').text();
		var courier_id = $('#courierid0').val();
		shipping_price = parseInt(price.substring(1));
		console.log(courier_id);

		if(shipping_price > 0) {
			$.ajax({
				type: "POST",
				url: "/jit/get_courier_id",
				data: {"courier_id": courier_id, "shipping_price": shipping_price},
				success: function() {
					
					parent_section.fadeOut(400, function() {
						$(this).next().fadeIn();
					});
				}
			});
			
		}
	});


	// Ups
	$('.courier1').on('click', function() {
		var parent_section = $(this).parents('section');


		$("h4.courier-one").text("SELECT");
		$("h4.courier-two").text("SELECTED");
		$("h4.courier-three").text("SELECT");
		$("h4.courier-four").text("SELECT");
		$("h4.courier-five").text("SELECT");

		$('.courier').css('box-shadow', '');
		$('.courier').css('border', '');
		$(this).parent().find('.courier1').css('box-shadow', '1px 1px 1px 1px #999');
		$(this).parent().find('.courier1').css('border', '2px solid #f69147');
		$('.courier2').css('box-shadow', '');
		$('.courier2').css('border', '');
		$('.courier3').css('box-shadow', '');
		$('.courier3').css('border', '');
		$('.courier4').css('box-shadow', '');
		$('.courier4').css('border', '');

		var price = $('h3#courier1').text();
		var courier_id = $('#courierid1').val();
		shipping_price = parseInt(price.substring(1));
		if(shipping_price > 0) {
			$.ajax({
				type: "POST",
				url: "/jit/get_courier_id",
				data: {"courier_id": courier_id, "shipping_price": shipping_price},
				success: function() {
					parent_section.fadeOut(400, function() {
						$(this).next().fadeIn();
					});
				}
			});
			
		}


	});


	// Dhl
	$('.courier2').on('click', function() {
		var parent_section = $(this).parents('section');


		$("h4.courier-one").text("SELECT");
		$("h4.courier-two").text("SELECT");
		$("h4.courier-three").text("SELECTED");
		$("h4.courier-four").text("SELECT");
		$("h4.courier-five").text("SELECT");

		$('.courier').css('box-shadow', '');
		$('.courier').css('border', '');
		$('.courier1').css('box-shadow', '');
		$('.courier1').css('border', '');
		$(this).parent().find('.courier2').css('box-shadow', '1px 1px 1px 1px #999');
		$(this).parent().find('.courier2').css('border', '2px solid #f69147');
		$('.courier3').css('box-shadow', '');
		$('.courier3').css('border', '');
		$('.courier4').css('box-shadow', '');
		$('.courier4').css('border', '');
		
		var price = $('h3#courier2').text();
		var courier_id = $('#courierid2').val();
		shipping_price = parseInt(price.substring(1));
		if(shipping_price > 0) {
			$.ajax({
				type: "POST",
				url: "/jit/get_courier_id",
				data: {"courier_id": courier_id, "shipping_price": shipping_price},
				success: function() {
					parent_section.fadeOut(400, function() {
						$(this).next().fadeIn();
					});
				}
			});
			
		}

	});

	// Skynet
	$('.courier3').on('click', function() {
		var parent_section = $(this).parents('section');

		$("h4.courier-one").text("SELECT");
		$("h4.courier-two").text("SELECT");
		$("h4.courier-three").text("SELECT");
		$("h4.courier-four").text("SELECTED");
		$("h4.courier-five").text("SELECT");


		$('.courier').css('box-shadow', '');
		$('.courier').css('border', '');
		$('.courier1').css('box-shadow', '');
		$('.courier1').css('border', '');
		$('.courier2').css('box-shadow', '');
		$('.courier2').css('border', '');
		$(this).parent().find('.courier3').css('box-shadow', '1px 1px 1px 1px #999');
		$(this).parent().find('.courier3').css('border', '2px solid #f69147');
		$('.courier4').css('box-shadow', '');
		$('.courier4').css('border', '');
		
		var price = $('h3#courier3').text();
		var courier_id = $('#courierid3').val();
		shipping_price = parseInt(price.substring(1));
		if(shipping_price > 0) {
			$.ajax({
				type: "POST",
				url: "/jit/get_courier_id",
				data: {"courier_id": courier_id, "shipping_price": shipping_price},
				success: function() {
					parent_section.fadeOut(400, function() {
						$(this).next().fadeIn();
					});
				}
			});
		}
	});

	//Courier Plus
	$('.courier4').on('click', function() {
		var parent_section = $(this).parents('section');

		$("h4.courier-one").text("SELECT");
		$("h4.courier-two").text("SELECT");
		$("h4.courier-three").text("SELECT");
		$("h4.courier-four").text("SELECT");
		$("h4.courier-five").text("SELECTED");

		$('.courier').css('box-shadow', '');
		$('.courier').css('border', '');
		$('.courier1').css('box-shadow', '');
		$('.courier1').css('border', '');
		$('.courier2').css('box-shadow', '');
		$('.courier2').css('border', '');
		$('.courier3').css('box-shadow', '');
		$('.courier3').css('border', '');
		$(this).parent().find('.courier4').css('box-shadow', '1px 1px 1px 1px #999');
		$(this).parent().find('.courier4').css('border', '2px solid #f69147');

		var price = $('h3#courier4').text();
		var courier_id = $('#courierid4').val();
		shipping_price = parseInt(price.substring(1));
		if(shipping_price > 0) {
			$.ajax({
				type: "POST",
				url: "/jit/get_courier_id",
				data: {"courier_id": courier_id, "shipping_price": shipping_price},
				success: function() {
					parent_section.fadeOut(400, function() {
						$(this).next().fadeIn();
					});
				}
			});
		}
	});


	// Get LGA for selected STATE
	$('#merchantState').change(function(){
		$states = $(this).val();
		var state = $('#merchantState option:selected').text();
		$.ajax({
			type: "POST",
			url: "/jit/merchant_delivery_lga",
			data: {"state": state},
			success: function(data){
				$('.form-box #merchantLga').html(data);
			}
		});
	});


	$('.form-box #customerState').change(function(){
		$states = $(this).val();
		var state = $('#customerState option:selected').text();
		$.ajax({
			type: "POST",
			url: "/jit/merchant_delivery_lga",
			data: {"state": state},
			success: function(data){
				$('.form-box #customerLga').html(data);
			}
		});
	});


	
	
	
});
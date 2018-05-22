<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Saddle</title>

    <!-- Bootstrap -->
    <link href="/saddleng/css/bootstrap.min.css" rel="stylesheet">
    <link href="/saddleng/css/style.css" rel="stylesheet">
    <link href="/saddleng/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/saddleng/css/form-elements.css">
    <link rel="stylesheet" href="/saddleng/css/style1.css">

    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">

        <div class="navbar-header">

          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle Navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span> 
          </button>

          <a class="navbar-brand" href="#">
            <div class="logo">
                <img src="/saddleng/img/logo.png"/>
            </div>
          </a>

        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#photo">SEND A PACKAGE</a></li>
                <li><a href="#photo">PRICING</a></li>
                <li><a href="#download">CONTACT US</a></li>
            </ul>
        </div>
      </div>
    </div>

    <style>
      strong {
        color: #F69147;
      }
    </style>


    <!-- Top content -->
    <section id="package">
        <div class="top-content">
          
            <div class="inner-bg">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 text" align="center">
                            <h1><strong>Send a Package</strong> Start by filling the required details below</h1>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-8 col-sm-offset-2 form-box">
                          
                          <form role="form" action="<?php echo base_url();?>jit/netpluspay" method="post" class="registration-form">
                        
                            <fieldset>
                              <div class="form-top">
                                <div class="form-top-left">
                                  <h3>Step 1 /3</h3>
                                    <p>Package details:</p>
                                </div>
                                <div class="form-top-right">
                                  
                                </div>
                                </div>
                                <div class="form-bottom">
                              <div class="form-group col-md-6">
                                  <label class="control-label">Item Name:</label>
                                  <input type="text" name="item_name" placeholder="Item Name" class="item_namae form-control" id="item_name">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Item Quantity:</label>
                                  <input type="number" name="item_quantity" placeholder="Item Quantity" class="item_quantity form-control" id="item_quantity">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Item Weight(KG):</label>
                                  <input type="number" name="item_weight" placeholder="Item Weight" class="item_weight form-control" id="item_weight">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Item item_price:</label>
                                  <input type="number" name="item_price" placeholder="Item Price" class="item_price form-control" id="item_price">
                                </div>
                                <button type="button" class="btn btn-next">Next</button>
                            </div>
                            
                          </fieldset>
                          
                          <fieldset>
                              <div class="form-top">
                                <div class="form-top-left">
                                  <h3>Step 2 / 3</h3>
                                    <p>Merchant/Customer Details:</p>
                                </div>
                                <div class="form-top-right">
                                </div>
                                </div>
                                <div class="form-bottom">
                                
                                <div class="form-group col-md-6">
                                  <label class="control-label">Merchant Name:</label>
                                  <input type="text" name="merchant_name" placeholder="Merchant Name" class="merchant_name form-control" id="merchant_name">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Customer name:</label>
                                  <input type="text" name="customer_name" placeholder="Customer Name" class="customer_name form-control" id="customer_name">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Merchant Phone:</label>
                                  <input type="text" name="merchant_contact" placeholder="Merchant Contact" class="merchant_contact form-control" id="merchant_contact">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Customer Phone:</label>
                                  <input type="text" name="customer_contact" placeholder="Customer Contact" class="customer_contact Number form-control" id="customer_contact">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Merchant Email:</label>
                                  <input type="email" name="merchant_email" placeholder="Merchant Email" class="merchant_email email form-control" id="merchant_email">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Customer Email:</label>
                                  <input type="email" name="customer_email" placeholder="Customer email" class="customer_email form-control" id="customer_email">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Merchant Address:</label>
                                  <input type="text" name="merchant_address" placeholder="Merchant address" class="merchant_address form-control" id="merchant_address">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Customer Address:</label>
                                  <input type="text" name="customer_address" placeholder="Customer address" class="customer_address form-control" id="customer_address">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Merchant State:</label>
                                  <select class="form-control select" name="merchantDeliverystate" id="merchantDeliverystate" required>
                                    <option value="">Merchant State</option>
                                    <?php 
                                    if($states)
                                        {
                                        foreach($states as $row)
                                        {
                                    ?>
                                     <option value="<?php echo $row->name; ?>"><?php echo $row->name; ?></option>
                                     <?php
                                        }
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Customer State:</label>
                                  <select class="form-control select" name="customerDeliverystate" id="customerDeliverystate" required>
                                    <option value="">Customer State</option>
                                    <?php 
                                    if($states)
                                        {
                                        foreach($states as $row)
                                        {
                                    ?>
                                     <option value="<?php echo $row->name; ?>"><?php echo $row->name; ?></option>
                                     <?php
                                        }
                                    }
                                    ?>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                <label class="control-label">Merchant LGA:</label>
                                  <select class="form-control select" name="merchantDeliverylga" id="merchantDeliverylga" required>
                                    <option value="">Merchant LGA</option>
                                  </select>
                                </div>
                                <div class="form-group col-md-6">
                                <label class="control-label">Customer LGA:</label>
                                  <select class="form-control select" name="customerDeliverylga" id="customerDeliverylga" required>
                                    <option value="">Customer LGA</option>
                                  </select>
                                </div>
                                <button type="button" class="btn btn-previous">Previous</button>
                                <button type="button" class="btn btn-next">Next</button>
                                
                            </div>
                          </fieldset>

                          <fieldset>
                              <div class="form-top">
                                <div class="form-top-left">
                                  <h3>Step 3 /3</h3>
                                    <p>Courier details:</p>
                                </div>
                                <div class="form-top-right">
                                  
                                </div>
                              </div>
                                <div class="form-bottom">
                                    <div class="form-group col-md-6">
                                        <div class="form-group col-md-6">
                                        <select class="form-control select"  name="selectCourier" id="selectCourier">
                                            <option>Courier</option>
                                         <?php 
                                            if($couriers)
                                                {
                                                foreach($couriers as $row)
                                                {
                                            ?>
                                            <option value="<?php echo $row->courier_name; ?>"><?php echo $row->courier_name; ?></option>
                                        <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                     </div>
                                </div>
                                <div class="form-bottom">
                                    <div class="form-group col-md-12">
                                        <label class="control-label">Item Cost:</label>
                                        <input type="text" value="0.00" class="form-control" id="item_cost">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label">Shippping Fee:</label>
                                        <input type="text" value="0.00" class="form-control" id="shipping_cost">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label">Total Amount:</label>
                                        <input type="text" value="0.00" class="form-control" id="total_amount">
                                    </div>
                                </div>
                             
                              <br>
                              <br>
                              <hr>
                              <button type="button" class="btn btn-previous">Previous</button>
                            <button type="submit" class="btn btn-success" id="send_package" style="display:none;">Send Package!</button>
                            
                          </fieldset>
                        
                        </form>
                        
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </section>




    

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="/saddleng/js/bootstrap.min.js"></script>
    <script src="/saddleng/js/jquery.backstretch.min.js"></script>
    <script src="/saddleng/js/retina-1.1.0.min.js"></script>
    <script src="/saddleng/js/scripts.js"></script>
  </body>
</html>
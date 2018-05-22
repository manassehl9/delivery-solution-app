


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
                                  <label class="control-label">Item:</label>
                                  <input type="text" name="item_name" placeholder="Item" class="item_namae form-control" id="item_name">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Item Quantity:</label>
                                  <input type="number" name="item_quantity" placeholder="Item Quantity" class="item_quantity form-control" id="item_quantity">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Item Weight(KG):</label>
                                  <input type="number" name="item_weight" placeholder="Estimated Weight" class="item_weight form-control" id="item_weight">
                                </div>
                                <div class="form-group col-md-6">
                                  <label class="control-label">Price:</label>
                                  <input type="number" name="item_price" placeholder="Price" class="item_price form-control" id="item_price">
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
                                <legend style="width:auto">Merchant Details:</legend>
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Merchant Name:</label>
                                    <input type="text" name="merchant_name" placeholder="Name" class="merchant_name form-control" id="merchant_name">
                                  </div>
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Merchant Phone:</label>
                                    <input type="text" name="merchant_contact" placeholder="Contact" class="merchant_contact form-control" id="merchant_contact">
                                  </div>
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Merchant Email:</label>
                                    <input type="email" name="merchant_email" placeholder="Email" class="merchant_email email form-control" id="merchant_email">
                                  </div>
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Merchant Address:</label>
                                    <input type="text" name="merchant_address" placeholder="Address" class="merchant_address form-control" id="merchant_address">
                                  </div>
                                  <div class="form-group col-md-12">
                                  <label class="control-label">Merchant State:</label>
                                  <select class="form-control select" name="merchantDeliverystate" id="merchantDeliverystate" required>
                                    <option value="">State</option>
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
                                  <div class="form-group col-md-12">
                                  <label class="control-label">Merchant LGA:</label>
                                    <select class="form-control select" name="merchantDeliverylga" id="merchantDeliverylga" required>
                                      <option value="">LGA</option>
                                    </select>
                                  </div>
                                </div>
                                
                                <legend>Customer Details:</legend>
                                <div class="form-group col-md-6">
                                 
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Customer name:</label>
                                    <input type="text" name="customer_name" placeholder="Name" class="customer_name form-control" id="customer_name">
                                  </div>
                                  
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Customer Phone:</label>
                                    <input type="text" name="customer_contact" placeholder="Contact" class="customer_contact Number form-control" id="customer_contact">
                                  </div>
                                  
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Customer Email:</label>
                                    <input type="email" name="customer_email" placeholder="Email" class="customer_email form-control" id="customer_email">
                                  </div>
                                  
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Customer Address:</label>
                                    <input type="text" name="customer_address" placeholder="Address" class="customer_address form-control" id="customer_address">
                                  </div>
                                  
                                  <div class="form-group col-md-12">
                                    <label class="control-label">Customer State:</label>
                                    <select class="form-control select" name="customerDeliverystate" id="customerDeliverystate" required>
                                      <option value="">State</option>
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
                                  <div class="form-group col-md-12">
                                  <label class="control-label">Customer LGA:</label>
                                    <select class="form-control select" name="customerDeliverylga" id="customerDeliverylga" required>
                                      <option value="">LGA</option>
                                    </select>
                                  </div>
                                  
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
                                            <option>Select Courier</option>
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
                                        <label class="control-label">Delivery Fee:</label>
                                        <input type="text" value="0.00" class="form-control" id="shipping_cost">
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label class="control-label">Total Payment:</label>
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



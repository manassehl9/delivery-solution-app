<form action="#" method="post" class="registration-form">
<div id="paymentFrame"></div> 
    <section id="middle" class="first-page">
      <div class="middle">
        <div class="container">
          <div class="row">
            <div class="col-lg-6">
              <div class="middle-lhs">
                <h3>BE ONE OF 50 PEOPLE THAT GET N1000 AIRTIME THIS MONTH</h3>
                <p>Send a package and share service with your friends and stand a chance to get N1,000 worth of airtime, ask us how? </p>
                <img src="/assets/images/cards_images.jpg" class="img-fluid" />
              </div>
            </div>


            <div class="col-lg-6">
              <div class="middle-rhs">
                <h4>ENJOY EASE WITH YOUR DELIVERY</h4>
                <div class="form-container">

                  <div class="form-box">
                    <p><strong>What are we sending out today?</strong></p>
                    <select name="item_weight" id="item_weight" class="form-txt2">
                      <option class="form-option" value="">SELECT</option>
                      <option value="0.5">Package up to 0.5kg</option>
                      <option value="1">Package up to 1kg</option>
                      <option value="1.5">Package up to 1.5kg</option>
                      <option value="2">Package up to 2kg</option>
                      <option value="2.5">Package up to 2.5kg</option>
                      <option value="3">Package up to 3kg</option>
                      <option value="3.5">Package up to 3.5kg</option>
                      <option value="4">Package up to 4kg</option>
                      <option value="4.5">Package up to 4.5kg</option>
                      <option value="5">Package up to 5kg</option>
                      <option value="5.5">Package up to 5.5kg</option>
                      <option value="6">Package up to 6kg</option>
                      <option value="6.5">Package up to 6.5kg</option>
                      <option value="7">Package up to 7kg</option>
                      <option value="7.5">Package up to 7.5kg</option>
                      <option value="8">Package up to 8kg</option>
                      <option value="8.5">Package up to 8.5kg</option>
                      <option value="9">Package up to 9kg</option>
                      <option value="9.5">Package up to 9.5kg</option>
                      <option value="10">Package up to 10kg</option>
                    </select>
                  </div>

                  <div class="form-box">
                    <p><strong>Where is your pickup location?</strong></p>
                    <select name="merchantState" id="merchantState" class="form-txt">
                       <option class="form-option" value="">STATE</option>
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

                      <select name="merchantLga" id="merchantLga" class="form-txt3">
                        <option class="form-option" value="">LGA</option>
                      </select>


                  <div class="form-box">
                     <p><strong>Where are we delivering to?</strong></p>
                     <select name="customerState" id="customerState" class="form-txt">
                       <option value="" class="form-option">STATE</option>
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

                      <select name="customerLga" id="customerLga" class="form-txt3">
                        <option class="form-option" value="">LGA</option>
                      </select>

                  <div><br>
                    <a class="btn btn-next button" style="color:white">Next</a>
                  </div>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- End of first page -->


    <!-- Second page -->
    <section id="middle" class="second-page">
      <div class="middle">
        <div class="container">
          <h3 class="courier-text">CHOOSE YOUR PREFERED COURIER</h3>
          
          <div class="section group" align="center">
            <div class="col span_1_of_5">
              
              <a href="#" style="text-decoration:none;">

                <div class="courier">
                <input type="radio" name="radiobtn" id="radiobtn" value="courer-one" style="display:none"/>
                  <div class="courier-logo" align="center">
                    <img class="img-fluid" src="/assets/images/cp_logo.jpg" />
                  </div>

                  <div class="courier-name" align="center">
                    Courier Plus

                  </div>

                  <div class="price" align="center">
                      <h3 id="courier0">N1000</h3>
                      <input type="hidden" id="courierid0" value="SA493a731" />
                  </div>
                  <p  style="font-size:12px">1 - 3days</p>
                  <div class="select" align="center" >
                    <h4 class="courier-one">SELECT</h4>
                  </div>
                </div>
              </a>
            </div>

            <div class="col span_1_of_5">
             
              <a href="#" style="text-decoration:none;">
               <div class="courier1">
                <input type="radio"  class="radiobtn" id="radiobtn" value="courer-two" style="display:none"/>
                 <div class="courier-logo2" align="center">
                   <img class="img-fluid" src="/assets/images/upss_logo.jpg" />
                 </div>

                 <div class="courier-name2" align="center">
                   UPS
                 </div>

                 <div class="price2" align="center">
                     <h3 id="courier1">N1000</h3>
                     <input type="hidden" id="courierid1" value="SAf9fac5e" />
                 </div>
                 <p  style="font-size:12px">1 - 3days</p>

                 <div class="select" align="center" >
                   <h4 class="courier-two">SELECT</h4>
                 </div>

               </div>
              </a>
            </div>

            <div class="col span_1_of_5">
              <input type="radio" id="radiobtn" value="courer-three" style="display:none"/>
              <a href="#" style="text-decoration:none;">
                <div class="courier2">
                  <div class="courier-logo" align="center">
                    <img class="img-fluid" src="/assets/images/dhl_logo.jpg" />
                  </div>

                  <div class="courier-name" align="center">
                    DHL
                  </div>

                  <div class="price" align="center">
                    <h3 id="courier2">N1000</h3>
                    <input type="hidden" id="courierid2" value="SA505f6e8" />
                  </div>
                  <p  style="font-size:12px">1 - 3days</p>
                  <div class="select" align="center" >
                    <h4 class="courier-three">  SELECT </h4>
                  </div>

                </div>
              </a>
            </div>
            <div class="col-md-6 col-md-offset-2" id="loader"> 
              <svg width="200px"  height="200px"  xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="xMidYMid" class="lds-ripple" style="background: none;">
                <circle cx="50" cy="50" r="10.7529" fill="none" ng-attr-stroke="{{config.c1}}" ng-attr-stroke-width="{{config.width}}" stroke="#F07B2C" stroke-width="2">
                  <animate attributeName="r" calcMode="spline" values="0;15" keyTimes="0;1" dur="0.9" keySplines="0 0.2 0.8 1" begin="-0.45s" repeatCount="indefinite"></animate>
                  <animate attributeName="opacity" calcMode="spline" values="1;0" keyTimes="0;1" dur="0.9" keySplines="0.2 0 0.8 1" begin="-0.45s" repeatCount="indefinite"></animate>
                </circle>
                <circle cx="50" cy="50" r="2.47627" fill="none" ng-attr-stroke="{{config.c2}}" ng-attr-stroke-width="{{config.width}}" stroke="#ffffff" stroke-width="2">
                  <animate attributeName="r" calcMode="spline" values="0;15" keyTimes="0;1" dur="0.9" keySplines="0 0.2 0.8 1" begin="0s" repeatCount="indefinite"></animate>
                  <animate attributeName="opacity" calcMode="spline" values="1;0" keyTimes="0;1" dur="0.9" keySplines="0.2 0 0.8 1" begin="0s" repeatCount="indefinite"></animate>
                </circle>
              </svg>
            </div>
            <div class="col span_1_of_5">
              <input type="radio" id="radiobtn" value="courer-four" style="display:none"/>
              <a href="#" style="text-decoration:none;">
                <div class="courier3">
                  <div class="courier-logo" align="center">
                    <img class="img-fluid" src="/assets/images/sn_logo.jpg" />
                  </div>

                  <div class="courier-name" align="center">
                    SkyNet
                  </div>
                  

                  <div class="price" align="center">
                   <h3 id="courier3"><span>&#8358;</span></h3>
                    <input type="hidden" id="courierid3" value="SAa28a764" />
                  </div>
                  <p  style="font-size:12px">Next day delivery</p>

                  <div class="select" align="center" >
                    <h4 class="courier-four">SELECT</h4>
                  </div>
                 
                </div>
              </a>
            </div>

            <div class="col span_1_of_5">
              <input type="radio" id="radiobtn" value="courier-five" style="display:none"/>
              <a href="#" style="text-decoration:none;">
                <div class="courier4">
                  <div class="courier-logo" align="center">
                    <img class="img-fluid" src="/assets/images/muve_logo.jpg"  style="width:80px; height: 30px;"/>
                  </div>

                  <div class="courier-name" align="center">
                  Muve 
                  </div>

                  <div class="price" align="center">
                    <h3 id="courier4">N1000</h3>
                    <input type="hidden" id="courierid4" value="SA98bbd50" />
                  </div>

                  <p style="font-size:12px">Same day delivery</p>

                  <div class="select" align="center">
                    <h4 class="courier-five">  SELECT </h4>
                  </div>

                </div>
              </a>
            </div>
          </div>
          
          <div>
            <a class="btn btn-prev" style="border-radius:2px; background-color: #F69147; margin-bottom: 10px;  font-weight: lighter; font-size: 15px; margin-left: 20px; color:white">Back</a>
          </div>
        </div>
      </div>
      
    
    </section>
    <!-- End of second page -->

    <!-- third page -->                
    <section id="middle" class="third-page" style="border-top:1px solid #ccc;">
    <div class="container">
      <div class="details-middle">
        <div class="row">
          <div class="col-lg-6">
            <div class="middle-lhs2">
              <h4>Kindly fill your details below</h4>

                <div style="font-weight:bold; margin-top: 30px; color: #f69147;">Sender's Details</div>

                  <div class="input-style">
                    <input data-name="name" type="text" data-required name="name" id="merchant_contactname" placeholder="Sender's Full Name" class="form_space" required>
                  </div>

                  <div class="input-style">
                    <input data-name="phone" type="text" data-required name="phone" id="merchant_phone" placeholder="Sender's Phone Number" class="form_space" required>
                  </div>

                  <div class="input-style">
                    <input data-name="email" type="email" data-required name="phone" id="merchant_email" placeholder="Sender's Email Address" class="form_space" required>
                  </div>


                  <div class="input-style">
                      <input data-name="senders-address" type="text" data-required name="senders-address" id="merchant_address" placeholder="Sender's Address" class="textarea" required>
                  </div>
                  
                </div>
                
              </div>


              <div class="col-lg-6">
                <div class="middle-rhs2">
                  <div style="font-weight:bold; margin-top: 30px; color: #f69147;">Receiver's Details</div>

                  <div class="input-style">
                      <input data-name="name" type="text" data-required name="name" id="customer_name" placeholder="Receiver's Full Name" class="form_space" required>
                  </div>

                  <div class="input-style">
                    <input data-name="phone" type="text" data-required name="phone" id="customer_phone" placeholder="Receiver's Phone Number" class="form_space" required>
                  </div>

                  <div class="input-style">
                    <input data-name="email" type="email" data-required name="phone" id="customer_email" placeholder="Receiver's Email Address" class="form_space" required>
                  </div>

                  <div class="input-style">
                      <input data-name="senders-address" type="text" data-required name="receivers-address" id="customer_address" placeholder="Receiver's Address" class="textarea" required>
                  </div>
                  <input type="hidden" value="<?php echo $order_id; ?>" class="form-control" id="order_id">
                  <div class="input-style">

                  <a class="btn btn-prev" style="color:#fff; border-radius:2px; float: left; background-color: #F69147; display:block;">Back</a>
                  
                  <!-- <input id="submit" type="submit" name="pay" class="button2" value="SEND PACKAGE"> -->
                   <a class="btn btn-pay" id="netplus-pay"  style="color:#fff; border-radius:2px; float: right; background-color: #F69147;">Pay</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    </section>
    <!-- End of third page -->

    
    </form>
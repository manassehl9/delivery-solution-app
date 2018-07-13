<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120827714-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-120827714-1');
    </script>
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

    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Encode+Sans+Condensed" rel="stylesheet">
    <link href='https://fonts.googleapis.com/css?family=Lato' rel='stylesheet' type='text/css'>

    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-120857321-1"></script>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="/saddleng/js/scripts.js"></script>

    <script>
      var i = 0;
	    function insertItem(elementId) {
		    ++i;
		    $('<div style="border-top: 1px solid lightgray; margin-top: 25px" id="dynamic_field'+i+'" id="row'+i+'"><br><br><br><div class="form-group col-md-6 col-xs-6"  id="item_name'+i+'"><label class="control-label">Item Name:</label><input type="text" name="item_name[]" placeholder="Item Name" class="item_namae form-control" id="item_name"></div><div class="form-group col-md-6 col-xs-6"  id="item_quantity'+i+'"><label class="control-label">Item Quantity:</label><input type="number" name="item_quantity[]" placeholder="Item Quantity" class="item_quantity form-control" id="item_quantity"></div><div class="form-group col-md-6 col-xs-6" id="item_weight'+i+'"><label class="control-label">Item Weight(KG):</label><input type="number" name="item_weight[]" placeholder="Item Weight" class="item_weight form-control" id="item_weight"></div><div class="form-group col-md-6 col-xs-6" id="item_price'+i+'"><label class="control-label">Item price:</label><input type="number" name="item_price[]" placeholder="Item Price" class="item_price form-control" id="item_price"></div><a class="btn btn-add" style="color: #F69147;" id="add-item" title="Add new item" href="javascript: insertItem(\'dynamic_field'+i+'\')">Add Item</a> <span>|</span><a class="btn btn_remove" style="color: #F69147;" id='+i+' >Delete</a><br><br></div>').insertAfter('#'+elementId);
      }
      $(document).on('click', '.btn_remove', function() {
        var button_id = $(this).attr("id");
        $("#dynamic_field"+button_id+"").remove();
      });
    </script>

  </head>

  <body>
    <div class="navbar navbar-default" role="navigation">
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
            <a href="http://new.saddleng.com"> <img src="/saddleng/img/logo.png"/> </a>
            </div>
          </a>

        </div>

        <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#" style="color: #FF7F20">SEND A PACKAGE</a></li>
                <li><a href="http://new.saddleng.com">CONTACT US</a></li>
            </ul>
        </div>
      </div>
    </div>

    <style>
      strong {
        color: #F69147;
      }
    </style>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" style="width:100%;font-family:open sans, helvetica neue, helvetica, arial, sans-serif;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%;padding:0;Margin:0">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <style type="text/css">
    	body{
			margin-top:20px;
			background:#fff;
}

/*Invoice*/
.invoice .top-left {
    font-size:65px;
	color:#3ba0ff;
}

.invoice .top-right {
	text-align:right;
	padding-right:20px;
}

.invoice .table-row {
	margin-left:-15px;
	margin-right:-15px;
	margin-top:25px;
}

.invoice .payment-info {
	font-weight:500;
}

.invoice .table-row .table>thead {
	border-top:0px solid #ddd;
	background-color:#4d39e9;
	color:#fff;
}

.invoice .table-row .table>thead>tr>th {
	border-bottom:none;
}

.invoice .table>tbody>tr>td {
	padding:8px 20px;
}

.invoice .invoice-total {
	margin-right:-10px;
	font-size:16px;
}

.invoice .last-row {
	border-bottom:1px solid #ddd;
}

.invoice-ribbon {
	width:85px;
	height:88px;
	overflow:hidden;
	position:absolute;
	top:-1px;
	right:14px;
}

.ribbon-inner {
	text-align:center;
	-webkit-transform:rotate(45deg);
	-moz-transform:rotate(45deg);
	-ms-transform:rotate(45deg);
	-o-transform:rotate(45deg);
	position:relative;
	padding:7px 0;
	left:-5px;
	top:11px;
	width:120px;
	/* background-color:#66c591;*/
	background-color:#4d39e9;
	font-size:15px;
	color:#fff;
}

.ribbon-inner:before,.ribbon-inner:after {
	content:"";
	position:absolute;
}

.ribbon-inner:before {
	left:0;
}

.ribbon-inner:after {
	right:0;
}

@media(max-width:575px) {
	.invoice .top-left,.invoice .top-right,.invoice .payment-details {
		text-align:center;
	}

	.invoice .from,.invoice .to,.invoice .payment-details {
		float:none;
		width:100%;
		text-align:center;
		margin-bottom:25px;
	}

	.invoice p.lead,.invoice .from p.lead,.invoice .to p.lead,.invoice .payment-details p.lead {
		font-size:22px;
	}

	.invoice .btn {
		margin-top:10px;
	}
}

@media print {
	.invoice {
		width:100%;
		height:800px;
	}
}
    </style>
<body>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container bootstrap snippets bootdeys">
<div class="row">
  <div class="col-sm-12">
	  	<div class="panel panel-default invoice" id="invoice">
		  <div class="panel-body">
			<div class="invoice-ribbon"><div class="ribbon-inner">PAID</div></div>
		    <div class="row">
				
				<div class="col-sm-6 top-left">
					<img src="'.$logo.'" width="200"/>
				</div>

				<div class="col-sm-6 top-right">
						<h3 class="marginright">INVOICE-'.$TransID.'</h3>
						<span class="marginright">'.date("D, d F Y H:i:s").'</span>
			    </div>

			</div>
			<hr>
			<div class="row">

				<div class="col-xs-6 from">
					<p class="lead marginbottom">From :<b> Cloud Rebue</b></p>
					<p>Phone: +254708361797</p>
					<p>Email: info@cloudrebue.co.ke</p>
				</div>

				<div class="col-xs-6 to">
					<p class="lead marginbottom">To :<b> '.$company.'</b></p>
					<p>Phone: '.$phone.'</p>
					<p>Email: '.$email.'</p>

			    </div>

			</div>

			<div class="row table-row">
				<table class="table table-striped">
			      <thead>
			        <tr>
			          <th class="text-center" style="width:5%">#</th>
			          <th style="width:60%">Item</th>
			          <th class="text-right" style="width:35%">Value</th>
			        </tr>
			      </thead>
			      <tbody>
			        <tr>
			          <td class="text-center">1</td>
			          <td>Bulk SMS Units</td>
			          <td class="text-right">'.$units.' Units</td>
			        </tr>
			        <tr>
			          <td class="text-center">2</td>
			          <td>Worth</td>
			          <td class="text-right">Ksh '.$total_amount.'</td>
			        </tr>
			       </tbody>
			    </table>

			</div>

			<div class="row">
			<div class="col-xs-6 margintop">
				<p class="lead marginbottom">THANK YOU!</p>
				
			</div>
			<div class="col-xs-6 text-right pull-right invoice-total">
					  <p><b>Subtotal</b> : Ksh '.$total_amount.'</p>
			          <p>Pricing Ksh '.$pricing.'/unit </p>
			          <p><b>Total</b> : Ksh '.$total_amount.' </p>
			</div>
			</div>

		  </div>
		</div>
	</div>
</div>
</div>
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</body>
</html>
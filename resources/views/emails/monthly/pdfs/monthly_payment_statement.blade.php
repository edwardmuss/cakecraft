<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Monthly Payment Statement</title>
    
    <style>
        @font-face {
        font-family: 'Arial';
        src: url(SourceSansPro-Regular.ttf);
        }

        .clearfix:after {
        content: "";
        display: table;
        clear: both;
        }

        a {
        color: #5A3733;
        text-decoration: none;
        }

        body {
        position: relative;
        width: 18cm;  
        height: 29.7cm; 
        margin: 0 auto; 
        color: #555555;
        background: #FFFFFF; 
        font-family: Arial, sans-serif; 
        font-size: 12px; 
        /* font-family: SourceSansPro; */
        }

        header {
        padding: 10px 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #AAAAAA;
        }

        #logo {
        float: left;
        margin-top: 8px;
        }

        #logo img {
        height: 40px;
        }

        #company {
        float: right;
        text-align: right;
        }


        #details {
        margin-bottom: 50px;
        }

        #client {
        padding-left: 6px;
        border-left: 6px solid #5A3733;
        float: left;
        }

        #client .to {
        color: #777777;
        }

        h2.name {
        font-size: 1.4em;
        font-weight: normal;
        margin: 0;
        }

        #invoice {
        float: right;
        text-align: right;
        }

        #invoice h1 {
        color: #5A3733;
        font-size: 1.5em;
        line-height: 1em;
        font-weight: bold;
		/* font-family: 'Arial'; */
        margin: 0  0 10px 0;
        }

        #invoice .date {
        font-size: 1.1em;
        color: #777777;
        }

       h2 { color: #5A3733; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f3eef6; }
        .total {
            font-weight: bold;
            background-color: #fdecf6;
            color: #5A3733;
        }
        td:nth-child(4),
        td:nth-child(5) {
            text-align: right;
        }

        #thanks{
        font-size: 2em;
        margin-bottom: 50px;
		position: absolute;
        bottom: 140;
        }

        #notices{
        padding-left: 6px;
        border-left: 6px solid #5A3733; 
		position: absolute;
        bottom: 120; 
        }

        #notices .notice {
        font-size: 1.2em;
        }

        footer {
        color: #777777;
        width: 100%;
        height: 30px;
        position: absolute;
        bottom: 50;
        border-top: 1px solid #AAAAAA;
        padding: 8px 0;
        text-align: center;
        }

		.text-pink {
			color: #5A3733;
		}

		.text-purple {
			color: #EB6D2F;
		}


    </style>
  </head>
  <body>
    {{-- <div class="clearfix">
        @if($payment->status == 'unpaid')
        <h4 style="background: #5A3733;color:#fff; padding:10px; float:right">UNPAID</h4>
        @endif
        @if($payment->status == 'paid')
          <h4 style="background: #EB6D2F;color:#fff; padding:10px; float:right; font-family:arial">PAID</h4>
        @endif
      </div> --}}
    <header class="clearfix">
      <div id="logo">
        <img src="{{  public_path('build/images/logo-dark.png') }}">
      </div>
      <div id="company">
        <h2 class="name">Holy Communion Elements</h2>
        <div>Nairobi, Kenya</div>
        <div>+254 741 226 412 </div>
        <div><a href="mailto:info@rebuetext.com">info@rebuetext.com</a></div>
      </div>
      </div>
    </header>
    <main>
      <div id="details" class="clearfix">
        <div id="client">
          <div class="to">ACCOUNT:</div>
          <h2 class="name">{{ $branch->branch_name }}</h2>
          <div class="address">{{ $branch->kra_pin_number }}</div>
          <div class="email"><a href="mailto:{{ $branch->branch_email }}">{{ $branch->branch_email }}</a></div>
        </div>
        <div id="invoice">
          <h1> Monthly Payment Statement </h1>
          <div class="date">Generated on: {{ now()->toFormattedDateString() }}</div>
          {{-- <div class="date">Due Date: {{ date('F jS, Y', strtotime($payment->created_at)) }}</div> --}}
        </div>
      </div>
      <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Reference</th>
                <th>Amount (KES)</th>
                <th>Units</th>
                <th>Type</th>
                <th>Method</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($payments as $index => $payment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $payment->created_at->format('d M Y') }}</td>
                    <td>{{ $payment->reference }}</td>
                    <td>{{ number_format($payment->amount, 2) }}</td>
                    <td>{{ number_format($payment->units, 2) }}</td>
                    <td>{{ $payment->type }}</td>
                    <td>{{ $payment->method }}</td>
                </tr>
            @endforeach
            <tr class="total">
                <td colspan="3">Total</td>
                <td colspan="2">KES {{ number_format($payments->sum('amount'), 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>
      <div id="thanks">Thank you!</div>
      <div id="notices">
        <div><strong>NOTES:</strong></div>
        <div class="notice">You can make payment through Paybill No: <strong>4029109</strong> 
			A/C No: <strong>{{ $branch->kra_pin_number }}</strong>. You can also use the
			Credit/Debit Card payment methods available through the portal</div>
      </div>
    </main>
    <footer>
      This statement was created on a computer and is valid without the signature and seal.
    </footer>
  </body>
</html>
<?php
$ticket = $delegate->transactions()->first()->ticket;
$qrCode = base64_encode(\QrCode::format('png')->size(150)
                               ->generate($transaction->uuid));
?>
		<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<style>
		body {
			width: 100%;
		}
		
		@page {
			margin: 0.5cm 0.5cm
		}
	</style>
</head>

<body>


<table width="100%">
	<tr>
		<td style="text-align: center">
			<img width="100" src="imgs/cu_logo.png">
		</td>
		<td style="text-align: center">
			<h1 style="font-size: 20px;margin-bottom: 1px">ADVANCES IN MEDICINE 2019</h1>
<strong><i>
	25-26 May 2019 <br />
	Hong Kong Convention & Exhibition Centre <br />
	</i></strong>

Department of Medicine & Therapeutics <br>
Faculty of Medicine <br>
The Chinese University of Hong Kong <br>
		</td>
		<td style="text-align: center">
			<img width="90" src="imgs/medical_logo.png">
		</td>
	</tr>
</table>
<hr>

<table width="100%">
	<tr>
		<td>
			<p>{{\Carbon\Carbon::now()->toDateString()}}</p>
			<p>
				{{$delegate->prefix}} {{$delegate->first_name}} {{$delegate->last_name}}
				, <br>
				{{$delegate->department}} <br>
				{{$delegate->institution}} <br>
				{{$delegate->address_1}} <br>
				{{$delegate->address_2}} <br>
				{{$delegate->address_3}} <br>
				{{$delegate->country}} <br>
			</p>
		</td>
		<td style="text-align: right">
			<img src="data:image/png;base64,{{$qrCode}}" />
		</td>
	</tr>
</table>

<p>Your registration no.: {{$delegate->getRegistrationId()}}</p>
<h4 style=" text-align: center">CONFIRMATION OF REGISTRATION</h4>
			<p>We are pleased to confirm your registration details as follows:-</p>
<table style="width: 100%">
	<tr>
		<th style="text-align: center; border-collapse: separate; border-spacing: 1px 1px; border-bottom: 1px solid black">Category</th>
		<th style="text-align: center; border-collapse: separate; border-spacing: 1px 1px; border-bottom: 1px solid black">Unit price (HK$)</th>
		<th style="text-align: center; border-collapse: separate; border-spacing: 1px 1px; border-bottom: 1px solid black">No. of person</th>
		<th style="text-align: center; border-collapse: separate; border-spacing: 1px 1px; border-bottom: 1px solid black">Amount paid (HK$)</th>
	</tr>
		
		<tr>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">
				{{strpos($ticket->note,'trainee') > -1? "Para-medics / Trainees":"Medical Practitioner"}}
				<br>
				
				@if(strpos(strtolower($ticket->name), 'full') > -1)
					Full Registration 25-26 May 2019
				@elseif(strpos($ticket->name, '25') > -1)
					Day Registration 25 May 2019
				@elseif(strpos($ticket->name, '26') > -1)
					Day Registration 26 May 2019
				@endif
			</td>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">
				@if(strpos(strtolower($ticket->name),'waived')))
				                                               Waived
				@elseif(strpos(strtolower($ticket->name),'sponsor'))
				                                               Sponsored
				@else
				                                               ${{number_format($ticket->price,1,".",",")}}
				@endif
			</td>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">1</td>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">
				@if(strpos(strtolower($ticket->name),'waived')))
				                                               Waived
				@elseif(strpos(strtolower($ticket->name),'sponsor'))
				                                               Sponsored
				@else
				                                               ${{number_format($ticket->price,1,".",",")}}
				@endif

			</td>
		</tr>
		<tr>
		<td colspan="3" style="text-align: right">
			<strong>Grand Total (HK$):</strong>
		</td>
		<td style="text-align: center; border-bottom-style: double; border-collapse: separate; border-spacing: 5px 5px">
			<strong>${{number_format($ticket->price,1,".",",")}}</strong>
		</td>
	</tr>
</table>

<p>Paid by : Credit Card</p>
<p><strong>*Lunch box will be served at the venue on a first-come-first-served basis.</strong></p>
<p>Remarks:</p>
<p>

1)  Please quote your registration number above in all communications. <br>
2)  This confirmation is an official receipt of your registration. <br>
3)  Should there be any amendments regarding your registration, please inform Ms. Celia Lin at at 3505-1299 as soon as possible. <br>
4)	Please present this letter at the Conference registration counter to obtain your registration kit. <br>
</p>

<p>Should you have any questions, please feel free to contact the Conference Secretariat by email at aim@cuhk.edu.hk.</p>

<img width="100" src="imgs/stamp.png" />

<p>
	Conference Secretariat, AIM 2019 <br>
	Department of Medicine & Therapeutics <br>
	The Chinese University of Hong Kong <br>
</p>

</body>
</html>
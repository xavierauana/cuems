<?php
$ticket = $transaction->ticket;
$qrCode = base64_encode(\QrCode::format('png')->size(150)
                               ->generate($transaction->uuid));

$isWaived = strpos(strtolower($ticket->note), 'waived') > -1;
$isSponsored = strpos(strtolower($ticket->note), 'sponsored') > -1;
$isWaivedOrSponsored = $isWaived or $isSponsored;
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
			<h1 style="font-size: 20px;margin-bottom: 1px">ADVANCES IN MEDICINE (AIM) 2019</h1>
<strong><i>
	25-26 May 2019 <br />
	Hong Kong Convention & Exhibition Centre <br />
	</i></strong>

Department of Medicine and Therapeutics <br>
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
			<p>{{\Carbon\Carbon::now()->format("j F Y")}}</p>
			<p>
				{{$delegate->prefix}}
				. {{$delegate->first_name}} {{$delegate->last_name}}<br>
				{{$delegate->department}} <br>
				@if($delegate->institution != "empty") {{$delegate->institution}}
				@else
					{{$delegate->email}}
				@endif
				<br>
				@if($delegate->address_1 != "empty") {{$delegate->address_1}}
				<br> @endif
				@if($delegate->address_1 != "empty" and $delegate->address_2 != "empty") {{$delegate->address_2}}
				<br> @endif
				@if($delegate->address_1 != "empty" and $delegate->address_3 != "empty") {{$delegate->address_3}}
				<br> @endif
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
			<p>We are pleased to confirm your registration for AIM Conference 2019 as follows:-</p>
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
				{{number_format($ticket->price,0,".",",")}}
			</td>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">1</td>
			<td style="padding-bottom: 15px; text-align: center; border-collapse: separate; border-spacing: 5px 5px; border-bottom: 1px solid black">
				@if($isWaived)
					Waived
				@elseif($isSponsored)
					Sponsored
				@else
					{{number_format($ticket->price,0,".",",")}}
				@endif

			</td>
		</tr>
		<tr>
		<td colspan="3" style="text-align: right">
			<strong>Grand Total (HK$):</strong>
		</td>
		<td style="text-align: center; border-bottom-style: double; border-collapse: separate; border-spacing: 5px 5px">
			<strong>
				@if($isWaived)
					Waived
				@elseif($isSponsored)
					Sponsored
				@else
					{{number_format($ticket->price,0,".",",")}}
				@endif
					</strong>
		</td>
	</tr>
</table>

<p>				@if($isWaived)
	
	@elseif($isSponsored)
	
	@else
		Paid by : {{$transaction->transactionType->label}}
	@endif</p>
<p><strong>*Lunch box will be served at the venue on a first-come-first-served basis.</strong></p>
<p>Remarks:</p>
<ol>
<li>Please quote your registration number above in all communications.</li>
<li>This confirmation is an official receipt of your registration. </li>
<li>Should there be any amendments of your registration details, please inform Ms. Celia Lin by email at <u>aim@cuhk.edu.hk</u> as soon as possible. </li>
<li>Please present this letter (hard copy or electronic copy is acceptable) at the Conference registration counter to obtain your registration kit. </li>
</ol>
<br>
<p>
<img width="150" src="imgs/stamp.png" /><br>
	Conference Secretariat, AIM 2019 <br>
	Department of Medicine and Therapeutics <br>
	The Chinese University of Hong Kong <br>
</p>

</body>
</html>

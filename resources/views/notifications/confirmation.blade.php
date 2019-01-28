<?php
$delegate = isset($delegate) ? $delegate : $transaction->payee
?>
		
		<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<style>
		body {
			width: 100%;
		}
	</style>
</head>

<body>

<p>Dear {{$delegate->prefix}} {{$delegate->last_name}},</p>
<p>Thank you for your participation in Advances in Medicine which was held on 25-26 May 2019.  Attached please find your confirmation which is served as an official receipt of your registration.  Please kindly be reminded to present this letter at the Conference registration counter to obtain your registration kit.</p>
<p>Should you have any enquiry, please do not hesitate to contact the Conference Secretariat by email at <a
			href="mailto:aim@cuhk.edu.hk"
			target="_top">aim@cuhk.edu.hk</a>.</p>

<p>Look forward to seeing you next year!</p>


<p>Best regards,</p>
<p>
<b style="color:#6b2d6c">Wingman Wong <br>
Department of Medicine & Therapeutics <br>
Faculty of Medicine <br>
The Chinese University of Hong Kong
</b></p>
<p>
	Tel: (852) 3505-3127  Fax: (852) 2645-1699 <br>
Email: <a href="mailto:wing-man@cuhk.edu.hk">wing-man@cuhk.edu.hk</a> <br>
Add: 9/F., Lui Che Woo Clinical Sciences Building, Prince of Wales Hospital, Shatin, N.T., Hong Kong

</p>

<img src="{{asset('imgs/logo.png')}}" style="max-width: 600px; height:auto">

</body>
</html>


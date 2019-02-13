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
<p>Thank you very much for registering for the Advances in Medicine conference 2019 which will be held on 25-26 May 2019 at the Hong Kong Convention and Exhibition Centre.  Please find enclosed the confirmation letter of your registration for your reference.  The letter serves as an official receipt and please bring along the letter (hard copy or electronic copy is acceptable) to the conference for registration purpose.</p>
<p>You are welcome to visit the website at <a
			href="http://www.mect.cuhk.edu.hk/AIM/" target="_top">http://www.mect.cuhk.edu.hk/AIM/</a> from time to time for the conference updates.  Should you have any enquiries regarding the registration, please feel free to contact Ms. Celia Lin at <a
			href="mailto:aim@cuhk.edu.hk" target="_top">aim@cuhk.edu.hk</a>.</p>

<p>We look forward to meeting you at the conference.</p>


<p>Best regards,</p>
<p><b style="color:#660066">Wingman Wong <br>
Department of Medicine & Therapeutics <br>
Faculty of Medicine <br>
The Chinese University of Hong Kong
</b></p>

<img src="{{asset('imgs/logo.png')}}" style="max-width: 600px; height:auto">

</body>
</html>


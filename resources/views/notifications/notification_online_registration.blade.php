<?php
$delegate = $delegate ?? $transaction->payee;
?>

<p>Dear {{$delegate->prefix}} {{$delegate->last_name}},</p>
<p>
	Thank you very much for registering for the Advances in Medicine conference which will be held on 25-26 May 2019 at the Hong Kong Convention and Exhibition Centre.  A confirmation letter with an assigned QR code will be sent to you by email in due course.
</p>

<p>If you do not hear from us after 7 days upon the receipt of this email, please contact Ms. Celia Lin by email at <a
			href="mailto:aim@cuhk.edu.hk">aim@cuhk.edu.hk</a> or by phone at (852) 3505-1299.</p>

<p>Best regards,</p>

<p><b><span color="#660066">
Wingman Wong <br>
Department of Medicine & Therapeutics <br>
Faculty of Medicine <br>
The Chinese University of Hong Kong
</span></b></p>

<img src="{{asset('imgs/logo.png')}}" style="max-width: 600px; height:auto">
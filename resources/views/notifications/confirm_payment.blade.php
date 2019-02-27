<?php 
        $delegate = isset($delegate) ? $delegate : $transaction->payee;
?>
<p>Dear {{$delegate->name}},</p>
<p>
	Payment is confirmed.
</p>

<p>We look forward to meeting you at the conference.</p>


<p>Best regards,</p>
<p>
Wingman Wong <br>
Department of Medicine & Therapeutics <br>
Faculty of Medicine <br>
The Chinese University of Hong Kong
</p>
<p>
	Tel: (852) 3505-3127  Fax: (852) 2645-1699 <br>
Email: <a href="mailto:wing-man@cuhk.edu.hk">wing-man@cuhk.edu.hk</a> <br>
Add: 9/F., Lui Che Woo Clinical Sciences Building, Prince of Wales Hospital, Shatin, N.T., Hong Kong

</p>

<img src="{{asset('imgs/logo.png')}}" style="max-width: 600px; height:auto">

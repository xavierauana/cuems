<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    
    <style>
        html, body {
	        font-family: Helvetica, Arial, sans-serif;
	        padding: 0;
	        width: 100%;
	        height: 100%;
	        margin: 0 15px;
        }

        .ticket {
	        margin-top: 10px;
	        border-collapse: collapse;
	        width: 100%;
	        height: 91%;
	        padding: 0 15px;
        }

        .left {
	        width: 150px;
        }

        .left img {
	        width: 150px;
        }

        .right {
	        vertical-align: middle;
	        padding-left: 15px;
        }

        .event-title {
	        font-size: 16px;
	        font-weight: bold;
	        text-align: center;
        }
    </style>
 
</head>
<body>
    <table class="ticket">
            <tr>
                <td class="left">
                    <img src="data:image/png;base64,{{$imageData}}">
                </td>
                <td class="right">
                    <p class="event-title"
                       style="font-size: 18px">Event Name: {{$event->title}}</p>
                    <p class="event-title"
                       style="font-weight: normal">Name: {{$delegateName}}</p>
                    <p class="event-title"
                       style="font-weight: normal">Ticket: {{$ticketName}}</p>
                    <p class="event-title"
                       style="font-weight: normal"> Date: {{$event->start_at->toDateString()}}</p>
                </td>
            </tr>
    </table>
</body>
</html>

<?php
/**
 * Author: Xavier Au
 * Date: 8/11/2018
 * Time: 11:58 AM
 */

namespace App\Mock;


use Carbon\Carbon;

class CUIPGStatus
{

    public function success(): string {
        $now = Carbon::now();
        $timeStamp = $now->format("Y-mm-d hh:mm:ss");
        $url = str_replace("https://", "", url("/"));

        return "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
            <Server>
            <Name>{$url}</Name>
            <Time>{$timeStamp}</Time>
            <Status>Available</Status>
            <Message />
            </Server>";
    }

    public function fail(): string {
        $now = Carbon::now();
        $timeStamp = $now->format("Y-mm-d hh:mm:ss");
        $url = str_replace("https://", "", url("/"));

        return "<?xml version=\"1.0\" encoding=\"ISO-8859-1\" ?>
                    <Server>
                    <Name>{{$url}</Name>
                    <Time>{$timeStamp}</Time>
                    <Status>Not Available</Status>
                    <Message>The CUHK Internet Payment Gateway service is suspend for maintenance:<br />
                    2009-01-29 00:30 to 01:30.
                    </Message>
                    </Server>";
    }
}
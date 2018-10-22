<?php
/**
 * Author: Xavier Au
 * Date: 20/10/2018
 * Time: 3:39 PM
 */

namespace App\Services;


use App\Transaction;
use Dompdf\Dompdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CreateTicketService
{
    private $pdf;

    /**
     * CreateTicketService constructor.
     * @param \Dompdf\Dompdf $pdf
     */
    public function __construct(Dompdf $pdf) {

        $this->pdf = $pdf;
    }


    /**
     * Create a ticket pdf with a Transaction
     * return file path to the pdf
     *
     * @param \App\Transaction $transaction
     * @param \App\Ticket      $ticket
     * @return string
     * @throws \Throwable
     */
    public function createPDF(Transaction $transaction): ?string {

        $data = $this->createQRCode($transaction->uuid);
        $imageData = base64_encode($data);
        $delegateName = optional($transaction->payee)->name;
        $ticketName = $transaction->ticket->name;
        $event = $transaction->ticket->event;

        $html = view("templates.ticket." . $transaction->ticket->template,
            compact('imageData', 'delegateName', 'ticketName',
                'event'))->render();

        $pdf = new \Dompdf\Dompdf();
        $pdf->setPaper($transaction->ticket->templateDimension, 'landscape');

        $pdf->loadHtml($html);
        $pdf->render($html);

        return $pdf->output();

    }

    private
    function createQRCode(
        string $message, string $format = "png",
        int $size = 400,
        string $tolerance = "Q"
    ) {

        return QrCode::format($format)
                     ->size($size)
                     ->errorCorrection($tolerance)
                     ->generate($message);

    }
}
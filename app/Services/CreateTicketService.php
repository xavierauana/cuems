<?php
/**
 * Author: Xavier Au
 * Date: 20/10/2018
 * Time: 3:39 PM
 */

namespace App\Services;


use App\Transaction;
use Dompdf\Dompdf;
use Dompdf\Options;

class CreateTicketService
{
    private $pdf;
    private $ticketView;
    private $pageSize;
    private $orientation;

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
        $delegate = $transaction->payee;
        $view = $this->ticketView ?? "templates.tickets." . $transaction->ticket->template;
        $html = view($view, compact('delegate',
            'transaction'))->render();

        $pageSize = $this->pageSize ?? $transaction->ticket->templateDimension;
        $orientation = $this->orientation ?? 'landscape';
        $options = new Options();
        $options->setIsRemoteEnabled(true);
        $options->set('isHtml5ParserEnabled', true);
        $pdf = new \Dompdf\Dompdf();
        $pdf->setOptions($options);

        $pdf->setPaper($pageSize, $orientation);

        $pdf->loadHtml($html);
        $pdf->render($html);

        return $pdf->output();

    }

    /**
     * @param mixed $ticketView
     * @return CreateTicketService
     */
    public function setTicketView($ticketView) {
        $this->ticketView = $ticketView;

        return $this;
    }

    /**
     * @param mixed $pageSize
     * @return CreateTicketService
     */
    public function setPageSize($pageSize) {
        $this->pageSize = $pageSize;

        return $this;
    }

    /**
     * @param mixed $orientation
     * @return CreateTicketService
     */
    public function setOrientation($orientation) {
        $this->orientation = $orientation;

        return $this;
    }
}
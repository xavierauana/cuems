<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-26
 * Time: 15:59
 */

namespace App\Presenters;


use App\Ticket;

class RegistrationDataPresenter
{
    private $fields = [
        'prefix'                        => "Prefix",
        'first_name'                    => "Given Name",
        'last_name'                     => "Surname",
        'email'                         => "Email",
        'mobile'                        => "Phone",
        'fax'                           => "Fax",
        'position'                      => "Position",
        'department'                    => "Department",
        'institution'                   => "Institution",
        'address_1'                     => "Address 1",
        'address_2'                     => "Address 2",
        'address_3'                     => "Address 3",
        'country'                       => "Country",
        'training_organisation'         => "Training Organisation",
        'training_organisation_address' => "Training Organisation Address",
        'supervisor'                    => "Supervisor",
        'training_position'             => "Training Position",
    ];

    public function __invoke($key, $value) {


        if ($this->inConvertedFields($key)) {
            $key = $this->fields[$key];
        }

        if ($this->isTicketId($key)) {
            list($key, $value) = $this->getTicketInformation($value);
        }

        echo sprintf($this->getPatten(), $key, $value);
    }

    private function getPatten(): string {
        return "<p><strong>%s: </strong> %s</p>";
    }

    /**
     * @param $key
     * @return bool
     */
    private function isTicketId($key): bool {
        return $key === 'ticket_id';
    }

    /**
     * @param $value
     * @return array
     */
    private function getTicketInformation($value): array {
        $ticket = Ticket::findOrFail($value);

        $key = "Ticket";
        $value = $ticket->name;

        return array($key, $value);
    }

    /**
     * @param $key
     * @return bool
     */
    private function inConvertedFields($key): bool {
        return in_array($key, array_keys($this->fields));
    }
}
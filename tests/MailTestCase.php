<?php
/**
 * Author: Xavier Au
 * Date: 2019-05-15
 * Time: 10:42
 */

namespace Tests;


interface MailTestCase
{
    public function removeAllEmails();

    public function getAllEmail();

    public function getLastEmail();

    public function getFirstEmail();

    public function assertEmailBodyContains($body, $email);

    public function assertEmailBodyNotContains($body, $email);

    public function assertEmailWasSentTo($recipient, $email);

    public function assertEmailWasNotSentTo($recipient, $email);

    public function assertEmailWasSentFrom($sender, $email);

    public function assertEmailWasNotSentFrom($sender, $email);

    public function assertEmailSubjectContains($subject, $email);

    public function assertEmailSubjectNotContains($subject, $email);

    public function assertEmailHasAttachment($attachmentFileName, $email);

    public function assertNoAttachment($email);

    public function assertHasCc($address);

    public function assertHasBcc($address);
}
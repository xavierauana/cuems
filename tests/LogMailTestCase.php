<?php
/**
 * Author: Xavier Au
 * Date: 2019-05-15
 * Time: 10:44
 */

namespace Tests;


class LogMailTestCase extends TestCase implements MailTestCase
{

    public function removeAllEmails() {
        unlink(storage_path('logs/laravel.log'));
    }

    public function getAllEmail() {
        $mails = [];
        $count = 0;
        $start = false;
        $contents = file(storage_path('logs/laravel.log'));
        foreach ($contents as $line) {
            if (strpos($line, "DEBUG: Message-ID:")) {
                if ($start) {
                    $count++;
                }
                $start = true;
            } else {
                if ($start) {
                    $mails[$count][] = $line;
                }
            }
        }

        return $mails;
    }

    public function getLastEmail() {
        // TODO: Implement getLastEmail() method.
    }

    public function getFirstEmail() {
        // TODO: Implement getFirstEmail() method.
    }

    public function assertEmailBodyContains($body, $email) {
        // TODO: Implement assertEmailBodyContains() method.
    }

    public function assertEmailBodyNotContains($body, $email) {
        // TODO: Implement assertEmailBodyNotContains() method.
    }

    public function assertEmailWasSentTo($recipient, $email) {
        // TODO: Implement assertEmailWasSentTo() method.
    }

    public function assertEmailWasNotSentTo($recipient, $email) {
        // TODO: Implement assertEmailWasNotSentTo() method.
    }

    public function assertEmailWasSentFrom($sender, $email) {
        // TODO: Implement assertEmailWasSentFrom() method.
    }

    public function assertEmailWasNotSentFrom($sender, $email) {
        // TODO: Implement assertEmailWasNotSentFrom() method.
    }

    public function assertEmailSubjectContains($subject, $email) {
        // TODO: Implement assertEmailSubjectContains() method.
    }

    public function assertEmailSubjectNotContains($subject, $email) {
        // TODO: Implement assertEmailSubjectNotContains() method.
    }

    public function assertEmailHasAttachment($attachmentFileName, $email) {
        // TODO: Implement assertEmailHasAttachment() method.
    }

    public function assertNoAttachment($email) {
        // TODO: Implement assertNoAttachment() method.
    }

    public function assertHasCc($address) {
        // TODO: Implement assertHasCc() method.
    }

    public function assertHasBcc($address) {
        // TODO: Implement assertHasBcc() method.
    }


}
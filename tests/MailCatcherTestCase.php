<?php
/**
 * Author: Xavier Au
 * Date: 15/10/2018
 * Time: 7:48 PM
 */

namespace Tests;

use GuzzleHttp\Client;

class MailCatcherTestCase extends TestCase
{
    protected $mailCatcher;

    /**
     * MailCatcherTestCase constructor.
     * @param $mailCatcher
     */
    public function __construct($name = null, array $data = [], $dataName = ''
    ) {

        parent::__construct($name, $data, $dataName);

        $this->mailCatcher = new Client(['base_uri' => "http://127.0.0.1:1080"]);
    }

    protected function removeAllEmails() {
        $this->mailCatcher->delete('/messages');
    }

    protected function getAllEmail() {
        $emails = $this->mailCatcher->get("/messages");;

        if (empty($emails)) {

            $this->fail("No Emails");
        }

        return json_decode((string)$emails->getBody(), true);
    }

    protected function getLastEmail() {
        $emails = $this->getAllEmail();
        $emails[count($emails) - 1];
        $emailId = $emails[count($emails) - 1]['id'];

        return $this->mailCatcher->get("/messages/{$emailId}.json");
    }

    protected function assertEmailBodyContains($body, $email) {
        $this->assertContains($body, (string)$email->getBody());
    }

    protected function assertEmailBodyNotContains($body, $email) {
        $this->assertNotContains($body, (string)$email->getBody());
    }

    protected function assertEmailWasSentTo($recipient, $email) {
        $recipients = json_decode(((string)$email->getBody()),
            true)['recipients'];
        $this->assertContains("<{$recipient}>", $recipients);
    }

    protected function assertEmailWasNotSentTo($recipient, $email) {

        $recipients = json_decode(((string)$email->getBody()),
            true)['recipients'];
        $this->assertNotContains("<{$recipient}>", $recipients);
    }

    protected function assertEmailWasSentFrom($sender, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['sender'];
        $this->assertContains("{$sender}", $data);
    }

    protected function assertEmailWasNotSentFrom($sender, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['sender'];
        $this->assertNotContains("{$sender}", $data);
    }

    protected function assertEmailSubjectContains($subject, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['subject'];
        $this->assertContains("{$subject}", $data);
    }

    protected function assertEmailSubjectNotContains($subject, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['subject'];
        $this->assertNotContains("{$subject}", $data);
    }

    protected function assertEmailHasAttachment($attachmentFileName, $email) {

        $attachments = $name = json_decode((string)$email->getBody(),
            true)['attachments'];


        if (count($attachments) === 0) {
            $this->assertTrue(false, "No attachment find in email");
        }
        $name = $attachments[0]['filename'];

        $this->assertEquals($attachmentFileName, $name);
    }

    protected function assertNoAttachment($email) {
        $attachments = $name = json_decode((string)$email->getBody(),
            true)['attachments'];


        if (count($attachments) === 0) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, 'There is attachment');
        }
    }

    protected function assertHasCc($email, $address) {
        $data = json_decode((string)$email->getBody(), true);
        $this->assertTrue(strpos($data['source'],
                "Cc: " . strtolower($address)) > -1, "Cannot find CC");
    }
    protected function assertHasBcc($email, $address) {
        $data = json_decode((string)$email->getBody(), true);
        $this->assertTrue(strpos($data['source'],
                "Bcc: " . strtolower($address)) > -1, "Cannot find BCC");
    }


}
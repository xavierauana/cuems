<?php
/**
 * Author: Xavier Au
 * Date: 15/10/2018
 * Time: 7:48 PM
 */

namespace Tests;

use GuzzleHttp\Client;

class MailCatcherTestCase extends TestCase implements MailTestCase
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

    public function removeAllEmails() {
        $this->mailCatcher->delete('/messages');
    }

    public function getAllEmail() {
        $emails = $this->mailCatcher->get("/messages");

        if (empty($emails)) {
            $this->fail("No Emails");
        }

        return json_decode((string)$emails->getBody(), true);
    }

    public function getLastEmail() {
        $emails = $this->getAllEmail();
        $emails[count($emails) - 1];
        $emailId = $emails[count($emails) - 1]['id'];

        return $this->mailCatcher->get("/messages/{$emailId}.json");
    }

    public function getFirstEmail() {
        $emails = $this->getAllEmail();
        $emailId = $emails[0]['id'];

        return $this->mailCatcher->get("/messages/{$emailId}.json");
    }

    public function assertEmailBodyContains($body, $email) {

        $body = json_decode((string)$email->getBody());

        if ($body->type === 'text/html') {
            $email = $this->mailCatcher->get("/messages/{$body->id}.html");
            $content = (string)$email->getBody();
            $this->assertContains($content, (string)$email->getBody());
        } else {
            $this->assertContains($body, (string)$email->getBody());
        }
    }

    public function assertEmailBodyNotContains($body, $email) {
        $this->assertNotContains($body, (string)$email->getBody());
    }

    public function assertEmailWasSentTo($recipient, $email) {
        $recipients = json_decode(((string)$email->getBody()),
            true)['recipients'];
        $this->assertContains("<{$recipient}>", $recipients);
    }

    public function assertEmailWasNotSentTo($recipient, $email) {

        $recipients = json_decode(((string)$email->getBody()),
            true)['recipients'];
        $this->assertNotContains("<{$recipient}>", $recipients);
    }

    public function assertEmailWasSentFrom($sender, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['sender'];
        $this->assertContains("{$sender}", $data);
    }

    public function assertEmailWasNotSentFrom($sender, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['sender'];
        $this->assertNotContains("{$sender}", $data);
    }

    public function assertEmailSubjectContains($subject, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['subject'];
        $this->assertContains("{$subject}", $data);
    }

    public function assertEmailSubjectNotContains($subject, $email) {
        $data = json_decode(((string)$email->getBody()),
            true)['subject'];
        $this->assertNotContains("{$subject}", $data);
    }

    public function assertEmailHasAttachment($attachmentFileName, $email) {

        $attachments = $name = json_decode((string)$email->getBody(),
            true)['attachments'];


        if (count($attachments) === 0) {
            $this->assertTrue(false, "No attachment find in email");
        }
        $name = $attachments[0]['filename'];

        $this->assertEquals($attachmentFileName, $name);
    }

    public function assertNoAttachment($email) {
        $attachments = $name = json_decode((string)$email->getBody(),
            true)['attachments'];


        if (count($attachments) === 0) {
            $this->assertTrue(true);
        } else {
            $this->assertTrue(false, 'There is attachment');
        }
    }

    public function assertHasCc($address) {
        $firstEmail = $this->getAllEmail()[0];
        $this->assertTrue(in_array("<{$address}>", $firstEmail['recipients']),
            "Cannot find CC address in recipient list");
    }

    public function assertHasBcc($address) {
        $number = count($this->getAllEmail());

        $this->assertTrue($number === 2,
            "There should have 2 emails for bcc testing, but {$number}");

        $lastEmail = $this->getAllEmail()[1];

        $this->assertTrue(in_array("<{$address}>", $lastEmail['recipients']),
            "Cannot find BCC address in recipient list");
    }


}
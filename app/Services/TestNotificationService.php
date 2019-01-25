<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-25
 * Time: 10:43
 */

namespace App\Services;


use App\Notification;

/**
 * Class TestNotificationService
 * @package App\Services
 */
class TestNotificationService
{
    /**
     * @var \App\Notification
     */
    private $notification;

    /**
     * @var DummyDataCreator
     */
    private $dummyData;
    /**
     * @var string
     */
    private $testEmail;


    /**
     * @param  $notification
     * @return TestNotificationService
     */
    public function setNotification(Notification $notification) {
        $this->notification = $notification;

        return $this;
    }


    /**
     * @throws \Exception
     */
    private function construct(): void {
        $this->validateDataIsReady();

        $this->dummyData = (new DummyDataCreator)->setNotification($this->notification)
                                                 ->setEmail($this->testEmail)
                                                 ->createDummyData();

    }


    /**
     * @throws \Exception
     */
    public function testDelegate() {
        $this->construct();

        $this->notification->send($this->dummyData->getDelegate());

        $this->dummyData->remove();
    }

    /**
     * @throws \Exception
     */
    public function testTransaction() {
        $this->construct();
        $this->notification->send($this->dummyData->getTransaction());
        $this->dummyData->remove();
    }

    /**
     * @param mixed $testEmail
     * @return TestNotificationService
     */
    public function setTestEmail(string $testEmail) {
        $this->testEmail = $testEmail;

        return $this;
    }

    /**
     * @throws \Exception
     */
    private function validateDataIsReady(): void {
        if (is_null($this->notification)) {
            throw  new \Exception("No notification to test.");
        }
        if (is_null($this->testEmail)) {
            throw  new \Exception("No test email is set.");
        }
    }


}
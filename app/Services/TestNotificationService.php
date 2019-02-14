<?php
/**
 * Author: Xavier Au
 * Date: 2019-01-25
 * Time: 10:43
 */

namespace App\Services;


use App\Notification;
use Illuminate\Support\Facades\Log;

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
     * @return bool
     */
    private function construct(): bool {
        if ($this->validateDataIsReady()) {
            $this->dummyData = (new DummyDataCreator)->setNotification($this->notification)
                                                     ->setEmail($this->testEmail)
                                                     ->createDummyData();

            return true;
        }

        return false;

    }


    public function testDelegate() {

        if (!$this->construct()) {
            return false;
        }

        try {
            $this->notification->send($this->dummyData->getDelegate());
            $this->dummyData->remove();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->dummyData->remove();
        }

    }


    /**
     * @throws \Exception
     */
    public function testTransaction() {
        if (!$this->construct()) {
            return false;
        }


        try {
            $this->notification->send($this->dummyData->getTransaction());
            $this->dummyData->remove();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            $this->dummyData->remove();
        }
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
     * @return bool
     */
    private function validateDataIsReady(): bool {
        if (is_null($this->notification)) {
            Log::error("Test notification failed. No notification to test.");

            return false;
        }
        if (is_null($this->testEmail)) {
            Log::error("Test notification failed. No test email set.");

            return false;
        }

        return true;
    }


}
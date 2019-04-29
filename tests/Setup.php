<?php
/**
 * Author: Xavier Au
 * Date: 2019-04-19
 * Time: 18:30
 */

namespace Tests;


class Setup
{

    private $notificationParam = null;
    private $notifications = [];

    public function generate() {
        //TODO: Implement method
    }

    /**
     * @param null $notificationParam
     * @return Setup
     */
    public function setNotificationParam(array $notificationParam) {
        $this->notificationParam = $notificationParam;

        return $this;
    }
}
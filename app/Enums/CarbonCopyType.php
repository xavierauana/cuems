<?php
/**
 * Author: Xavier Au
 * Date: 2019-03-06
 * Time: 13:48
 */

namespace App\Enums;


use Illuminate\Notifications\Action;
use MyCLabs\Enum\Enum;

/**
 * @method static Action CC()
 * @method static Action BCC()
 */
class CarbonCopyType extends Enum
{
    private const CC  = "cc";
    private const BCC = "bcc";
}
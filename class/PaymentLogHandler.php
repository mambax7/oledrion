<?php

namespace XoopsModules\Oledrion;

/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

/**
 * oledrion
 *
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

/**
 * Class PaymentLogHandler
 */
class PaymentLogHandler extends OledrionPersistableObjectHandler
{
    /**
     * @var Oledrion\Helper
     */
    public $helper;

    /**
     * @param \XoopsDatabase                     $db
     * @param null|\XoopsModules\Oledrion\Helper $helper
     */
    public function __construct(\XoopsDatabase $db = null, \XoopsModules\Oledrion\Helper $helper = null)
    {
        /** @var \XoopsModules\Oledrion\Helper $this ->helper */
        if (null === $helper) {
            $this->helper = \XoopsModules\Oledrion\Helper::getInstance();
        } else {
            $this->helper = $helper;
        }
        if (null === $db) {
            $db = \XoopsDatabaseFactory::getDatabaseConnection();
        }
        //                         Table                         Classe       Id
        parent::__construct($db, 'oledrion_payment_log', PaymentLog::class, 'log_id');
    }
}

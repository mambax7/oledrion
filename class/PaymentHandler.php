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
 * Class PaymentHandler
 */
class PaymentHandler extends OledrionPersistableObjectHandler
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
        //                                       Table                    Classe              Id
        parent::__construct($db, 'oledrion_payment', Payment::class, 'payment_id');
    }

    /**
     * @param Parameters $parameters
     * @return array
     */
    public function getAllPayment(Parameters $parameters)
    {
        $parameters = $parameters->extend(new Oledrion\Parameters([
                                                                      'start' => 0,
                                                                      'limit' => 0,
                                                                      'sort'  => 'payment_id',
                                                                      'order' => 'ASC',
                                                                  ]));
        $critere    = new \Criteria('payment_id', 0, '<>');
        $critere->setLimit($parameters['limit']);
        $critere->setStart($parameters['start']);
        $critere->setSort($parameters['sort']);
        $critere->setOrder($parameters['order']);
        $categories = [];
        $categories = $this->getObjects($critere);

        return $categories;
    }

    /**
     * @param $delivery_id
     * @return array
     */
    public function getThisDeliveryPayment($delivery_id)
    {
        $deliveryPaymentHandler = $this->helper->getHandler('DeliveryPayment');
        $ret                    = [];
        $parameters             = ['delivery' => $delivery_id];
        $deliveryPayment        = $deliveryPaymentHandler->getDeliveryPaymantId($parameters);
        foreach ($deliveryPayment as $payment) {
            $id[] = $payment['dp_payment'];
        }

        $critere = new \CriteriaCompo();
        $critere->add(new \Criteria('payment_id', '(' . implode(',', $id) . ')', 'IN'));
        $critere->add(new \Criteria('payment_online', 1));
        $obj = $this->getObjects($critere);
        if ($obj) {
            foreach ($obj as $root) {
                $tab   = [];
                $tab   = $root->toArray();
                $ret[] = $tab;
            }
        }

        return $ret;
    }
}

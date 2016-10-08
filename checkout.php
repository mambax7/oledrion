<?php
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
 * @copyright   {@link http://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

/**
 * Saisie des données du client + affichage des informations saisies pour validation avec redirection vers la passerelle de paiement
 */
require __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_command.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
require_once XOOPS_ROOT_PATH . '/class/xoopsformloader.php';
require_once XOOPS_ROOT_PATH . '/class/xoopslists.php';
require_once XOOPS_ROOT_PATH . '/class/tree.php';
require_once OLEDRION_PATH . 'class/registryfile.php';

// Get user id
$uid = Oledrion_utils::getCurrentUserID();
// Get checkout level
$checkout_level = Oledrion_utils::getModuleOption('checkout_level');
// Passage de commandes réservé aux utilisateurs enregistrés
if (Oledrion_utils::getModuleOption('restrict_orders', false) == 1 && $uid == 0) {
    $registry = new oledrion_registryfile();
    $text     = $registry->getfile(OLEDRION_TEXTFILE5);
    Oledrion_utils::redirect(xoops_trim($text), 'index.php', 5);
}
// Get option
$op = 'default';
if (isset($_POST['op'])) {
    $op = $_POST['op'];
} elseif (isset($_GET['op'])) {
    $op = $_GET['op'];
}
// Get action
$action = 'default';
if (isset($_POST['action'])) {
    $action = $_POST['action'];
} elseif (isset($_GET['action'])) {
    $action = $_GET['action'];
}
// Get commend id
$commend_id = 0;
if (isset($_POST['commend_id'])) {
    $commend_id = (int)$_POST['commend_id'];
} elseif (isset($_GET['commend_id'])) {
    $commend_id = (int)$_GET['commend_id'];
}

$xoopsTpl->assign('op', $op);
$xoopsTpl->assign('mod_pref', $mod_pref);
$cartForTemplate      = array();
$emptyCart            = false;
$shippingAmount       = $commandAmount = $vatAmount = $commandAmountTTC = $discountsCount = $commandAmountVAT = 0;
$goOn                 = '';
$discountsDescription = array();

function listCart()
{
    global $cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription;
    $reductions = new oledrion_reductions();
    $reductions->computeCart($cartForTemplate, $emptyCart, $shippingAmount, $commandAmount, $vatAmount, $goOn, $commandAmountTTC, $discountsDescription, $discountsCount);
}

$oledrion_Currency = Oledrion_Currency::getInstance();
$countries         = Oledrion_utils::getCountriesList();

switch ($op) {
    case 'save' :
        if (empty($_POST)) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        listCart();

        switch ($action) {
            case 'make' :
                $commandAmountTTC = $commandAmountTTC + $commandAmountVAT;
                $password         = md5(xoops_makepass());
                $passwordCancel   = md5(xoops_makepass());
                $commande         = $h_oledrion_commands->create(true);
                $commande->setVars($_POST);
                $commande->setVar('cmd_uid', $uid);
                $commande->setVar('cmd_date', date('Y-m-d'));
                $commande->setVar('cmd_create', time());
                $commande->setVar('cmd_state', OLEDRION_STATE_NOINFORMATION);
                $commande->setVar('cmd_ip', Oledrion_utils::IP());
                $commande->setVar('cmd_articles_count', count($cartForTemplate));
                $commande->setVar('cmd_total', Oledrion_utils::formatFloatForDB($commandAmountTTC));
                $commande->setVar('cmd_shipping', Oledrion_utils::formatFloatForDB($shippingAmount));
                $commande->setVar('cmd_password', $password);
                $commande->setVar('cmd_cancel', $passwordCancel);
                $commande->setVar('cmd_text', implode("\n", $discountsDescription));
                $commande->setVar('cmd_status', 2);
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                // Check checkout level
                if ($checkout_level == 1) {
                    Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commande->getVar('cmd_id'), 1);
                } elseif ($checkout_level == 2) {
                    Oledrion_utils::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commande->getVar('cmd_id'), 1);
                } else {
                    Oledrion_utils::redirect(_OLEDRION_SELECT_PACKING, OLEDRION_URL . 'checkout.php?op=packing&commend_id=' . $commande->getVar('cmd_id'), 1);
                }
                break;

            case 'find' :
                if ($commend_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                $commandAmountTTC = $commandAmountTTC + $commandAmountVAT;
                $commande         = $h_oledrion_commands->get($commend_id);
                $commande->setVars($_POST);
                $commande->setVar('cmd_state', OLEDRION_STATE_NOINFORMATION);
                $commande->setVar('cmd_ip', Oledrion_utils::IP());
                $commande->setVar('cmd_articles_count', count($cartForTemplate));
                $commande->setVar('cmd_total', Oledrion_utils::formatFloatForDB($commandAmountTTC));
                $commande->setVar('cmd_shipping', Oledrion_utils::formatFloatForDB($shippingAmount));
                $commande->setVar('cmd_text', implode("\n", $discountsDescription));
                $commande->setVar('cmd_status', 2);
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion_utils::redirect(_OLEDRION_SELECT_PACKING, OLEDRION_URL . 'checkout.php?op=packing&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'packing' :
                $packing_id = 0;
                if (isset($_POST['packing_id'])) {
                    $packing_id = (int)$_POST['packing_id'];
                }
                if ($packing_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if ($commend_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if ($checkout_level == 1) {
                    Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                } elseif ($checkout_level == 2) {
                    Oledrion_utils::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commend_id, 1);
                }

                $packing = $h_oledrion_packing->get($packing_id);
                if (!$packing->getVar('packing_id')) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                $commande = $h_oledrion_commands->get($commend_id);
                $commande->setVar('cmd_packing', $packing->getVar('packing_title'));
                $commande->setVar('cmd_packing_id', $packing->getVar('packing_id'));
                $commande->setVar('cmd_packing_price', $packing->getVar('packing_price'));
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion_utils::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'location' :
                $location_id = 0;
                if (isset($_POST['location_id'])) {
                    $location_id = (int)$_POST['location_id'];
                }
                if ($location_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if ($commend_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if ($checkout_level == 1) {
                    Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $location = $h_oledrion_location->get($location_id);
                $commande = $h_oledrion_commands->get($commend_id);
                $commande->setVar('cmd_location', $location->getVar('location_title'));
                $commande->setVar('cmd_location_id', $location->getVar('location_id'));
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }

                if ($h_oledrion_location->haveChild($location->getVar('location_id'))) {
                    Oledrion_utils::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&action=select&commend_id=' . $commande->getVar('cmd_id'), 1);
                } else {
                    Oledrion_utils::redirect(_OLEDRION_SELECT_DELIVERY, OLEDRION_URL . 'checkout.php?op=delivery&commend_id=' . $commande->getVar('cmd_id'), 1);
                }
                break;

            case 'delivery' :
                $delivery_id = 0;
                if (isset($_POST['delivery_id'])) {
                    $delivery_id = (int)$_POST['delivery_id'];
                }
                if ($delivery_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if ($commend_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if ($checkout_level == 1) {
                    Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $commande = $h_oledrion_commands->get($commend_id);
                $delivery = $h_oledrion_delivery->getThisLocationThisDelivery($delivery_id, $commande->getVar('cmd_location_id'));

                $shipping_price    = '';
                $shipping_price_op = Oledrion_utils::getModuleOption('checkout_shipping', false);
                switch ($shipping_price_op) {
                    case 1 :
                        $shipping_price = $shippingAmount + $delivery['delivery_price'];
                        break;

                    case 2 :
                        $shipping_price = $shippingAmount;
                        break;

                    case 3 :
                        $shipping_price = $delivery['delivery_price'];
                        break;

                    case 4 :
                        $shipping_price = 0;
                        break;
                }
                $commande->setVar('cmd_delivery', $delivery['delivery_title']);
                $commande->setVar('cmd_delivery_id', $delivery['delivery_id']);
                $commande->setVar('cmd_shipping', $shipping_price);
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion_utils::redirect(_OLEDRION_SELECT_PAYMENT, OLEDRION_URL . 'checkout.php?op=payment&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;

            case 'payment' :
                $payment_id = 0;
                if (isset($_POST['payment_id'])) {
                    $payment_id = (int)$_POST['payment_id'];
                }
                if ($payment_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                if ($commend_id == 0) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
                }
                // Check checkout level
                if ($checkout_level == 1) {
                    Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
                }
                $payment  = $h_oledrion_payment->get($payment_id);
                $commande = $h_oledrion_commands->get($commend_id);
                $commande->setVar('cmd_payment', $payment->getVar('payment_title'));
                $commande->setVar('cmd_payment_id', $payment->getVar('payment_id'));
                $res = $h_oledrion_commands->insert($commande, true);
                if (!$res) {
                    Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
                }
                Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commande->getVar('cmd_id'), 1);
                break;
        }

        break;

    // ****************************************************************************************************************
    case 'default' :
        // Présentation du formulaire
        // ****************************************************************************************************************
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        listCart();
        $notFound = true;

        if ($uid > 0) {
            // Si c'est un utlisateur enregistré, on recherche dans les anciennes commandes pour pré-remplir les champs
            $commande = null;
            $commande = $h_oledrion_commands->getLastUserOrder($uid);
            if (is_object($commande)) {
                $notFound = false;
            }
        }

        if ($notFound) {
            $commande = $h_oledrion_commands->create(true);
            $commande->setVar('cmd_country', OLEDRION_DEFAULT_COUNTRY);
        }

        // texte à afficher
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));

        $sform = new XoopsThemeForm(_OLEDRION_PLEASE_ENTER, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
        $sform->addElement(new XoopsFormHidden('op', 'save'));
        if ($commande->getVar('cmd_id') && $commande->getVar('cmd_id') > 0) {
            $sform->addElement(new XoopsFormHidden('action', 'find'));
            $sform->addElement(new XoopsFormHidden('commend_id', $commande->getVar('cmd_id')));
        } else {
            $sform->addElement(new XoopsFormHidden('action', 'make'));
        }
        $sform->addElement(new XoopsFormLabel(_OLEDRION_TOTAL, $oledrion_Currency->amountForDisplay($commandAmountTTC)));
        // By voltan
        if (in_array(Oledrion_utils::getModuleOption('checkout_shipping'), array(1, 2)) && $shippingAmount > 0) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_SHIPPING_PRICE, $oledrion_Currency->amountForDisplay($shippingAmount)));
        }
        $sform->addElement(new XoopsFormText(_OLEDRION_LASTNAME, 'cmd_lastname', 50, 255, $commande->getVar('cmd_lastname', 'e')), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_FIRSTNAME, 'cmd_firstname', 50, 255, $commande->getVar('cmd_firstname', 'e')), false);
        if ($uid > 0) {
            $sform->addElement(new XoopsFormText(_OLEDRION_EMAIL, 'cmd_email', 50, 255, $xoopsUser->getVar('email', 'e')), true);
        } else {
            $sform->addElement(new XoopsFormText(_OLEDRION_EMAIL, 'cmd_email', 50, 255, ''), true);
        }
        $sform->addElement(new XoopsFormText(_OLEDRION_CITY, 'cmd_town', 50, 255, $commande->getVar('cmd_town', 'e')), true);
        // By voltan
        if (Oledrion_utils::getModuleOption('checkout_country')) {
            $countriesList = new XoopsFormSelect(_OLEDRION_COUNTRY, 'cmd_country', $commande->getVar('cmd_country', ' e'));
            $countriesList->addOptionArray($countries);
            $sform->addElement($countriesList, true);
        } else {
            $sform->addElement(new XoopsFormHidden('cmd_country', OLEDRION_DEFAULT_COUNTRY));
        }
        $sform->addElement(new XoopsFormText(_OLEDRION_CP, 'cmd_zip', 15, 30, $commande->getVar('cmd_zip', 'e')), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_MOBILE, 'cmd_mobile', 15, 50, $commande->getVar('cmd_mobile', 'e')), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_PHONE, 'cmd_telephone', 15, 50, $commande->getVar('cmd_telephone', 'e')), true);
        if (Oledrion_utils::getModuleOption('ask_vatnumber')) {
            $sform->addElement(new XoopsFormText(_OLEDRION_VAT_NUMBER, 'cmd_vat_number', 50, 255, $commande->getVar('cmd_vat_number', 'e')), false);
        }
        if (Oledrion_utils::getModuleOption('ask_bill')) {
            $sform->addElement(new XoopsFormRadioYN(_OLEDRION_INVOICE, 'cmd_bill', 0), true);
        }
        $sform->addElement(new XoopsFormTextArea(_OLEDRION_STREET, 'cmd_adress', $commande->getVar('cmd_adress', 'e'), 3, 50), true);
        $sform->addElement(new XoopsFormText(_OLEDRION_GIFT, 'cmd_gift', 15, 30, $commande->getVar('cmd_gift', 'e')), false);
        $button_tray = new XoopsFormElementTray('', '');
        $submit_btn  = new XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit');
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $sform = Oledrion_utils::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());
        break;

    case 'packing' :
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if ($commend_id == 0) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if ($checkout_level == 1) {
            Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        } elseif ($checkout_level == 2) {
            Oledrion_utils::redirect(_OLEDRION_SELECT_LOCATION, OLEDRION_URL . 'checkout.php?op=location&commend_id=' . $commend_id, 1);
        }
        listCart();
        $packings = $h_oledrion_packing->getPacking();

        $sform = new XoopsThemeForm(_OLEDRION_PACKING_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
        $sform->addElement(new XoopsFormHidden('op', 'save'));
        $sform->addElement(new XoopsFormHidden('action', 'packing'));
        $sform->addElement(new XoopsFormHidden('commend_id', $commend_id));
        $packingSelect = new XoopsFormRadio(_OLEDRION_SELECT_PACKING, 'packing_id', '');
        foreach ($packings as $packing) {
            $packingSelect->addOption($packing['packing_id'], Oledrion_utils::packingHtmlSelect($packing));
        }
        $sform->addElement($packingSelect, true);
        $sform->addElement(new XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
        $sform =& Oledrion_utils::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'location' :
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if ($commend_id == 0) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if ($checkout_level == 1) {
            Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        }
        listCart();
        switch ($action) {
            case 'default' :
                $sform = new XoopsThemeForm(_OLEDRION_LOCATION_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
                $sform->addElement(new XoopsFormHidden('op', 'save'));
                $sform->addElement(new XoopsFormHidden('action', 'location'));
                $sform->addElement(new XoopsFormHidden('commend_id', $commend_id));
                $pids         = $h_oledrion_location->getAllPid(new Oledrion_parameters());
                $location_pid = new XoopsFormRadio(_OLEDRION_SELECT_LOCATION, 'location_id');
                foreach ($pids as $pid) {
                    $location_pid->addOption($pid->getVar('location_id'), $pid->getVar('location_title'));
                }
                $sform->addElement($location_pid, true);
                $sform->addElement(new XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
                $sform =& Oledrion_utils::formMarkRequiredFields($sform);
                $xoopsTpl->assign('form', $sform->render());
                break;

            case 'select' :
                $commande = $h_oledrion_commands->get($commend_id);
                $sform    = new XoopsThemeForm(_OLEDRION_LOCATION_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
                $sform->addElement(new XoopsFormHidden('op', 'save'));
                $sform->addElement(new XoopsFormHidden('action', 'location'));
                $sform->addElement(new XoopsFormHidden('commend_id', $commend_id));
                $locations       = $h_oledrion_location->getLocation($commande->getVar('cmd_location_id'));
                $location_select = new XoopsFormSelect(_OLEDRION_SELECT_LOCATION, 'location_id', '');
                foreach ($locations as $location) {
                    $location_select->addOption($location->getVar('location_id'), $location->getVar('location_title'));
                }
                $sform->addElement($location_select, true);
                $sform->addElement(new XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
                $sform =& Oledrion_utils::formMarkRequiredFields($sform);
                $xoopsTpl->assign('form', $sform->render());
                break;
        }

        // texte à afficher
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'delivery' :
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if ($commend_id == 0) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if ($checkout_level == 1) {
            Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        }
        listCart();
        $commande    = $h_oledrion_commands->get($commend_id);
        $location_id = $commande->getVar('cmd_location_id');
        $deliveres   = $h_oledrion_delivery->getThisLocationDelivery($location_id);

        $sform = new XoopsThemeForm(_OLEDRION_DELIVERY_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
        $sform->addElement(new XoopsFormHidden('op', 'save'));
        $sform->addElement(new XoopsFormHidden('action', 'delivery'));
        $sform->addElement(new XoopsFormHidden('commend_id', $commend_id));
        $delivery_options = new XoopsFormRadio(_OLEDRION_SELECT_DELIVERY, 'delivery_id');
        foreach ($deliveres as $delivery) {
            $delivery_options->addOption($delivery['delivery_id'], Oledrion_utils::deliveryHtmlSelect($delivery));
        }
        $sform->addElement($delivery_options, true);
        $sform->addElement(new XoopsFormButton('', 'post', _OLEDRION_SAVE_NEXT, 'submit'));
        $sform =& Oledrion_utils::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    case 'payment' :
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if ($commend_id == 0) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        // Check checkout level
        if ($checkout_level == 1) {
            Oledrion_utils::redirect(_OLEDRION_FINAL_CHECKOUT, OLEDRION_URL . 'checkout.php?op=confirm&commend_id=' . $commend_id, 1);
        }
        listCart();
        $commande    = $h_oledrion_commands->get($commend_id);
        $delivery_id = $commande->getVar('cmd_delivery_id');
        $payments    = $h_oledrion_payment->getThisDeliveryPayment($delivery_id);

        $sform = new XoopsThemeForm(_OLEDRION_PAYMENT_FORM, 'informationfrm', OLEDRION_URL . 'checkout.php', 'post');
        $sform->addElement(new XoopsFormHidden('op', 'save'));
        $sform->addElement(new XoopsFormHidden('action', 'payment'));
        $sform->addElement(new XoopsFormHidden('commend_id', $commend_id));
        $payment_options = new XoopsFormRadio(_OLEDRION_SELECT_PAYMENT, 'payment_id');
        foreach ($payments as $payment) {
            $payment_options->addOption($payment['payment_id'], Oledrion_utils::paymentHtmlSelect($payment));
        }
        $sform->addElement($payment_options, true);
        $sform->addElement(new XoopsFormButton('', 'post', _OLEDRION_SAVE_CONFIRM, 'submit'));
        $sform =& Oledrion_utils::formMarkRequiredFields($sform);
        $xoopsTpl->assign('form', $sform->render());

        // texte à afficher
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE6);
        $xoopsTpl->assign('text', xoops_trim($text));
        break;

    // ****************************************************************************************************************
    case 'confirm' :
        // Validation finale avant envoi sur la passerelle de paiement (ou arrêt)
        // ****************************************************************************************************************
        if ($h_oledrion_caddy->isCartEmpty()) {
            Oledrion_utils::redirect(_OLEDRION_CART_IS_EMPTY, OLEDRION_URL, 4);
        }
        if ($commend_id == 0) {
            Oledrion_utils::redirect(_OLEDRION_ERROR20, OLEDRION_URL, 4);
        }
        listCart();

        $commandAmountTTC = $commandAmountTTC + $commandAmountVAT;

        $commande = $h_oledrion_commands->get($commend_id);
        if ($commande->getVar('cmd_status') == 1) {
            Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password'), 6);
        }
        $commande->setVar('cmd_create', time());
        $commande->setVar('cmd_date', date('Y-m-d'));
        $commande->setVar('cmd_state', OLEDRION_STATE_NOINFORMATION);
        $commande->setVar('cmd_ip', Oledrion_utils::IP());
        $commande->setVar('cmd_status', 1);
        $res = $h_oledrion_commands->insert($commande, true);
        if (!$res) {
            Oledrion_utils::redirect(_OLEDRION_ERROR10, OLEDRION_URL, 6);
        }

        // Save command and empty cart
        $h_oledrion_caddy->emptyCart();

        // Enregistrement du panier
        $msgCommande = '';
        $handlers    = OledrionHandler::getInstance();
        foreach ($cartForTemplate as $line) {
            $panier = $h_oledrion_caddy->create(true);
            $panier->setVar('caddy_product_id', $line['product_id']);
            $panier->setVar('caddy_qte', $line['product_qty']);
            $panier->setVar('caddy_price', Oledrion_utils::formatFloatForDB($line['totalPrice']));
            // Attention, prix TTC avec frais de port
            $panier->setVar('caddy_cmd_id', $commande->getVar('cmd_id'));
            $panier->setVar('caddy_shipping', Oledrion_utils::formatFloatForDB($line['discountedShipping']));
            $panier->setVar('caddy_pass', md5(xoops_makepass()));
            // Pour le téléchargement
            $res = $h_oledrion_caddy->insert($panier, true);
            // Make msg
            $cat = $h_oledrion_cat->get($line['product_cid'])->toArray();
            $msgCommande .= str_pad($line['product_id'], 5, ' ') . ' ';
            $msgCommande .= str_pad($cat['cat_title'], 10, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($line['product_title'], 19, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($line['product_qty'], 8, ' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= str_pad($oledrion_Currency->amountForDisplay($line['product_price']), 15, ' ', STR_PAD_LEFT) . ' ';
            //$msgCommande .= str_pad($line['totalPriceFormated'],10,' ', STR_PAD_LEFT) . ' ';
            $msgCommande .= "\n";
            // Attributs
            if ($res && is_array($line['attributes']) && count($line['attributes']) > 0) {
                // Enregistrement des attributs pour ce produit
                foreach ($line['attributes'] as $attributeId => $attributeInformation) {
                    $caddyAttribute = $handlers->h_oledrion_caddy_attributes->create(true);
                    $caddyAttribute->setVar('ca_cmd_id', $commande->getVar('cmd_id'));
                    $caddyAttribute->setVar('ca_caddy_id', $panier->getVar('caddy_id'));
                    $caddyAttribute->setVar('ca_attribute_id', $attributeId);
                    $selectedOptions = $attributeInformation['attribute_options'];
                    $msgCommande .= '- ' . $attributeInformation['attribute_title'] . "\n";
                    foreach ($selectedOptions as $selectedOption) {
                        $caddyAttribute->addOption($selectedOption['option_name'], $selectedOption['option_value'], $selectedOption['option_price']);
                        $msgCommande .= '    ' . $selectedOption['option_name'] . ' : ' . $selectedOption['option_ttc_formated'] . "\n";
                    }
                    $handlers->h_oledrion_caddy_attributes->insert($caddyAttribute, true);
                }
            }
        }

        // Totaux généraux
        //$msgCommande .= "\n\n"._OLEDRION_SHIPPING_PRICE.' '.$oledrion_Currency->amountForDisplay($shippingAmount)."\n";
        $msgCommande .= "\n\n" . _OLEDRION_TOTAL . ' ' . $oledrion_Currency->amountForDisplay($commandAmountTTC) . "\n";
        if (count($discountsDescription) > 0) {
            $msgCommande .= "\n\n" . _OLEDRION_CART4 . "\n";
            $msgCommande .= implode("\n", $discountsDescription);
            $msgCommande .= "\n";
        }

        $msg                 = array();
        $msg['COMMANDE']     = $msgCommande;
        $msg['NUM_COMMANDE'] = $commande->getVar('cmd_id');
        $msg['NOM']          = $commande->getVar('cmd_lastname');
        $msg['PRENOM']       = $commande->getVar('cmd_firstname');
        $msg['ADRESSE']      = $commande->getVar('cmd_adress', 'n');
        $msg['CP']           = $commande->getVar('cmd_zip');
        $msg['VILLE']        = $commande->getVar('cmd_town');
        $msg['PAYS']         = $countries[$commande->getVar('cmd_country')];
        $msg['TELEPHONE']    = $commande->getVar('cmd_telephone');
        $msg['EMAIL']        = $commande->getVar('cmd_email');
        $msg['URL_BILL']     = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
        $msg['IP']           = Oledrion_utils::IP();
        if ($commande->getVar('cmd_bill') == 1) {
            $msg['FACTURE'] = _YES;
        } else {
            $msg['FACTURE'] = _NO;
        }
        // Send mail to client
        Oledrion_utils::sendEmailFromTpl('command_client.tpl', $commande->getVar('cmd_email'), sprintf(_OLEDRION_THANKYOU_CMD, $xoopsConfig['sitename']), $msg);
        // Send mail to admin
        Oledrion_utils::sendEmailFromTpl('command_shop.tpl', Oledrion_utils::getEmailsFromGroup(Oledrion_utils::getModuleOption('grp_sold')), _OLEDRION_NEW_COMMAND, $msg);

        // Présentation du formulaire pour envoi à la passerelle de paiement
        // Présentation finale avec panier en variables cachées ******************************
        $registry = new oledrion_registryfile();
        $text     = $registry->getfile(OLEDRION_TEXTFILE7);
        $xoopsTpl->assign('text', xoops_trim($text));

        if ($checkout_level == 1) {
            $text = $registry->getfile(OLEDRION_TEXTFILE4);
            $xoopsTpl->append('text', '<br>' . xoops_trim($text));
            $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
            $sform  = new XoopsThemeForm(_OLEDRION_FINISH, 'payform', $payURL, 'post');
        } else {
            if (!isset($payment) || $payment['payment_type'] === 'offline' || $commandAmountTTC == 0) {
                $text = $registry->getfile(OLEDRION_TEXTFILE4);
                $xoopsTpl->append('text', '<br>' . xoops_trim($text));
                $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
                $sform  = new XoopsThemeForm(_OLEDRION_FINISH, 'payform', $payURL, 'post');
            } else {
                // Set gateway
                $gateway = Oledrion_gateways::getGatewayObject($payment['payment_gateway']);
                if (!is_object($gateway)) {
                    die(_OLEDRION_ERROR20);
                }
                if (is_object($gateway)) {
                    $payURL = $gateway->getRedirectURL($commande->getVar('cmd_total'), $commande->getVar('cmd_id'));
                } else {
                    $payURL = OLEDRION_URL . 'invoice.php?id=' . $commande->getVar('cmd_id') . '&pass=' . $commande->getVar('cmd_password');
                }
                $sform    = new XoopsThemeForm(_OLEDRION_PAY_GATEWAY, 'payform', $payURL, 'post');
                $elements = array();
                if (is_object($gateway)) {
                    $elements = $gateway->getCheckoutFormContent($commande);
                }
                foreach ($elements as $key => $value) {
                    $sform->addElement(new XoopsFormHidden($key, $value));
                }
            }
        }

        $sform->addElement(new XoopsFormLabel(_OLEDRION_AMOUNT_PRICE, $oledrion_Currency->amountForDisplay($commandAmountTTC)));
        if ($commande->getVar('cmd_shipping') > 0) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_SHIPPING_PRICE, $oledrion_Currency->amountForDisplay($commande->getVar('cmd_shipping'))));
        }
        if ($commande->getVar('cmd_packing_price') > 0) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_PACKING_PRICE, $oledrion_Currency->amountForDisplay($commande->getVar('cmd_packing_price'))));
        }
        $sform->addElement(new XoopsFormLabel(_OLEDRION_TOTAL, $oledrion_Currency->amountForDisplay($commandAmountTTC + $commande->getVar('cmd_shipping') + $commande->getVar('cmd_packing_price'))));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_LASTNAME, $commande->getVar('cmd_lastname')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_FIRSTNAME, $commande->getVar('cmd_firstname')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_STREET, $commande->getVar('cmd_adress')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_CP, $commande->getVar('cmd_zip')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_CITY, $commande->getVar('cmd_town')));
        if (Oledrion_utils::getModuleOption('checkout_country')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_COUNTRY, $countries[$commande->getVar('cmd_country')]));
        }
        $sform->addElement(new XoopsFormLabel(_OLEDRION_PHONE, $commande->getVar('cmd_telephone')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_MOBILE, $commande->getVar('cmd_mobile')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_EMAIL, $commande->getVar('cmd_email')));
        $sform->addElement(new XoopsFormLabel(_OLEDRION_GIFT, $commande->getVar('cmd_gift')));
        if ($commande->getVar('cmd_packing')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_PACKING, $commande->getVar('cmd_packing')));
        }
        if ($commande->getVar('cmd_location')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_LOCATION, $commande->getVar('cmd_location')));
        }
        if ($commande->getVar('cmd_delivery')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_DELIVERY, $commande->getVar('cmd_delivery')));
        }
        if ($commande->getVar('cmd_payment')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_PAYMENT, $commande->getVar('cmd_payment')));
        }
        if (Oledrion_utils::getModuleOption('ask_vatnumber')) {
            $sform->addElement(new XoopsFormLabel(_OLEDRION_VAT_NUMBER, $commande->getVar('cmd_vat_number')));
        }
        if (Oledrion_utils::getModuleOption('ask_bill')) {
            if ($commande->getVar('cmd_bill') == 0) {
                $sform->addElement(new XoopsFormLabel(_OLEDRION_INVOICE, _NO));
            } else {
                $sform->addElement(new XoopsFormLabel(_OLEDRION_INVOICE, _YES));
            }
        }
        $button_tray = new XoopsFormElementTray('', '');
        if (!isset($payment) || $payment['payment_type'] === 'offline' || $commandAmountTTC == 0
            || $checkout_level == 1
        ) {
            $submit_btn = new XoopsFormButton('', 'post', _OLEDRION_FINISH, 'submit');
        } else {
            $submit_btn = new XoopsFormButton('', 'post', _OLEDRION_PAY_GATEWAY, 'submit');
        }
        $button_tray->addElement($submit_btn);
        $sform->addElement($button_tray);
        $xoopsTpl->assign('form', $sform->render());

        // Send sms
        if (Oledrion_utils::getModuleOption('sms_checkout')) {
            $information['to']   = ltrim($commande->getVar('cmd_mobile'), 0);
            $information['text'] = Oledrion_utils::getModuleOption('sms_checkout_text');
            $sms                 = Oledrion_sms::sendSms($information);
        }
        break;
}

$xoopsTpl->assign('breadcrumb', Oledrion_utils::breadcrumb(array(OLEDRION_URL . basename(__FILE__) => _OLEDRION_VALIDATE_CMD)));

// Image icons
if (file_exists(OLEDRION_PATH . 'language/' . $xoopsConfig['language'] . '/image/step1.png')) {
    $step1 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step1.png';
    $step2 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step2.png';
    $step3 = OLEDRION_URL . 'language/' . $xoopsConfig['language'] . '/image/step3.png';
} else {
    // Fallback
    $step1 = OLEDRION_URL . 'language/english/image/step1.png';
    $step2 = OLEDRION_URL . 'language/english/image/step2.png';
    $step3 = OLEDRION_URL . 'language/english/image/step3.png';
}
$xoopsTpl->assign('step1', $step1);
$xoopsTpl->assign('step2', $step2);
$xoopsTpl->assign('step3', $step3);

$title = _OLEDRION_VALIDATE_CMD . ' - ' . Oledrion_utils::getModuleName();
Oledrion_utils::setMetas($title, $title);
Oledrion_utils::setCSS();
Oledrion_utils::setLocalCSS($xoopsConfig['language']);
require_once XOOPS_ROOT_PATH . '/footer.php';
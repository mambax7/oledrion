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
 * @copyright   {@link https://xoops.org/ XOOPS Project}
 * @license     {@link http://www.fsf.org/copyleft/gpl.html GNU public license}
 * @author      Hossein Azizabadi (azizabadi@faragostaresh.com)
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_user.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';
// Check is user
$uid = Oledrion\Utility::getCurrentUserID();
if (0 == $uid) {
    Oledrion\Utility::redirect(_OLEDRION_ERROR23, XOOPS_URL . '/register.php', 4);
}
// Load header
//$handlers = HandlerManager::getInstance();
// Get list of this user order
$orders   = $list = [];
$criteria = new \CriteriaCompo();
$criteria->add(new \Criteria('cmd_uid', $uid));
$criteria->setSort('cmd_id');
$criteria->setOrder('DESC');
$commandsHandler = $helper->getHandler('Commands');
$orders          = $commandsHandler->getObjects($criteria, false);
if (!empty($orders)) {
    foreach ($orders as $item) {
        $command = $item->toArray();
        /* $caddy = $caddyHandler->getCaddyFromCommand($command['cmd_id']);
        foreach ($caddy as $item) {
            $tmp[] = $item->getVar('caddy_product_id');
        }
        $tmp = array_unique($tmp);
        foreach ($caddy as $itemCaddy) {
            $products = $productsHandler->getProductsFromIDs($tmp, true);
            $product = $products[$itemCaddy->getVar('caddy_product_id')];
            $productForTemplate[] = $product->toArray(); // Produit
        }
        $command['all_products'] = $productForTemplate; */
        $command['cmd_url'] = OLEDRION_URL . 'invoice.php?id=' . $command['cmd_id'] . '&pass=' . $command['cmd_password'];
        switch ($command['cmd_state']) {
            case 0:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE0;

                break;
            case 1:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE1;

                break;
            case 2:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE2;

                break;
            case 3:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE3;

                break;
            case 4:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE4;

                break;
            case 5:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE5;

                break;
            case 6:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE6;

                break;
            case 7:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE7;

                break;
            case 8:

                $command['cmd_state_title'] = _OLEDRION_USER_STATE8;

                break;
        }
        $list[] = $command;
    }
}

$xoopsTpl->assign('list', $list);
Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);
$title = _OLEDRION_USER . ' - ' . Oledrion\Utility::getModuleName();
Oledrion\Utility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';

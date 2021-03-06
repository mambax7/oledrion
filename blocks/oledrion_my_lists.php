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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

use XoopsModules\Oledrion;
use XoopsModules\Oledrion\Constants;

/**
 * Affiche les listes de l'utilisateur
 *
 * @param array $options [0] = Nombre maximum de listes à voir
 * @return array
 */
function b_oledrion_my_lists_show($options)
{
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $helper->loadLanguage('modinfo');
    $start = 0;
    $limit = (int)$options[0];
    $uid   = Oledrion\Utility::getCurrentUserID();
    if (0 == $uid) {
        return null;
    }
    $listType = Constants::OLEDRION_LISTS_ALL;
    $block    = [];
    //    $handlers = HandlerManager::getInstance();
    $listsHandler = $helper->getHandler('List');
    $items        = [];
    $items        = $listsHandler->getRecentLists(new Oledrion\Parameters([
                                                                              'start'    => $start,
                                                                              'limit'    => $limit,
                                                                              'sort'     => 'list_date',
                                                                              'order'    => 'DESC',
                                                                              'idAsKey'  => true,
                                                                              'listType' => $listType,
                                                                              'list_uid' => $uid,
                                                                          ]));
    if (count($items) > 0) {
        foreach ($items as $item) {
            $block['my_lists'][] = $item->toArray();
        }
    }

    return $block;
}

/**
 * Edition des paramètres du bloc
 *
 * @param array $options [0] = Nombre maximum de listes à voir
 * @return string
 */
function b_oledrion_my_lists_edit($options)
{
    require_once XOOPS_ROOT_PATH . '/modules/oledrion/include/common.php';
    $form = '';
    $form .= "<table border='0'>";
    $form .= '<tr><td>' . _MB_OLEDRION_LISTS_COUNT . "</td><td><input type='text' name='options[]' id='options' value='" . (int)$options[0] . "'></td></tr>";
    $form .= '</table>';

    return $form;
}

/**
 * Bloc à la volée
 * @param $options
 */
function b_oledrion_my_lists_duplicatable($options)
{
    $options = explode('|', $options);
    $block   = b_oledrion_my_lists_show($options);

    $tpl = new \XoopsTpl();
    $tpl->assign('block', $block);
    $tpl->display('db:oledrion_block_my_lists.tpl');
}

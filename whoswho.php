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

/**
 * Liste des fabricants
 */

use Xoopsmodules\oledrion;

require_once __DIR__ . '/header.php';
$GLOBALS['current_category']             = -1;
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_whoswho.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$tblAll = $tblAnnuaire = [];
$xoopsTpl->assign('alphabet', $manufacturerHandler->getAlphabet());
$xoopsTpl->assign('mod_pref', $mod_pref); // Préférences du module

$manufacturers = $manufacturerHandler->getItems(0, 0, 'manu_name, manu_commercialname');
foreach ($manufacturers as $item) {
    $forTemplate              = [];
    $forTemplate              = $item->toArray();
    $initiale                 = $item->getInitial();
    $tblAnnuaire[$initiale][] = $forTemplate;
}
$xoopsTpl->assign('manufacturers', $tblAnnuaire);

oledrion\Utility::setCSS();
oledrion\Utility::setLocalCSS($xoopsConfig['language']);
$helper->loadLanguage('modinfo');

$xoopsTpl->assign('global_advert', oledrion\Utility::getModuleOption('advertisement'));
$xoopsTpl->assign('breadcrumb', oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _MI_OLEDRION_SMNAME5]));

$title = _MI_OLEDRION_SMNAME5 . ' - ' . oledrion\Utility::getModuleName();
oledrion\Utility::setMetas($title, $title);
require_once XOOPS_ROOT_PATH . '/footer.php';

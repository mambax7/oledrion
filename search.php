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
 * Recherche dans les produits
 */

use XoopsModules\Oledrion;

require_once __DIR__ . '/header.php';
// require_once OLEDRION_PATH . 'class/tree.php';
$GLOBALS['current_category']             = -1; // Pour le bloc des catégories
$GLOBALS['xoopsOption']['template_main'] = 'oledrion_search.tpl';
require_once XOOPS_ROOT_PATH . '/header.php';

$limit      = Oledrion\Utility::getModuleOption('newproducts'); // Nombre maximum d'éléments à afficher
$categories = $manufacturers = $vendors = [];
$baseurl    = OLEDRION_URL . basename(__FILE__); // URL de ce script (sans son nom)

$xoopsTpl->assign('mod_pref', $mod_pref); // Module Preferences
$xoopsTpl->assign('columnsCount', Oledrion\Utility::getModuleOption('category_colums'));

$categories    = $categoryHandler->getAllCategories(new Oledrion\Parameters());
$vendors       = $vendorsHandler->getAllVendors(new Oledrion\Parameters());
$manufacturers = $manufacturerHandler->getItems(0, 0, 'manu_name', 'ASC', false);

if ((isset($_POST['op']) && 'go' === $_POST['op']) || isset($_GET['start'])) {
    // Recherche des résultats
    $xoopsTpl->assign('search_results', true);
    $xoopsTpl->assign('global_advert', Oledrion\Utility::getModuleOption('advertisement'));
    $xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_SEARCHRESULTS]));
    Oledrion\Utility::setMetas(Oledrion\Utility::getModuleName() . ' - ' . _OLEDRION_SEARCHRESULTS, Oledrion\Utility::getModuleName() . ' - ' . _OLEDRION_SEARCHRESULTS);

    if (!isset($_GET['start'])) {
        $sql = 'SELECT b.product_id, b.product_title, b.product_submitted, b.product_submitter, b.product_thumb_url, b.product_price, b.product_property1, b.product_property2, b.product_property3, b.product_property4, b.product_property5, b.product_property6, b.product_property7, b.product_property8, b.product_property9, b.product_property10, b.product_stock, b.product_summary FROM '
               . $xoopsDB->prefix('oledrion_products')
               . ' b, '
               . $xoopsDB->prefix('oledrion_productsmanu')
               . ' a WHERE (b.product_id = a.pm_product_id AND b.product_online = 1) ';
        if (0 == Oledrion\Utility::getModuleOption('show_unpublished')) {
            // Ne pas afficher les produits qui ne sont pas publiés
            $sql .= ' AND b.product_submitted <= ' . time();
        }
        if (0 == Oledrion\Utility::getModuleOption('nostock_display')) {
            // Se limiter aux seuls produits encore en stock
            $sql .= ' AND b.product_stock > 0';
        }
        $sql .= ') ';

        // Recherche sur une catégorie
        if (\Xmf\Request::hasVar('product_category', 'POST')) {
            $cat_cid = \Xmf\Request::getInt('product_category', 0, 'POST');
            if ($cat_cid > 0) {
                $sql .= 'AND (b.product_cid = ' . $cat_cid . ')';
            }
        }

        // Recherche sur les fabricants
        if (\Xmf\Request::hasVar('product_manufacturers', 'POST')) {
            $submittedManufacturers = null;
            $submittedManufacturers = $_POST['product_manufacturers'];
            if (is_array($submittedManufacturers) && 0 == (int)$submittedManufacturers[0]) {
                $submittedManufacturers = array_shift($submittedManufacturers);
            }
            if ($submittedManufacturers && is_array($submittedManufacturers)) {
                array_walk($submittedManufacturers, 'intval');
                $sql .= ' AND (a.pm_manu_id IN ( ' . implode(',', $submittedManufacturers) . '))';
            } else {
                $submittedManufacturer = (int)$submittedManufacturers;
                if ($submittedManufacturer > 0) {
                    $sql .= ' AND (a.pm_manu_id = ' . $submittedManufacturer . ')';
                }
            }
        }

        // Recherche sur les vendeurs
        if (\Xmf\Request::hasVar('product_vendors', 'POST')) {
            $vendor = \Xmf\Request::getInt('product_vendors', 0, 'POST');
            if ($vendor > 0) {
                $sql .= ' AND (product_vendor_id = ' . $vendor . ')';
            }
        }

        // set from
        if (\Xmf\Request::hasVar('product_from', 'POST')) {
            $product_from = \Xmf\Request::getInt('product_from', 0, 'POST');
            if ($product_from > 0) {
                $sql .= ' AND (product_price > ' . $product_from . ')';
            }
        }

        // set to
        if (\Xmf\Request::hasVar('product_to', 'POST')) {
            $product_to = \Xmf\Request::getInt('product_to', 0, 'POST');
            if ($product_to > 0) {
                $sql .= ' AND (product_price < ' . $product_to . ')';
            }
        }

        if (2 == \Xmf\Request::getInt('product_stock', 0, 'POST')) {
            $sql .= ' AND (product_stock > 0)';
        } elseif (0 == \Xmf\Request::getInt('product_stock', 0, 'POST')) {
            $sql .= ' AND (product_stock = 0)';
        }

        if (\Xmf\Request::hasVar('product_property1', 'POST')) {
            if ($_POST['product_property1']) {
                $sql .= ' AND (b.product_property1 = "' . \Xmf\Request::getString('product_property1', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property2', 'POST')) {
            if ($_POST['product_property2']) {
                $sql .= ' AND (b.product_property2 = "' . \Xmf\Request::getString('product_property2', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property3', 'POST')) {
            if ($_POST['product_property3']) {
                $sql .= ' AND (b.product_property3 = "' . \Xmf\Request::getString('product_property3', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property4', 'POST')) {
            if ($_POST['product_property4']) {
                $sql .= ' AND (b.product_property4 = "' . \Xmf\Request::getString('product_property4', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property5', 'POST')) {
            if ($_POST['product_property5']) {
                $sql .= ' AND (b.product_property5 = "' . \Xmf\Request::getString('product_property5', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property6', 'POST')) {
            if ($_POST['product_property6']) {
                $sql .= ' AND (b.product_property6 = "' . \Xmf\Request::getString('product_property6', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property7', 'POST')) {
            if ($_POST['product_property7']) {
                $sql .= ' AND (b.product_property7 = "' . \Xmf\Request::getString('product_property7', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property8', 'POST')) {
            if ($_POST['product_property8']) {
                $sql .= ' AND (b.product_property8 = "' . \Xmf\Request::getString('product_property8', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property9', 'POST')) {
            if ($_POST['product_property9']) {
                $sql .= ' AND (b.product_property9 = "' . \Xmf\Request::getString('product_property9', '', 'POST'). '")';
            }
        }

        if (\Xmf\Request::hasVar('product_property10', 'POST')) {
            if ($_POST['product_property10']) {
                $sql .= ' AND (b.product_property10 = "' . \Xmf\Request::getString('product_property10', '', 'POST'). '")';
            }
        }

        // Recherche sur du texte
        if (\Xmf\Request::hasVar('product_text', 'POST') && '' !== xoops_trim($_POST['product_text'])) {
            $temp_queries = $queries = [];
            $temp_queries = preg_split('/[\s,]+/', $_POST['product_text']);

            foreach ($temp_queries as $q) {
                $q         = trim($q);
                $queries[] = $myts->addSlashes($q);
            }
            if (count($queries) > 0) {
                $tmpObject = new Oledrion\Products();
                $datas     = $tmpObject->getVars();
                $fields    = [];
                $cnt       = 0;
                foreach ($datas as $key => $value) {
                    if (XOBJ_DTYPE_TXTBOX == $value['data_type'] || XOBJ_DTYPE_TXTAREA == $value['data_type']) {
                        if (0 == $cnt) {
                            $fields[] = 'b.' . $key;
                        } else {
                            $fields[] = ' OR b.' . $key;
                        }
                        ++$cnt;
                    }
                }
                $count      = count($queries);
                $cnt        = 0;
                $sql        .= ' AND ';
                $searchType = \Xmf\Request::getInt('search_type', 0, 'POST');
                $andor      = ' OR ';
                foreach ($queries as $oneQuery) {
                    $sql .= '(';
                    switch ($searchType) {
                        case 0: // Commence par

                            $cond = " LIKE '" . $oneQuery . "%' ";

                            break;
                        case 1: // Finit par

                            $cond = " LIKE '%" . $oneQuery . "' ";

                            break;
                        case 2: // Correspond à

                            $cond = " = '" . $oneQuery . "' ";

                            break;
                        case 3: // Contient

                            $cond = " LIKE '%" . $oneQuery . "%' ";

                            break;
                    }
                    $sql .= implode($cond, $fields) . $cond . ')';
                    ++$cnt;
                    if ($cnt != $count) {
                        $sql .= ' ' . $andor . ' ';
                    }
                }
            }
        }
        $_SESSION['criteria_oledrion'] = serialize($sql);
    } else {
        // $_GET['start'] est en place, on a cliqué sur un chevron pour aller voir les autres pages, il faut travailler à partir des informations de la session
        if (\Xmf\Request::hasVar('criteria_oledrion', 'SESSION')) {
            $sql = unserialize($_SESSION['criteria_oledrion']);
        }
    }
    $start    = \Xmf\Request::getInt('start', 0, 'GET');
    $sqlCount = str_replace('b.product_id, b.product_title, b.product_submitted, b.product_submitter', 'Count(*) as cpt', $sql);
    $result   = $xoopsDB->query($sqlCount);
    $rowCount = $xoopsDB->fetchArray($result);
    if ($rowCount['cpt'] > $limit) {
        require_once XOOPS_ROOT_PATH . '/class/pagenav.php';
        $pagenav = new \XoopsPageNav($rowCount['cpt'], $limit, $start, 'start');
        $xoopsTpl->assign('pagenav', $pagenav->renderNav());
    }

    $sql         .= ' GROUP BY b.product_id ORDER BY product_submitted DESC';
    $result      = $xoopsDB->query($sql, $limit, $start);
    $ret         = [];
    $tempProduct = $productsHandler->create(true);
    $count       = 1;
    while (false !== ($myrow = $xoopsDB->fetchArray($result))) {
        $ret                           = [];
        $ret['product_url_rewrited']   = $tempProduct->getLink($myrow['product_id'], $myrow['product_title']);
        $ret['product_title']          = $myts->htmlSpecialChars($myrow['product_title']);
        $ret['product_href_title']     = Oledrion\Utility::makeHrefTitle($myts->htmlSpecialChars($myrow['product_title']));
        $ret['product_time']           = $myrow['product_submitted'];
        $ret['product_uid']            = $myrow['product_submitter'];
        $ret['product_id']             = $myrow['product_id'];
        $ret['product_thumb_url']      = $myrow['product_thumb_url'];
        $ret['product_thumb_full_url'] = OLEDRION_PICTURES_URL . '/' . $myrow['product_thumb_url'];
        $ret['product_property1']      = $myrow['product_property1'];
        $ret['product_property2']      = $myrow['product_property2'];
        $ret['product_property3']      = $myrow['product_property3'];
        $ret['product_property4']      = $myrow['product_property4'];
        $ret['product_property5']      = $myrow['product_property5'];
        $ret['product_property6']      = $myrow['product_property6'];
        $ret['product_property7']      = $myrow['product_property7'];
        $ret['product_property8']      = $myrow['product_property8'];
        $ret['product_property9']      = $myrow['product_property9'];
        $ret['product_property10']     = $myrow['product_property10'];
        $ret['product_price']          = $myrow['product_price'];
        if (0 == $myrow['product_price']) {
            $criteria = new \CriteriaCompo();
            $criteria->add(new \Criteria('attribute_product_id', $myrow['product_id']));
            $attribute = $attributesHandler->getObjects($criteria, false);
            foreach ($attribute as $root) {
                $ret['product_price'] = $root->getVar('attribute_default_value');
            }
        }
        $ret['product_stock']     = $myrow['product_stock'];
        $ret['product_price_ttc'] = Oledrion\Utility::getTTC($ret['product_price'], '');
        $ret['product_count']     = $count;
        $ret['product_summary']   = $myrow['product_summary'];
        $xoopsTpl->append('products', $ret);
        ++$count;
    }
    unset($tempProduct);
} else {
    $xoopsTpl->assign('search_results', false);
    $xoopsTpl->assign('global_advert', Oledrion\Utility::getModuleOption('advertisement'));
    $xoopsTpl->assign('breadcrumb', Oledrion\Utility::breadcrumb([OLEDRION_URL . basename(__FILE__) => _OLEDRION_SEARCHFOR]));
    Oledrion\Utility::setMetas(Oledrion\Utility::getModuleName() . ' - ' . _OLEDRION_SEARCHFOR, Oledrion\Utility::getModuleName() . ' - ' . _OLEDRION_SEARCHFOR);
}

require_once OLEDRION_PATH . 'include/product_search_form.php';
$sform = Oledrion\Utility::formMarkRequiredFields($sform);
$xoopsTpl->assign('search_form', $sform->render());

Oledrion\Utility::setCSS();
Oledrion\Utility::setLocalCSS($xoopsConfig['language']);

require_once XOOPS_ROOT_PATH . '/footer.php';

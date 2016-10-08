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
 * Gestion des produits relatifs
 */
require __DIR__ . '/classheader.php';

/**
 * Class Oledrion_related
 */
class Oledrion_related extends Oledrion_Object
{
    /**
     * constructor
     *
     * normally, this is called from child classes only
     *
     * @access public
     */
    public function __construct()
    {
        $this->initVar('related_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('related_product_id', XOBJ_DTYPE_INT, null, false);
        $this->initVar('related_product_related', XOBJ_DTYPE_INT, null, false);
        $this->initVar('related_product_percent', XOBJ_DTYPE_INT, null, false);
    }
}

/**
 * Class OledrionOledrion_relatedHandler
 */
class OledrionOledrion_relatedHandler extends Oledrion_XoopsPersistableObjectHandler
{
    /**
     * OledrionOledrion_relatedHandler constructor.
     * @param XoopsDatabase|null $db
     */
    public function __construct(XoopsDatabase $db)
    { //                            Table               Classe                   Id
        parent::__construct($db, 'oledrion_related', 'oledrion_related', 'related_id');
    }

    /**
     * Supprime les produits relatifs rattachés à un produit
     *
     * @param integer $related_product_id L'identifiant du produit pour lequel il faut faire la suppression
     */
    public function deleteProductRelatedProducts($related_product_id)
    {
        $criteria = new Criteria('related_product_id', $related_product_id, '=');
        $this->deleteAll($criteria);
    }

    /**
     * Retourne la liste des produits relatifs d'une liste de produits
     *
     * @param  array $ids Les ID des produits dont on recherche les produits relatifs
     * @return array Objets de type oledrion_related
     */
    public function getRelatedProductsFromProductsIds($ids)
    {
        $ret = array();
        if (is_array($ids)) {
            $criteria = new Criteria('related_product_id', '(' . implode(',', $ids) . ')', 'IN');
            $ret      = $this->getObjects($criteria, true, true, '*', false);
        }

        return $ret;
    }

    /**
     * Update product percent
     *
     * @param $id
     * @param $related
     * @param $percent
     * @return bool
     * @internal param $
     */
    public function updatePercent($id, $related, $percent)
    {
        if ($percent > 100) {
            return false;
        } else {
            $sql = 'UPDATE ' . $this->table . ' SET `related_product_percent` = ' . (int)$percent . ' WHERE (related_product_id = ' . (int)$id . ') AND (related_product_related = ' . (int)$related . ')';

            return $this->db->queryF($sql);
        }
    }
}
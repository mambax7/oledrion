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
 * @author      Hervé Thouzard (http://www.herve-thouzard.com/)
 */

use XoopsModules\Oledrion;

/**
 * Gestion des votes sur les produits
 */

/**
 * Class VotedataHandler
 */
class VotedataHandler extends OledrionPersistableObjectHandler
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
        //                                Table                   Classe                             Id
        parent::__construct($db, 'oledrion_votedata', Votedata::class, 'vote_ratingid');
    }

    /**
     * Renvoie le nombre total de votes pour un produit ainsi que la sommes des votes
     *
     * @param int $product_id Identifiant du produit
     * @param int $totalVotes Variable passée par référence et devant contenir le nombre total de votes du produit
     * @param int $sumRating  Variable passée par référence et devant contenir le cumul des votes
     * @return int Rien
     */
    public function getCountRecordSumRating($product_id, &$totalVotes, &$sumRating)
    {
        $sql    = 'SELECT count( * ) AS cpt, sum( vote_rating ) AS sum_rating FROM ' . $this->table . ' WHERE vote_product_id = ' . (int)$product_id;
        $result = $this->db->query($sql);
        if (!$result) {
            return 0;
        }

        $myrow      = $this->db->fetchArray($result);
        $totalVotes = $myrow['cpt'];
        $sumRating  = $myrow['sum_rating'];
    }

    /**
     * Returns the (x) last votes
     *
     * @param int $start Starting position
     * @param int $limit count of items to return
     * @return array   Array of votedata objects
     */
    public function getLastVotes($start = 0, $limit = 0)
    {
        $tbl_datas = [];
        $criteria  = new \Criteria('vote_ratingid', 0, '<>');
        $criteria->setLimit($limit);
        $criteria->setStart($start);
        $criteria->setSort('vote_ratingtimestamp');
        $criteria->setOrder('DESC');
        $tbl_datas = $this->getObjects($criteria, true);

        return $tbl_datas;
    }

    /**
     * Suppression des votes d'un produit
     *
     * @param int $vote_product_id L'identifiant du produit
     * @return bool résultat de la suppression
     */
    public function deleteProductRatings($vote_product_id)
    {
        $criteria = new \Criteria('vote_product_id', $vote_product_id, '=');

        return $this->deleteAll($criteria);
    }

    /**
     * Indique si un utilisateur a déjà voté pour un produit
     *
     * @param int $vote_uid        L'identifiant de l'utilisateur
     * @param int $vote_product_id Le numéro du produit
     * @return bool True s'il a déjà voté sinon False
     */
    public function hasUserAlreadyVoted($vote_uid, $vote_product_id)
    {
        if (0 == $vote_uid) {
            $vote_uid = Oledrion\Utility::getCurrentUserID();
        }
        $criteria = new \CriteriaCompo();
        $criteria->add(new \Criteria('vote_product_id', $vote_product_id, '='));
        $criteria->add(new \Criteria('vote_uid', $vote_uid, '='));
        $count = $this->getCount($criteria);

        return $count > 0;
    }

    /**
     * Indique si un utilisateur anonyme a déjà voté (d'après son adresse IP)
     *
     * @param string $ip              L'adresse IP
     * @param int    $vote_product_id Ld'identifiant du produit
     * @return bool
     */
    public function hasAnonymousAlreadyVoted($ip = '', $vote_product_id = 0)
    {
        if ('' === $ip) {
            $ip = Oledrion\Utility::IP();
        }
        $anonwaitdays = 1;
        $yesterday    = (time() - (86400 * $anonwaitdays));
        $criteria     = new \CriteriaCompo();
        $criteria->add(new \Criteria('vote_product_id', $vote_product_id, '='));
        $criteria->add(new \Criteria('vote_uid', 0, '='));
        $criteria->add(new \Criteria('vote_ratinghostname', $ip, '='));
        $criteria->add(new \Criteria('vote_ratingtimestamp', $yesterday, '>'));
        $count = $this->getCount($criteria);

        return $count > 0;
    }

    /**
     * Crée un vote pour un produit
     *
     * @param int $vote_product_id L'identifiant du produit
     * @param int $vote_uid        L'identifiant de l'utilisateur
     * @param int $vote_rating     Le vote
     * @return bool résultat de la création du vote
     */
    public function createRating($vote_product_id, $vote_uid, $vote_rating)
    {
        $product = $this->create(true);
        $product->setVar('vote_product_id', $vote_product_id);
        $product->setVar('vote_uid', $vote_uid);
        $product->setVar('vote_rating', $vote_rating);
        $product->setVar('vote_ratinghostname', Oledrion\Utility::IP());
        $product->setVar('vote_ratingtimestamp', time());

        return $this->insert($product);
    }
}

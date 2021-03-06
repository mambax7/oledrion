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

/**
 * Gestion des textes affichés sur certaines pages pour les utilisateurs
 */
if (!defined('OLEDRION_ADMIN')) {
    exit();
}
/** @var \XoopsConfigHandler $configHandler */
$configHandler    = xoops_getHandler('config');
$moduleIdCriteria = new \Criteria('conf_modid', $xoopsModule->getVar('mid'));

switch ($action) {
    case 'default':

        xoops_cp_header();

        $sform = new \XoopsThemeForm(_MI_OLEDRION_ADMENU17, 'property', $baseurl);
        $sform->addElement(new \XoopsFormHidden('op', 'property'));
        $sform->addElement(new \XoopsFormHidden('action', 'fromproperty'));

        $property1 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY1, '<br>');
        $property1->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property1_title', 50, 255, Oledrion\Utility::getModuleOption('product_property1_title')));
        $property1->addElement(new \XoopsFormTextArea('', 'product_property1', Oledrion\Utility::getModuleOption('product_property1'), 5, 90));
        $property1->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property1);

        $property2 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY2, '<br>');
        $property2->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property2_title', 50, 255, Oledrion\Utility::getModuleOption('product_property2_title')));
        $property2->addElement(new \XoopsFormTextArea('', 'product_property2', Oledrion\Utility::getModuleOption('product_property2'), 5, 90));
        $property2->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property2);

        $property3 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY3, '<br>');
        $property3->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property3_title', 50, 255, Oledrion\Utility::getModuleOption('product_property3_title')));
        $property3->addElement(new \XoopsFormTextArea('', 'product_property3', Oledrion\Utility::getModuleOption('product_property3'), 5, 90));
        $property3->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property3);

        $property4 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY4, '<br>');
        $property4->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property4_title', 50, 255, Oledrion\Utility::getModuleOption('product_property4_title')));
        $property4->addElement(new \XoopsFormTextArea('', 'product_property4', Oledrion\Utility::getModuleOption('product_property4'), 5, 90));
        $property4->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property4);

        $property5 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY5, '<br>');
        $property5->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property5_title', 50, 255, Oledrion\Utility::getModuleOption('product_property5_title')));
        $property5->addElement(new \XoopsFormTextArea('', 'product_property5', Oledrion\Utility::getModuleOption('product_property5'), 5, 90));
        $property5->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property5);

        $property6 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY6, '<br>');
        $property6->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property6_title', 50, 255, Oledrion\Utility::getModuleOption('product_property6_title')));
        $property6->addElement(new \XoopsFormTextArea('', 'product_property6', Oledrion\Utility::getModuleOption('product_property6'), 5, 90));
        $property6->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property6);

        $property7 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY7, '<br>');
        $property7->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property7_title', 50, 255, Oledrion\Utility::getModuleOption('product_property7_title')));
        $property7->addElement(new \XoopsFormTextArea('', 'product_property7', Oledrion\Utility::getModuleOption('product_property7'), 5, 90));
        $property7->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property7);

        $property8 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY8, '<br>');
        $property8->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property8_title', 50, 255, Oledrion\Utility::getModuleOption('product_property8_title')));
        $property8->addElement(new \XoopsFormTextArea('', 'product_property8', Oledrion\Utility::getModuleOption('product_property8'), 5, 90));
        $property8->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property8);

        $property9 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY9, '<br>');
        $property9->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property9_title', 50, 255, Oledrion\Utility::getModuleOption('product_property9_title')));
        $property9->addElement(new \XoopsFormTextArea('', 'product_property9', Oledrion\Utility::getModuleOption('product_property9'), 5, 90));
        $property9->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property9);

        $property10 = new \XoopsFormElementTray(_AM_OLEDRION_PRODUCT_PROPERTY10, '<br>');
        $property10->addElement(new \XoopsFormText(_AM_OLEDRION_PRODUCT_PROPERTY_TITLE, 'product_property10_title', 50, 255, Oledrion\Utility::getModuleOption('product_property10_title')));
        $property10->addElement(new \XoopsFormTextArea('', 'product_property10', Oledrion\Utility::getModuleOption('product_property10'), 5, 90));
        $property10->setDescription(_AM_OLEDRION_PRODUCT_PROPERTY_DESC);
        $sform->addElement($property10);

        $buttonTray = new \XoopsFormElementTray('', '');
        $submit_btn = new \XoopsFormButton('', 'post', _AM_OLEDRION_MODIFY, 'submit');
        $buttonTray->addElement($submit_btn);
        $sform->addElement($buttonTray);
        $sform = Oledrion\Utility::formMarkRequiredFields($sform);
        $sform->display();

        require_once OLEDRION_ADMIN_PATH . 'admin_footer.php';

        break;
    case 'fromproperty':

        if (\Xmf\Request::hasVar('product_property1', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property1') != \Xmf\Request::getString('product_property1', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property1'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property1',
                    'conf_value'     => \Xmf\Request::getString('product_property1', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property1_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property1_title') != \Xmf\Request::getString('product_property1_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property1_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property1_title',
                    'conf_value'     => \Xmf\Request::getString('product_property1_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property2', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property2') != \Xmf\Request::getString('product_property2', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property2'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property2',
                    'conf_value'     => \Xmf\Request::getString('product_property2', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property2_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property2_title') != \Xmf\Request::getString('product_property2_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property2_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property2_title',
                    'conf_value'     => \Xmf\Request::getString('product_property2_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property3', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property3') != \Xmf\Request::getString('product_property3', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property3'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property3',
                    'conf_value'     => \Xmf\Request::getString('product_property3', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property3_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property3_title') != \Xmf\Request::getString('product_property3_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property3_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property3_title',
                    'conf_value'     => \Xmf\Request::getString('product_property3_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property4', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property4') != \Xmf\Request::getString('product_property4', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property4'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property4',
                    'conf_value'     => \Xmf\Request::getString('product_property4', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property4_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property4_title') != \Xmf\Request::getString('product_property4_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property4_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property4_title',
                    'conf_value'     => \Xmf\Request::getString('product_property4_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property5', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property5') != \Xmf\Request::getString('product_property5', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property5'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property5',
                    'conf_value'     => \Xmf\Request::getString('product_property5', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property5_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property5_title') != \Xmf\Request::getString('product_property5_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property5_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property5_title',
                    'conf_value'     => \Xmf\Request::getString('product_property5_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property6', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property6') != \Xmf\Request::getString('product_property6', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property6'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property6',
                    'conf_value'     => \Xmf\Request::getString('product_property6', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property6_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property6_title') != \Xmf\Request::getString('product_property6_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property6_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property6_title',
                    'conf_value'     => \Xmf\Request::getString('product_property6_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property7', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property7') != \Xmf\Request::getString('product_property7', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property7'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property7',
                    'conf_value'     => \Xmf\Request::getString('product_property7', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property7_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property7_title') != \Xmf\Request::getString('product_property7_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property7_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property7_title',
                    'conf_value'     => \Xmf\Request::getString('product_property7_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property8', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property8') != \Xmf\Request::getString('product_property8', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property8'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property8',
                    'conf_value'     =>  \Xmf\Request::getString('product_property8', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property8_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property8_title') != \Xmf\Request::getString('product_property8_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property8_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property8_title',
                    'conf_value'     => \Xmf\Request::getString('product_property8_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property9', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property9') !=  \Xmf\Request::getString('product_property9', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property9'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property9',
                    'conf_value'     => \Xmf\Request::getString('product_property9', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property9_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property9_title') != \Xmf\Request::getString('product_property9_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property9_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property9_title',
                    'conf_value'     => \Xmf\Request::getString('product_property9_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property10', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property10') != \Xmf\Request::getString('product_property10', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property10'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property10',
                    'conf_value'     => \Xmf\Request::getString('product_property10', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        if (\Xmf\Request::hasVar('product_property10_title', 'POST')) {
            if (Oledrion\Utility::getModuleOption('product_property10_title') != \Xmf\Request::getString('product_property10_title', '', 'POST')) {
                $criteria = new \CriteriaCompo();
                $criteria->add($moduleIdCriteria);
                $criteria->add(new \Criteria('conf_name', 'product_property10_title'));
                $config      = $configHandler->getConfigs($criteria);
                $config      = $config[0];
                $configValue = [
                    'conf_modid'     => $xoopsModule->getVar('mid'),
                    'conf_catid'     => 0,
                    'conf_name'      => 'product_property10_title',
                    'conf_value'     => \Xmf\Request::getString('product_property10_title', '', 'POST'),
                    'conf_formtype'  => 'hidden',
                    'conf_valuetype' => 'text',
                ];
                $config->setVars($configValue);
                $configHandler->insertConfig($config);
            }
        }

        Oledrion\Utility::updateCache();
        Oledrion\Utility::redirect(_AM_OLEDRION_SAVE_OK, $baseurl . '?op=property', 2);

        break;
}

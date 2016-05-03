<?php

/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*         DISCLAIMER   *
* *************************************** */

/* Do not edit or add to this file if you wish to upgrade Prestashop to newer
* versions in the future.
* *****************************************************
* @category   Belvg
* @package    catalog.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

if(!defined('_PS_VERSION_'))
    exit;

require_once _PS_MODULE_DIR_ . 'catalog/autoload.php';

class catalog extends Module
{

    public $_hooks = array(
        'moduleRoutes'
    );

    public function __construct()
    {
        $this->name = 'catalog';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Belvg';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Catalog');
        $this->description = $this->l('Create a custom catalog pages.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('Catalog'))
            $this->warning = $this->l('No name provided');
    }

    public function install()
    {
        if (!parent::install()
            || !Install::execute()
            || !$result = $this->registerHook($this->_hooks))
            return false;

        //create admin page for list custom pages
        $id_tab = Tab::getIdFromClassName('AdminModules');
        if (!$this->installModuleTab('AdminCatalogPages', $this->l('Catalog'), $id_tab))
            return false;

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !Uninstall::execute())
            return false;

        //unregister hooks
        if (isset($this->_hooks) && !empty($this->_hooks))
        {
            foreach ($this->_hooks as $hook)
            {
                if (!empty($hook) && !$this->unregisterHook($hook))
                {
                    return false;
                }
            }
        }

        //delete admin page
        if (!$this->uninstallModuleTab('AdminCatalogPages'))
            return false;

        return true;
    }

    public function hookModuleRoutes($params)
    {

        $default_routes = array();

        /*Array
        (
            [0] => Array
                (
                    [id_catalog_page] => 4
                    [date_add] => 2016-05-02 11:32:46
                    [date_upd] => 2016-05-02 12:00:41
                    [active] => 1
                    [id_category] => 4
                    [id_lang] => 2
                    [id_shop] => 1
                    [title] => test
                    [url] => test
                    [meta_title] =>
                    [description] =>
                )

        )*/
        foreach(CatalogPage::getCatalogPages() as $page)
        {
            $default_routes = array(
                'catalog_rule_' . $page['id_catalog_page'] => array(
                    'controller' =>    'module-catalog-category',
                    'rule' =>        $page['url'],
                    'keywords' => array(),
                    'params' => array(
                        'fc' => 'module',
                        'page' => '',
                        'id_catalog_page' => $page['id_catalog_page'],
                        'id_category' => $page['id_category'],
                    ),
                ),
            );
        }


        return $default_routes;
    }
    /**
     * Add new page in back office
     *
     * @param type $class_name
     * @param type $tab_name
     * @param type $id_parent
     * @param type $position
     *
     * @return type
     */
    public function installModuleTab($class_name, $tab_name, $id_parent = 0, $position = 0)
    {

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $class_name;
        $tab->name = array();

        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = $tab_name;

        $tab->id_parent = $id_parent;
        $tab->position = $position;
        $tab->module = $this->name;

        return $tab->save();
    }

    /**
     * Delete custom page of back office
     *
     * @param type $class_name
     *
     * @return type
     */
    public function uninstallModuleTab($class_name)
    {

        $id_tab = Tab::getIdFromClassName($class_name);

        if ($id_tab) {

            $tab = new Tab($id_tab);
            $tab->delete();
            return true;
        }

        return false;
    }
}
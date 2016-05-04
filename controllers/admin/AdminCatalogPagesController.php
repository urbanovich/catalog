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
* @package    AdminPagesController.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class AdminCatalogPagesController extends ModuleAdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'catalog_page';
        $this->className = 'CatalogPage';
        $this->lang = true;
        $this->deleted = false;
        $this->explicitSelect = true;
        $this->allow_export = true;

        $this->context = Context::getContext();

        $this->fields_list = array(
            'id_catalog_page' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->l('Title')
            ),
            'description' => array(
                'title' => $this->l('Description'),
                'callback' => 'getDescriptionClean',
                'orderby' => false
            ),
            'active' => array(
                'title' => $this->l('Displayed'),
                'active' => 'status',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'ajax' => true,
                'orderby' => false
            )
        );

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        $this->specificConfirmDelete = false;

        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        parent::__construct();
    }

    public function renderList()
    {
        $this->addRowAction('view');
        $this->addRowAction('add');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function renderView()
    {

        return $this->renderForm();
    }

    public function renderForm()
    {

        if (!$obj = $this->loadObject(TRUE))
        {
            return;
        }

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Subscription type'),
                'icon' => 'icon-tags'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'title',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Url'),
                    'name' => 'url',
                    'lang' => true,
                    'required' => true,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Meta title'),
                    'name' => 'meta_title',
                    'lang' => true,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'lang' => true,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type'  => 'categories',
                    'label' => $this->l('Categories'),
                    'name'  => 'id_category',
                    'tree'  => array(
                        'id'                  => 'categories-tree',
                        'selected_categories' => array($obj->id_category),
                        'root_category'       => $this->context->shop->getCategory()
                    )
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('CSS style'),
                    'name' => 'style',
                    'rows' => 25,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'html',
                    'name' => 'tokens',
                    'html_content' => $this->getCategoryTokens(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Template header'),
                    'name' => 'template_header',
                    'rows' => 25,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'html',
                    'name' => 'tokens',
                    'html_content' => $this->getProductTokens(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Template'),
                    'name' => 'template_content',
                    'rows' => 25,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'html',
                    'name' => 'tokens',
                    'html_content' => $this->getCategoryTokens(),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Template footer'),
                    'name' => 'template_footer',
                    'rows' => 25,
                    'hint' => $this->l('Forbidden characters:') . ' <>;=#{}'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Displayed'),
                    'name' => 'active',
                    'required' => false,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'name' => 'submitAdd' . $this->table,
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }

        return parent::renderForm();
    }

    public function getDescriptionClean($description)
    {
        return strip_tags(stripslashes($description));
    }

    public function getProductTokens()
    {
        $product_tokens = HelperToken::getTokens();
        $html = '<div class="tokens">';
        foreach($product_tokens as $token)
        {
            $html .= '<span class="' . $token . '"> {' . $token . '} </span>';
        }
        $html .= '</div>';

        return $html;
    }

    public function getCategoryTokens()
    {
        $product_tokens = HelperToken::getTokens('Category');
        $html = '<div class="tokens">';
        foreach($product_tokens as $token)
        {
            $html .= '<span class="' . $token . '"> {' . $token . '} </span>';
        }
        $html .= '</div>';

        return $html;
    }
}
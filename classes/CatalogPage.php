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
* @package    Page.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2007 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class CatalogPage extends ObjectModel
{

    public $id;

    public $id_catalog_page;

    public $title;

    public $url;

    public $meta_title;

    public $description;

    public $id_category;

    public $date_add;

    public $date_upd;

    public $active;

    public $style;

    public $template_header;

    public $template_content;

    public $template_footer;

    public static $definition = array(
        'table' => 'catalog_page',
        'primary' => 'id_catalog_page',
        'multilang' => true,
        'multilang_shop' => true,
        'fields' => array(
            'date_add' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_upd' =>           array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'id_category' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'style' =>              array('type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 50000),
            'template_header' =>    array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 50000),
            'template_content' =>   array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 50000),
            'template_footer' =>    array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 50000),

            //lang fields
            'title' =>              array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'url' =>                array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isLinkRewrite', 'size' => 255, 'require' => true),
            'meta_title' =>         array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
            'description' =>        array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isString', 'size' => 255),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation(self::$definition['table'], array('type' => 'shop'));
        parent::__construct($id, $id_lang, $id_shop);
    }

    public static function getCatalogPages()
    {
        $context = Context::getContext();

        return DB::getInstance()->executeS('SELECT cp.`id_catalog_page`, cp.`id_category`, cpl.`url`, cpl.`title` FROM `' . _DB_PREFIX_ . 'catalog_page` AS cp
                                            INNER JOIN `' . _DB_PREFIX_ . 'catalog_page_lang` AS cpl
                                                ON (cpl.id_catalog_page = cp.id_catalog_page
                                                    AND cpl.id_lang = ' . pSQL($context->language->id) . '
                                                    AND cpl.id_shop = ' . pSQL($context->shop->id) . ')
                                            WHERE cp.`active` = 1');
    }

}
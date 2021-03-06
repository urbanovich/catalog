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

class catalogCategoryModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        $this->n = Configuration::get('PS_PRODUCTS_PER_PAGE');
        $this->p = 1;
        parent::__construct();
    }

    public function initContent()
    {
        global $smarty;
        $header = clone $smarty;
        $content = clone $smarty;
        $footer = clone $smarty;

        parent::initContent();

        $id_lang = $this->context->language->id;
        $id_shop = $this->context->shop->id;

        $catalog_page = new CatalogPage(Tools::getValue('id_catalog_page'), $id_lang, $id_shop);
        $category = new Category(Tools::getValue('id_category'), $id_lang, $id_shop);

        $products = $category->getProducts($id_lang, $this->p, $this->n);
        foreach($category->getChildren(Tools::getValue('id_category'), $id_lang, true, $id_shop) as $children)
        {
            $c = new Category($children['id_category']);
            $products = array_merge($c->getProducts($id_lang, $this->p, $this->n), $products);
        }

        $category_tokens = HelperToken::getTokens('Category');
        foreach($category_tokens as $token)
        {
            $search_tokens[] = '{' . $token . '}';
            $replace_tokens[] = '{$' . $token . '}';
        }
        $template_header = str_replace($search_tokens, $replace_tokens, $catalog_page->template_header);
        $template_footer = str_replace($search_tokens, $replace_tokens, $catalog_page->template_footer);
        unset($search_tokens, $replace_tokens);

        $header->assign((array)$category);
        $footer->assign((array)$category);

        $product_tokens = HelperToken::getTokens();
        foreach($product_tokens as $token)
        {
            $search_tokens[] = '{' . $token . '}';
            $replace_tokens[] = '{$item.' . $token . '}';
        }
        $template_content = str_replace($search_tokens, $replace_tokens, $catalog_page->template_content);
        unset($search_tokens, $replace_tokens);

        $template_content = '{foreach $items as $item}'
                                . $template_content
                            . '{/foreach}';

        $content->assign(
            array(
                'items' => $products,
            )
        );

        $this->context->smarty->assign(
            array(
                'id_catalog_page' => $catalog_page->id_catalog_page,
                'style' => $catalog_page->style,
                'template_header' => $header->fetch('string:' . $template_header),
                'template_content' => $content->fetch('string:' . $template_content),
                'template_footer' => $footer->fetch('string:' . $template_footer),
            )
        );

        $this->setTemplate('category.tpl');
    }
}
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
* @package    Uninstall.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2010 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class Uninstall
{
    public static function execute()
    {
        if(!self::dropTables())
            return false;

        return true;
    }

    protected static function dropTables()
    {
        $error = Db::getInstance()->Execute('
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'catalog_page`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'catalog_page_lang`;
            DROP TABLE IF EXISTS `' . _DB_PREFIX_ . 'catalog_page_shop`;
        ');

        if(!$error)
            return false;

        return true;
    }
}

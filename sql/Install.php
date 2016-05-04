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
* @package    Install.php
* @author     Dzmitry Urbanovich (urbanovich.mslo@gmail.com)
* @site       http://module-presta.com
* @copyright  Copyright (c) 2010 - 2016 BelVG LLC. (http://www.belvg.com)
* @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
*/

class Install
{

    public static function execute()
    {

        if(!self::createTables())
            return false;

        return true;
    }

    protected static function createTables()
    {
        $error = Db::getInstance()->Execute('

            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'catalog_page` (
                `id_catalog_page` INT(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
                `date_add` TIMESTAMP NOT NULL,
                `date_upd` TIMESTAMP NOT NULL,
                `active` BOOL DEFAULT 1,
                `id_category` INT(11) NOT NULL,
                `style` TEXT(50000),
                `template_header` TEXT(50000),
                `template_content` TEXT(50000),
                `template_footer` TEXT(50000)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;

            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'catalog_page_lang` (
                `id_catalog_page` INT(11) NOT NULL,
                `id_lang` INT(11) NOT NULL,
                `id_shop` INT(11) NOT NULL,
                `title` CHAR(255),
                `url` CHAR(255) NOT NULL,
                `meta_title` CHAR(255),
                `description` CHAR(255)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;

            CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'catalog_page_shop` (
                `id_catalog_page` INT(11),
                `id_shop` INT(11) NOT NULL
            ) ENGINE=' . _MYSQL_ENGINE_ . ' CHARACTER SET=UTF8;

        ');

        if(!$error)
            return false;

        return true;
    }

}
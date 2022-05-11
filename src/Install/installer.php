<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

declare(strict_types=1);

// namespace cotizador\Install;

// use Db;
// use Module;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
class Installer
{
    /**
     * Module's installation entry point.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function install(Module $module)
    {

        if (!$this->registerHooks($module)) {
            return false;
        }

        if (!$this->installDatabase()) {
            return false;
        }

        if (!$this->installConfiguration()) {
            return false;
        }

        // if (!$this->installTab()) {
        //     return false;
        // }

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!$this->uninstallDatabase()) {
            return false;
        }

        if (!$this->removeConfiguration()) {
            return false;
        }

        // if (!$this->uninstallTab()) {
        //     return false;
        // }

        return true;
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase()
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_solicitud_cotizacion`(
                `id_cotizacion` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(128), `phone` VARCHAR(15), `id_product` INT(10), 
                `quantity` INT(11), `id_plazo_entrega` INT(11), `id_tipo_trabajo` INT(11), `id_forma_pago` INT(11), `comment` varchar(512), `allow` INT(1), 
                `replied` INT(1), `file` VARCHAR(256), `datetime` DATETIME NOT NULL default CURRENT_TIMESTAMP
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_cotizador_producto`(
                `id_cotizador_producto` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_product` INT(10), `enabled` INT(1), `min_qty` INT(10), `base_price` DOUBLE
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_plazo_entrega`(
                `id_plazo_entrega` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `description` VARCHAR(256), `num_days` INT(10)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_plazo`(
                `id` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_plazo_entrega` INT(10), `id_product` INT(10), `price_factor` DOUBLE, `enabled` INT(1), `max_qty` INT(10)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_tipo_trabajo`(
                `id_tipo_trabajo` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `description` VARCHAR(256)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_trabajo`(
                `id_prod_trabajo` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_tipo_trabajo` INT(10), `id_product` INT(10), `price_factor` DOUBLE, `enabled` INT(1)
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_forma_pago`(
                `id_forma_pago` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `description` VARCHAR(256)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_forma_pago`(
                `id_prod_pago` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `id_forma_pago` INT(10), `id_product` INT(10), `price_factor` DOUBLE, `enabled` INT(1)
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",

            // Next Inserts for development purposes only
            "INSERT INTO `ps_extraimagen_cotizador_producto` (`id_cotizador_producto`, `id_product`, `enabled`, `min_qty`, `base_price`) VALUES
            (1,	18,	1,	100,	100),
            (2,	5,	1,	100,	100);",
            "INSERT INTO `ps_extraimagen_producto_forma_pago` (`id_prod_pago`, `id_forma_pago`, `id_product`, `price_factor`, `enabled`) VALUES
                (1,	1,	18,	2,	1),
                (2,	2,	18,	0,	0),
                (3,	3,	18,	0,	0),
                (4,	1,	5,	2,	1),
                (5,	2,	5,	0,	0),
                (6,	3,	5,	0,	0);",
            "INSERT INTO `ps_extraimagen_producto_plazo` (`id`, `id_plazo_entrega`, `id_product`, `price_factor`, `enabled`, `max_qty`) VALUES
                (1,	1,	18,	2,	1,	0),
                (2,	2,	18,	0,	0,	0),
                (3,	3,	18,	0,	0,	0),
                (4,	4,	18,	0,	0,	0),
                (5,	5,	18,	0,	0,	0),
                (6,	1,	5,	2,	1,	0),
                (7,	2,	5,	0,	0,	0),
                (8,	3,	5,	0,	0,	0),
                (9,	4,	5,	0,	0,	0),
                (10,5,	5,	0,	0,	0);",
            "INSERT INTO `ps_extraimagen_producto_trabajo` (`id_prod_trabajo`, `id_tipo_trabajo`, `id_product`, `price_factor`, `enabled`) VALUES
                (1,	1,	18,	2,	1),
                (2,	2,	18,	0,	0),
                (3,	3,	18,	0,	0),
                (4,	4,	18,	0,	0),
                (5,	1,	5,	2,	1),
                (6,	2,	5,	0,	0),
                (7,	3,	5,	0,	0),
                (8,	4,	5,	0,	0);",
            "INSERT INTO `ps_extraimagen_solicitud_cotizacion` (`id_cotizacion`, `email`, `phone`, `id_product`, `quantity`, `id_plazo_entrega`, `id_tipo_trabajo`, `id_forma_pago`, `comment`, `allow`, `replied`, `file`, `datetime`) VALUES
            (1,	'user@extraimagen.cl',	'+56974471398',	18,	100,	1,	1,	1,	'',	0,	NULL,	'/var/www/html/prestashop/upload/2022_05_10_09_10_28_',	'2022-05-10 13:10:28'),
            (2,	'user@extraimagen.cl',	'+56974471398',	18,	100,	1,	1,	1,	'',	0,	NULL,	'/var/www/html/prestashop/upload/2022_05_10_09_10_53_choose.png',	'2022-05-10 13:10:53');"    
        ];

        return ($this->executeQueries($queries) && $this->populateDatabase());
    }

    private function populateDatabase()
    {
        $result_1 = Db::getInstance()->insert(
            'extraimagen_plazo_entrega',
            [
                [
                    'description' => 'super express',
                    'num_days' => 1,
                ],
                [
                    'description' => 'express',
                    'num_days' => 3,
                ],
                [
                    'description' => 'normal',
                    'num_days' => 5,
                ],
                [
                    'description' => 'semanal',
                    'num_days' => 7,
                ],
                [
                    'description' => 'quincenal',
                    'num_days' => 15,
                ],
            ]
        );

        $result_2 = Db::getInstance()->insert(
            'extraimagen_tipo_trabajo',
            [
                [
                    'description' => 'Impresión 1 color',
                ],
                [
                    'description' => 'Impresión 2 colores',
                ],
                [
                    'description' => 'Impresión full color',
                ],
                [
                    'description' => 'Grabado Laser',
                ],
            ]
        );

        $result_3 = Db::getInstance()->insert(
            'extraimagen_forma_pago',
            [
                [
                    'description' => 'Transferencia',
                ],
                [
                    'description' => 'Orden de Compra a 30 días',
                ],
                [
                    'description' => 'Orden de Compra a 60 días',
                ],
            ]
        );

        return ($result_1 && $result_2 && $result_3);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase()
    {
        $queries = [
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_cotizador_producto`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_solicitud_cotizacion`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_plazo_entrega`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_plazo`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_tipo_trabajo`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_trabajo`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_forma_pago`",
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "extraimagen_producto_forma_pago`",
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Register hooks for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function registerHooks(Module $module)
    {
        $hooks = [
            'displayAfterProductThumbs',
            // 'displayProductAdditionalInfo',
            'displayAdminProductsExtra',
            'actionProductUpdate',
        ];

        return (bool) $module->registerHook($hooks);
    }

        /**
     * Setup configuration for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function installConfiguration()
    {

        return (Configuration::updateValue('COTIZADOR_NAME', 'ExtraImagen')
                && Configuration::updateValue('COTIZADOR_MESSAGE', 'Este mensaje se mostrará en el cotizador y debe configurarse en el administrador')
                && Configuration::updateValue('COTIZADOR_STEPS_COLOR', '#455A64')
                && Configuration::updateValue('COTIZADOR_LINK_COLOR', '#455A64')
                && Configuration::updateValue('COTIZADOR_LINK_LOGGEDIN_TEXT', 'Cotizar aquí')
                && Configuration::updateValue('COTIZADOR_LINK_NOT_LOGGEDIN_TEXT', 'Registrarse para cotizar')
            );
    }

    private function removeConfiguration()
    {

        return (Configuration::deleteByName('COTIZADOR_NAME')
                && Configuration::deleteByName('COTIZADOR_MESSAGE')
                && Configuration::deleteByName('COTIZADOR_STEPS_COLOR')
                && Configuration::deleteByName('COTIZADOR_LINK_COLOR')
                && Configuration::deleteByName('COTIZADOR_LINK_LOGGEDIN_TEXT')
                && Configuration::deleteByName('COTIZADOR_LINK_NOT_LOGGEDIN_TEXT'));

    }

    private function installTab()
    {
        $tabId = (int) Tab::getIdFromClassName('CotizacionAdminController');
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = 'CotizacionAdminController';
        // Only since 1.7.7, you can define a route name
        // $tab->route_name = 'admin_my_symfony_routing';
        $tab->name = "Cotizaciones";
        $atb->parent_class_name = 'AdminParentOrders';
        // $tab->id_parent = (int) Tab::getIdFromClassName('AdminParentOrders');
        // $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab()
    {
        $tabId = (int) Tab::getIdFromClassName('Cotizador');
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    /**
     * A helper that executes multiple database queries.
     *
     * @param array $queries
     *
     * @return bool
     */
    private function executeQueries(array $queries)
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
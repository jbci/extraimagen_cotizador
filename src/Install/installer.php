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

use Db;
use Module;

/**
 * Class responsible for modifications needed during installation/uninstallation of the module.
 */
class Installer
{
    // /**
    //  * @var FixturesInstaller
    //  */
    // private $fixturesInstaller;

    // public function __construct(FixturesInstaller $fixturesInstaller)
    // {
    //     $this->fixturesInstaller = $fixturesInstaller;
    // }

    /**
     * Module's installation entry point.
     *
     * @param Module $module
     *
     * @return bool
     */
    public function install(Module $module): bool
    {

        Logger::addLog("Start Installer install()");
        if (!$this->registerHooks($module)) {
            Logger::addLog("Failed register hooks");
            return false;
        }
        Logger::addLog("2 Installer install()");

        if (!$this->installDatabase()) {
            Logger::addLog("Failed installDatabase");
            return false;
        }
        Logger::addLog("3 Installer install()");

        if (!$this->installConfiguration()) {
            Logger::addLog("Failed installConfiguration");
            return false;
        }
        Logger::addLog("4 Installer install()");
        // $this->fixturesInstaller->install();

        return true;
    }

    /**
     * Module's uninstallation entry point.
     *
     * @return bool
     */
    public function uninstall(): bool
    {
        if (!$this->uninstallDatabase()) {
            return false;
        }


        if (!$this->removeConfiguration()) {
            return false;
        }

        return true;
    }

    /**
     * Install the database modifications required for this module.
     *
     * @return bool
     */
    private function installDatabase(): bool
    {
        $queries = [
            "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "cotizaciones_extraimagen`(
                `id_cotizacion` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `email` VARCHAR(128), `phone` VARCHAR(15), `id_product` INT(10), 
                `qty` INT(11), `days` INT(11), `colors` INT(11), `comment` varchar(512), `allow` INT(1),
                `file` VARCHAR(256), `datetime` DATETIME NOT NULL default CURRENT_TIMESTAMP
                ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;",
        ];

        return $this->executeQueries($queries);
    }

    /**
     * Uninstall database modifications.
     *
     * @return bool
     */
    private function uninstallDatabase(): bool
    {
        $queries = [
            "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "cotizaciones_extraimagen`",
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
    private function registerHooks(Module $module): bool
    {
        Logger::addLog("registerHooks start");
        // Logger::addLog($module);
        

        // Hooks available in the order view page.
        $hooks = [
            'displayAfterProductThumbs',
            'displayAdminProductsExtra',
            'actionProductUpdate',
        ];
        Logger::addLog("registerHooks 2");

        return (bool) $module->registerHook($hooks);
        // return true;
    }

        /**
     * Setup configuration for the module.
     *
     * @param Module $module
     *
     * @return bool
     */
    private function installConfiguration(): bool
    {

        return (Configuration::updateValue('COTIZADOR_NAME', 'ExtraImagen')
                && Configuration::updateValue('COTIZADOR_MESSAGE', 'Este mensaje se mostrará en el cotizador y debe configurarse en el administrador'));
    }

    private function removeConfiguration(): bool
    {

        return (Configuration::deleteByName('COTIZADOR_NAME')
                && Configuration::deleteByName('COTIZADOR_MESSAGE'));

    }

    /**
     * A helper that executes multiple database queries.
     *
     * @param array $queries
     *
     * @return bool
     */
    private function executeQueries(array $queries): bool
    {
        foreach ($queries as $query) {
            if (!Db::getInstance()->execute($query)) {
                return false;
            }
        }

        return true;
    }
}
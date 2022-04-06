

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
// use src\Install\Installer;

require_once _PS_MODULE_DIR_ . 'cotizador/src/Install/installer.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class Cotizador extends Module
{
    public function __construct()
    {
        $this->name = 'cotizador';
        $this->tab = 'other';
        $this->version = '0.0.1';
        $this->author = 'jbci';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.7.1',
            'max' => '1.7.99',
        ];
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Cotizador de Trabajos ExtraImagen');
        $this->description = $this->l('Módulo de cotización de ExtraImagen.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

        if (!Configuration::get('COTIZADOR_NAME')) {
            $this->warning = $this->l('No name provided');
        }

        $this->tabs = [
            [
                'route_name' => '',
                'class_name' => 'CotizacionAdmin',
                'visible' => true,
                'name' => 'Cotizaciones',
                'parent_class_name' => 'SELL',
                'wording' => '',
                'wording_domain' => ''
            ],
        ];
    }

    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        $installer = new Installer();
        
        return (parent::install()
            && $installer->install($this));
            
    }

    public function uninstall()
    {
        $installer = new Installer();
        
        return (parent::uninstall()
            && $installer->uninstall());
    }

    /**
     * This method handles the module's configuration page
     * @return string The page's HTML content 
     */
    public function getContent()
    {
        $output = '';

        // this part is executed only when the form is submitted
        if (Tools::isSubmit('submit' . $this->name)) {
            // retrieve the value set by the user
            $configValue = (string) Tools::getValue('COTIZADOR_MESSAGE');
            $configColorValue = (string) Tools::getValue('COTIZADOR_STEPS_COLOR');

            // check that the value is valid
            if (empty($configValue) || !Validate::isGenericName($configValue) || empty($configColorValue) || !Validate::isGenericName($configColorValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('COTIZADOR_MESSAGE', $configValue);
                Configuration::updateValue('COTIZADOR_STEPS_COLOR', $configColorValue);
                $output = $this->displayConfirmation($this->l('Settings updated'));
            }
        }

        // display any message, then the form
        return $output . $this->displayForm();
    }

    /**
     * Builds the configuration form
     * @return string HTML code
     */
    public function displayForm()
    {
        // Init Fields form array
        $form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('Settings'),
                ],
                'input' => [
                    [
                        'type' => 'text',
                        'label' => $this->l('Configuration value'),
                        'name' => 'COTIZADOR_MESSAGE',
                        'size' => 200,
                        'required' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Color'),
                        'name' => 'COTIZADOR_STEPS_COLOR',
                        'size' => 10,
                        'required' => true,
                    ],
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                ],
            ],
        ];

        $helper = new HelperForm();

        // Module, token and currentIndex
        $helper->table = $this->table;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&' . http_build_query(['configure' => $this->name]);
        $helper->submit_action = 'submit' . $this->name;

        // Default language
        $helper->default_form_language = (int) Configuration::get('PS_LANG_DEFAULT');

        // Load current value into the form
        $helper->fields_value['COTIZADOR_MESSAGE'] = Tools::getValue('COTIZADOR_MESSAGE', Configuration::get('COTIZADOR_MESSAGE'));
        $helper->fields_value['COTIZADOR_STEPS_COLOR'] = Tools::getValue('COTIZADOR_STEPS_COLOR', Configuration::get('COTIZADOR_STEPS_COLOR'));

        return $helper->generateForm([$form]);
    }

    private function checkLoggedInUser()
    {
        // Logger::addLog("checkLoggedInUser => started");
        if ($this->context->customer->isLogged()) {
            // Logger::addLog("checkLoggedInUser => true");
            
        } else {
            // Logger::addLog("checkLoggedInUser => false");
        }
        
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        // Logger::addLog("hookDisplayAfterProductThumbs => started");
        // var_dump($params);die;
        $id_product = Tools::getValue('id_product');
        if (Tools::isSubmit('submit_cotizador')) {
            $email = Tools::getValue('email');
            $phone = Tools::getValue('phone');
            $extrai_work_days = (int)Tools::getValue('extrai_work_days');
            // Logger::addLog("hookDisplayAfterProductThumbs => extrai_work_days: {$extrai_work_days}");
            $quantity = (int)Tools::getValue('quantity');
            $extrai_work_type = (int)Tools::getValue('extrai_work_type');
            // Logger::addLog("hookDisplayAfterProductThumbs => extrai_work_type: {$extrai_work_type}");
            $comment = Tools::getValue('comment');
            $allow = (int)Tools::getValue('email_allow');
            $id_product = Tools::getValue('id_product');
            $insert = array(
                'email' => $email,
                'phone' => $phone,
                'id_product' => (int)$id_product,
                'quantity' => (int)$quantity,
                'id_plazo_entrega' => (int)$extrai_work_days,
                'id_tipo_trabajo' => (int)$extrai_work_type,
                'allow' => (int)$allow,
                'file' => "file",
                'comment' => $comment,
            );

            $result = Db::getInstance()->insert('extraimagen_solicitud_cotizacion', $insert);

            $product = new Product($id_product);
            // Logger::addLog("id_product => {$id_product}");
            $float_price = floatval($product->price);
            $price = $product->price;
            // $price = $product->getPrice();
            // Logger::addLog($price);
            // Logger::addLog($price);
            // Logger::addLog($price);
            // $total = $price * $quantity;
            // Logger::addLog($total);

            $this->sendCotizacionEmail($insert);

        }
        // Logger::addLog("hookDisplayAfterProductThumbs => 1");

        // $product = new Product($id_product);
        $langID = $this->context->language->id;

        $product = new Product($id_product, false, $langID);
        $this->context->smarty->assign([
            'cotizador_message' => Configuration::get('COTIZADOR_MESSAGE'),
            'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
            'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display'),
            'customer_logged' => $this->context->customer->isLogged(),
            'customer_email' => $this->context->customer->email,
            'requested_product' => $product,
            'requested_product_name' => strval($product->name),
            'requested_product_id' => $id_product,
            'min_qty' => $this->getCotizadorProductoMinQty($id_product),
            'prod_plazos' => $this->getCotizadorProductoPlazos($id_product),
            'tipo_trabajos' => $this->getCotizadorProductoTrabajos($id_product),
            'base_price' => $this->getCotizadorProductoBasePrice($id_product),
            'steps_color' => Configuration::get('COTIZADOR_STEPS_COLOR'),
        ]);
        // Logger::addLog("hookDisplayAfterProductThumbs => 2");
        // Logger::addLog("hookDisplayAfterProductThumbs id_product=> {$id_product}");
        if ($this->getCotizadorProductoEnabled($id_product)) {
            // Logger::addLog("steps should be added!");
            return $this->display(__FILE__, 'steps_cotizador.tpl');
            // return $this->display(__FILE__, 'cotizador.tpl');
        }
        // Logger::addLog("hookDisplayAfterProductThumbs => end");
        // return $this->display(__FILE__, 'steps_cotizador.tpl');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int)$params['id_product'];
        if (Validate::isLoadedObject($product = new Product($id_product))) {
            $checked = "";
            if ($this->getCotizadorProductoEnabled($id_product)) {
                $checked = "checked";
            }
            
            $this->context->smarty->assign([
                'allow_cotizador' => $checked,
                'min_qty' => $this->getCotizadorProductoMinQty($id_product),
                'base_price' => $this->getCotizadorProductoBasePrice($id_product),
                'prod_plazos' => $this->getCotizadorProductoPlazos($id_product),
                'tipo_trabajos' => $this->getCotizadorProductoTrabajos($id_product),
            ]);

            return $this->display(__FILE__, 'admin_prod_form.tpl');
        }
    }

    private function getCotizadorProductoEnabled($id_product)
    {
        // Logger::addLog("getCotizadorProductoEnabled => started");

        $request = "SELECT enabled FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";

        $is_enabled = Db::getInstance()->getValue($request);
        if (!$is_enabled or $is_enabled == 0) {
            // Logger::addLog("getCotizadorProductoEnabled => falseeee");
            return false;
        } else {
            // Logger::addLog("getCotizadorProductoEnabled => trueeee");
            return true;
        }
    }

    private function getCotizadorProductoMinQty($id_product)
    {
        $request = "SELECT min_qty FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";

        $min_qty = Db::getInstance()->getValue($request);
        if (!$min_qty) {
            return false;
        } else {
            return $min_qty;
        }
    }

    private function getCotizadorProductoBasePrice($id_product)
    {
        $request = "SELECT base_price FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";

        $base_price = Db::getInstance()->getValue($request);
        if (!$base_price) {
            return false;
        } else {
            return $base_price;
        }
    }

    private function getCotizadorProductoPlazos($id_product)
    {
        $request = "SELECT * FROM (
                    SELECT b.*, a.id_product, a.price_factor, a.enabled, a.max_qty FROM ps_extraimagen_producto_plazo a
                                LEFT JOIN ps_extraimagen_plazo_entrega b ON a.id_plazo_entrega = b.id_plazo_entrega
                    AND id_product = {$id_product}
                                UNION
                                SELECT b.*, a.id_product, a.price_factor, a.enabled, a.max_qty FROM ps_extraimagen_producto_plazo a
                                RIGHT JOIN ps_extraimagen_plazo_entrega b ON a.id_plazo_entrega = b.id_plazo_entrega
                    AND id_product = {$id_product}) as s
                    WHERE id_plazo_entrega IS NOT NULL;";

        $result = Db::getInstance()->executeS($request);
        if (!$result) {
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        } else {
            return $result;
        }
    }

    private function getCotizadorProductoTrabajos($id_product)
    {
        $request = "SELECT * FROM (
                    SELECT b.*, a.id_product, a.price_factor, a.enabled FROM ps_extraimagen_producto_trabajo a
                                LEFT JOIN ps_extraimagen_tipo_trabajo b ON a.id_tipo_trabajo = b.id_tipo_trabajo
                    AND id_product = {$id_product}
                                UNION
                                SELECT b.*, a.id_product, a.price_factor, a.enabled FROM ps_extraimagen_producto_trabajo a
                                RIGHT JOIN ps_extraimagen_tipo_trabajo b ON a.id_tipo_trabajo = b.id_tipo_trabajo
                    AND id_product = {$id_product}) as s
                    WHERE id_tipo_trabajo IS NOT NULL;";

        $result = Db::getInstance()->executeS($request);
        if (!$result) {
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        } else {
            return $result;
        }
    }

    public function hookActionProductUpdate($params)
    {
        $accept_value =(int)Tools::getValue('accept');
        if (isset($accept_value) and $accept_value == 1) {
            $accept_value =Tools::getValue('accept');
            $params_id_product = (int)$params['id_product'];

            $allow_cotizador = (int)Tools::getValue('allow_cotizador');
            $min_qty = (int)Tools::getValue('min_qty', 1);
            $base_price = (int)Tools::getValue('base_price', 0);

            $this-> updateCotizadorProducto($params_id_product, $allow_cotizador, $min_qty, $base_price);
            $this-> updateProductoPlazo($params);
            $this-> updateProductoTipoTrabajo($params);
        }
    }

    private function updateProductoPlazo($params)
    {
        // Logger::addLog("updateProductoPlazo start: ");

        $params_id_product = (int)$params['id_product'];

        // $this->getPlazoIds();
        foreach ($this->getPlazoIds() as $id_plazo_entrega) {
            
            $price_factor = Tools::getValue("price_factor_{$id_plazo_entrega}");
            $allow_plazo = Tools::getValue("allow_plazo_{$id_plazo_entrega}");
            $max_qty = Tools::getValue("max_qty_{$id_plazo_entrega}");
            $enabled = 0;
            if (isset($allow_plazo) && $allow_plazo == 'on'){
                $enabled = 1;
            }
            
            $query = "SELECT count(id) FROM `" . _DB_PREFIX_ . "extraimagen_producto_plazo` WHERE id_product = {$params_id_product} and id_plazo_entrega = {$id_plazo_entrega};";
            $queryCount = (int)Db::getInstance()->getValue($query);

            if ($queryCount > 0) {
                $result = Db::getInstance()->update('extraimagen_producto_plazo', [
                    'price_factor' => floatval($price_factor),
                    'enabled' => (int)$enabled,
                    'max_qty' => (int)$max_qty,
                ], "id_product = {$params_id_product} AND id_plazo_entrega = {$id_plazo_entrega}", 1, true);
                if (!$result) {
                    $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
                }
            } else {
                $result = Db::getInstance()->insert('extraimagen_producto_plazo', [
                    'id_product' => (int)$params_id_product,
                    'id_plazo_entrega' => (int)$id_plazo_entrega,
                    'price_factor' => floatval($price_factor),
                    'max_qty' => (int)$max_qty,
                    'enabled' => (int)$enabled,
                ]);
                if (!$result) {
                    $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
                }
                
            }
        }

    }
    private function updateProductoTipoTrabajo($params)
    {
        // Logger::addLog("updateProductoTipoTrabajo start: ");

        $params_id_product = (int)$params['id_product'];

        foreach ($this->getTipoTrabajoIds() as $id_tipo_trabajo) {
            // Logger::addLog("updateProductoTipoTrabajo id_tipo_trabajo: {$id_tipo_trabajo}");
            
            $price_factor = Tools::getValue("price_factor_tipo_{$id_tipo_trabajo}");
            // Logger::addLog("updateProductoTipoTrabajo price_factor_tipo_{$id_tipo_trabajo}: {$price_factor}");
            $allow_tipo = Tools::getValue("allow_tipo_{$id_tipo_trabajo}");
            // Logger::addLog("updateProductoTipoTrabajo allow_tipo_{$id_tipo_trabajo}: {$allow_tipo}");
            $enabled = 0;
            if (isset($allow_tipo) && $allow_tipo == 'on'){
                $enabled = 1;
            }
            
            $query = "SELECT count(id_tipo_trabajo) FROM `" . _DB_PREFIX_ . "extraimagen_producto_trabajo` WHERE id_product = {$params_id_product} and id_tipo_trabajo = {$id_tipo_trabajo};";
            // Logger::addLog("updateProductoTipoTrabajo query: {$query}");
            $queryCount = (int)Db::getInstance()->getValue($query);

            if ($queryCount > 0) {
                // Logger::addLog("updateProductoTipoTrabajo => update");
                $result = Db::getInstance()->update('extraimagen_producto_trabajo', [
                    'price_factor' => floatval($price_factor),
                    'enabled' => (int)$enabled,
                ], "id_product = {$params_id_product} AND id_tipo_trabajo = {$id_tipo_trabajo}", 1, true);
                if (!$result) {
                    $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
                }
            } else {
                // Logger::addLog("updateProductoTipoTrabajo => insert");
                $result = Db::getInstance()->insert('extraimagen_producto_trabajo', [
                    'id_product' => (int)$params_id_product,
                    'id_tipo_trabajo' => (int)$id_tipo_trabajo,
                    'price_factor' => floatval($price_factor),
                    'enabled' => (int)$enabled,
                ]);
                if (!$result) {
                    $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
                }
                
            }
            // Logger::addLog("updateProductoTipoTrabajo => foreach end");
        }

    }

    private function getPlazoIds()
    {
        $query = "SELECT id_plazo_entrega FROM " . _DB_PREFIX_ . "extraimagen_plazo_entrega LIMIT 100;";
        $results = Db::getInstance()->executeS($query);
        $ids = [];
        if (!$results) {
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        } else {
            $ids = array_map(function ($row) {
                return $row['id_plazo_entrega'];
            }, $results);
        }

        return $ids;
    }

    private function getTipoTrabajoIds()
    {
        // Logger::addLog("getTipoTrabajoIds => started ");
        $query = "SELECT id_tipo_trabajo FROM " . _DB_PREFIX_ . "extraimagen_tipo_trabajo LIMIT 100;";
        // Logger::addLog("getTipoTrabajoIds => query: {$query} ");
        $results = Db::getInstance()->executeS($query);
        $ids = [];
        if (!$results) {
            // Logger::addLog("getTipoTrabajoIds => if : not results ");
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        } else {
            // Logger::addLog("getTipoTrabajoIds => if : results ");
            $ids = array_map(function ($row) {
                // Logger::addLog("getTipoTrabajoIds => id_tipo_trabajo : {$row['id_tipo_trabajo']} ");
                return $row['id_tipo_trabajo'];
            }, $results);
        }

        return $ids;
    }

    private function updateCotizadorProducto($id_product, $enabled, $min_qty, $base_price)
    {
        // Logger::addLog("allow_cotizador: {$enabled} ");

        $query = "SELECT count(id_cotizador_producto) FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";
        $queryCount = (int)Db::getInstance()->getValue($query);

        if ($queryCount > 0) {
            $result = Db::getInstance()->update('extraimagen_cotizador_producto', [
                'enabled' => (int)$enabled,
                'min_qty' => (int)$min_qty,
                'base_price' => (int)$base_price,
            ], "id_product = {$id_product}", 1, true);
            if (!$result) {
                $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
            }
        } else {
            $result = Db::getInstance()->insert('extraimagen_cotizador_producto', [
                'id_product' => (int)$id_product,
                'enabled' => (int)$enabled,
                'min_qty' => (int)$min_qty,
                'base_price' => (int)$base_price,
            ]);
            if (!$result) {
                $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
            }
        }
    }
    
    public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerStylesheet(
            'cotizador-style',
            $this->_path . 'views/css/cotizador.css',
            [
                'media' => 'all',
                'priority' => 1000,
            ]
        );

        $this->context->controller->registerJavascript(
            'cotizador-javascript',
            $this->_path . 'views/js/cotizador.js',
            [
                'position' => 'bottom',
                'priority' => 1000,
            ]
        );
    }

    private function sendCotizacionEmail($insert)
    {
        Logger::addLog("sendCotizacionEmail => start");
        $template_path = dirname(__FILE__).'/mails/';
        // Logger::addLog($template_path);

        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
            'cotizacion', // email template file to be use
            ' Solicitud de Cotización', // email subject
            array(
                '{email}' => Configuration::get('PS_SHOP_EMAIL'), // sender email address
                '{message}' => 'Hello world' // email content
            ),
            Configuration::get('PS_SHOP_EMAIL'), // receiver email address
            $insert['email'], //receiver name
            Configuration::get('PS_SHOP_EMAIL'), //from email address
            NULL,  //from name
            NULL, //file attachment
            NULL, //mode smtp
            $template_path //custom template path
        );

        // Logger::addLog("sendCotizacionEmail => end");
    }
}

<?php

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

            // check that the value is valid
            if (empty($configValue) || !Validate::isGenericName($configValue)) {
                // invalid value, show an error
                $output = $this->displayError($this->l('Invalid Configuration value'));
            } else {
                // value is ok, update it and display a confirmation message
                Configuration::updateValue('COTIZADOR_MESSAGE', $configValue);
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

        return $helper->generateForm([$form]);
    }

    public function hookDisplayAfterProductThumbs($params)
    {
        $id_product = Tools::getValue('id_product');
        if (Tools::isSubmit('submit_cotizador')) {
            $email = Tools::getValue('email');
            $phone = Tools::getValue('phone');
            $days = (int)Tools::getValue('days');
            $qty = (int)Tools::getValue('qty');
            $colors = (int)Tools::getValue('colors');
            $comment = Tools::getValue('comment');
            $allow = (int)Tools::getValue('email_allow');

            $id_product = Tools::getValue('id_product');

            $insert = array(
                'email' => $email,
                'phone' => $phone,
                'id_product' => (int)$id_product,
                'qty' => (int)$qty,
                'days' => (int)$days,
                'colors' => (int)$colors,
                'allow' => (int)$allow,
                'file' => "file",
                'comment' => $comment,
            );

            $result = Db::getInstance()->insert('extraimagen_solicitud_cotizacion', $insert);

            $product = new Product($id_product);
            Logger::addLog("id_product => {$id_product}");
            $float_price = floatval($product->price);
            $price = $product->price;
            // $price = $product->getPrice();
            Logger::addLog($price);
            // Logger::addLog($price);
            // Logger::addLog($price);
            $total = $price * $qty;
            // Logger::addLog($total);

        }

        $this->context->smarty->assign([
            'cotizador_message' => Configuration::get('COTIZADOR_MESSAGE'),
            'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
            'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display')
        ]);

        if ($this->getCotizadorProductoEnabled($id_product)) {
            return $this->display(__FILE__, 'cotizador.tpl');
        }
        // return $this->display(__FILE__, 'cotizador.tpl');
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
                'prod_plazos' => $this->getCotizadorProductoPlazos($id_product),
            ]);

            return $this->display(__FILE__, 'admin_prod_form.tpl');
        }
    }

    private function getCotizadorProductoEnabled($id_product)
    {
        $request = "SELECT enabled FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";

        $is_enabled = Db::getInstance()->getValue($request);
        if (!$is_enabled or $is_enabled == 0) {
            return false;
        } else {
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
    private function getCotizadorProductoPlazos($id_product)
    {
        $request = "SELECT * FROM ps_extraimagen_producto_plazo a
            LEFT JOIN ps_extraimagen_plazo_entrega b ON a.id_plazo_entrega = b.id_plazo_entrega
            UNION
            SELECT * FROM ps_extraimagen_producto_plazo a
            RIGHT JOIN ps_extraimagen_plazo_entrega b ON a.id_plazo_entrega = b.id_plazo_entrega
            ORDER BY num_days;";

        $result = Db::getInstance()->executeS($request);
        if (!$result) {
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        } else {
            return $result;
        }
    }

    public function hookActionProductUpdate($params)
    {
        Logger::addLog("cotizador hookActionProductUpdate start: ");

        $params_id_product = (int)$params['id_product'];
        Logger::addLog("cotizador hookActionProductUpdate params: {$params_id_product} ");

        $allow_cotizador = (int)Tools::getValue('allow_cotizador');
        $min_qty = (int)Tools::getValue('min_qty', 1);

        $price_factor_1 = (int)Tools::getValue('price_factor_1');
        Logger::addLog("cotizador hookActionProductUpdate price_factor_1: {$price_factor_1} ");
        $allow_plazo_1 = Tools::getValue('allow_plazo_1');
        Logger::addLog("cotizador hookActionProductUpdate allow_plazo_1: {$allow_plazo_1} ");
        $allow_plazo_2 = Tools::getValue('allow_plazo_2');
        Logger::addLog("cotizador hookActionProductUpdate allow_plazo_2: {$allow_plazo_2} ");

        $this-> updateCotizadorProducto($params_id_product, $allow_cotizador, $min_qty);
    }

    public function updateCotizadorProducto($id_product, $enabled, $min_qty)
    {
        // Logger::addLog("allow_cotizador: {$enabled} ");

        $query = "SELECT count(id_cotizador_producto) FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";
        // Logger::addLog("query: {$query} ");

        $queryCount = (int)Db::getInstance()->getValue($query);
        if ($queryCount > 0) {
            $result = Db::getInstance()->update('extraimagen_cotizador_producto', [
                'enabled' => (int)$enabled,
                'min_qty' => (int)$min_qty,
            ], "id_product = {$id_product}", 1, true);
            if (!$result) {
                $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
            }
        } else {
            $result = Db::getInstance()->insert('extraimagen_cotizador_producto', [
                'id_product' => (int)$id_product,
                'enabled' => (int)$enabled,
                'min_qty' => (int)$min_qty,
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

}

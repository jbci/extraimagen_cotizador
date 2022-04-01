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
        $this->author = 'pedregalux jbci';
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
        Logger::addLog("Start cotizador install()");
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

        if ($this->getCotizadorProducto($id_product)) {
            return $this->display(__FILE__, 'cotizador.tpl');
        }
        // return $this->display(__FILE__, 'cotizador.tpl');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int)$params['id_product'];
        if (Validate::isLoadedObject($product = new Product($id_product))) {
            $checked = "";
            if ($this->getCotizadorProducto($id_product)) {
                $checked = "checked";
            }
            $this->context->smarty->assign([
                'allow_cotizador' => $this->getCotizadorProducto($id_product),
            ]);
            return $this->display(__FILE__, 'admin_prod_form.tpl');

        }
    }
    public function getCotizadorProducto($id_product)
    {
        $request = "SELECT enabled FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";

        $is_enabled = Db::getInstance()->getValue($request);
        Logger::addLog("is_enabled: {$is_enabled} ");
        if (!$is_enabled or $is_enabled == 0) {
            Logger::addLog("false or zero: {$is_enabled} ");
            return false;
        } else {
            Logger::addLog("true or one: {$is_enabled} ");
            return true;
        }
    }

    public function hookActionProductUpdate($params)
    {
        
        $params_id_product = (int)$params['id_product'];
        Logger::addLog("params: {$params_id_product} ");

        $allow_cotizador = (int)Tools::getValue('allow_cotizador');
        $this-> updateCotizadorProducto($params_id_product, $allow_cotizador);
    }

    public function updateCotizadorProducto($id_product, $enabled)
    {
        // Logger::addLog("allow_cotizador: {$enabled} ");

        $query = "SELECT count(id_cotizador_producto) FROM `" . _DB_PREFIX_ . "extraimagen_cotizador_producto` WHERE id_product = {$id_product};";
        // Logger::addLog("query: {$query} ");

        $queryCount = (int)Db::getInstance()->getValue($query);
        if ($queryCount > 0) {
            $result = Db::getInstance()->update('extraimagen_cotizador_producto', [
                'enabled' => (int)$enabled,
            ], "id_product = {$id_product}", 1, true);
            if (!$result) {
                $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
            }
        } else {
            $result = Db::getInstance()->insert('extraimagen_cotizador_producto', [
                'id_product' => (int)$id_product,
                'enabled' => (int)$enabled,
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

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

        // $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "cotizaciones_extraimagen`(
        // `id_cotizacion` INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        // `email` VARCHAR(128), `phone` VARCHAR(15), `id_product` INT(10), 
        // `qty` INT(11), `days` INT(11), `colors` INT(11), `comment` varchar(512), `allow` INT(1),
        // `file` VARCHAR(256), `datetime` DATETIME NOT NULL default CURRENT_TIMESTAMP)";

        // if (!$result = Db::getInstance()->Execute($sql))
        //     return false;

        // return (parent::install()
        //     && $this->registerHook('displayAfterProductThumbs')
        //     && $this->registerHook('displayAdminProductsExtra')
        //     && $this->registerHook('actionProductUpdate')
        //     && Configuration::updateValue('COTIZADOR_NAME', 'ExtraImagen')
        //     && Configuration::updateValue('COTIZADOR_MESSAGE', 'Este mensaje se mostrará en el cotizador y debe configurarse en el administrador')
        // );
    }

    //     CREATE TABLE IF NOT EXISTS `ps_cotizaciones_extraimagen`(
    //         `id_cotizacion` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    //         `email` VARCHAR(256), `phone` VARCHAR(10), `id_product` INT(11), 
    //         `qty` INT(11), `days` INT(11), `colors` INT(11), `comment` INT(11))
    // --DROP TABLE IF EXISTS ps_cotizaciones_extraimagen

    public function uninstall()
    {
        Logger::addLog(_DB_PREFIX_);

        $sql = "DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "cotizaciones_extraimagen`";

        if (!$result = Db::getInstance()->Execute($sql))
            return false;

        return (parent::uninstall()
            && Configuration::deleteByName('COTIZADOR_NAME')
            && Configuration::deleteByName('COTIZADOR_MESSAGE')
        );
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
        if (Tools::isSubmit('submit_cotizador')) {
            //form proccessing
            // Logger::addLog('Test log form processing 1 ');
            $email = Tools::getValue('email');
            // Logger::addLog($email);
            $phone = Tools::getValue('phone');
            // Logger::addLog($phone);
            $days = (int)Tools::getValue('days');
            // Logger::addLog($days);
            $qty = (int)Tools::getValue('qty');
            // Logger::addLog($qty);
            $colors = (int)Tools::getValue('colors');
            // Logger::addLog($colors);
            $comment = Tools::getValue('comment');
            // Logger::addLog($comment);
            $allow = (int)Tools::getValue('email_allow');
            // Logger::addLog($allow);
            // $file = Tools::getValue('file');
            // Logger::addLog($file);

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
                // 'date' => date('Y-m-d H:i:s'),
                'comment' => $comment,
            );

            $result = Db::getInstance()->insert('cotizaciones_extraimagen', $insert);

            $product = new Product($id_product);
            $price = floatval($product->price);
            // $price = $product->getPrice();
            Logger::addLog($price);
            Logger::addLog($price);
            Logger::addLog($price);
            $total = $price * $qty;
            // Logger::addLog($total);

        }

        $this->context->smarty->assign([
            'cotizador_message' => Configuration::get('COTIZADOR_MESSAGE'),
            'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
            'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display')
        ]);
        return $this->display(__FILE__, 'cotizador.tpl');
    }

    public function hookDisplayAdminProductsExtra($params)
    {

        if (Tools::isSubmit('submit_admin_cotizador')) {
            Logger::addLog("form submitted hookDisplayAdminProductsExtra");
        }
        dump($params);
        $product_id = (int)Tools::getValue('id_product');
        Logger::addLog("started hookDisplayAdminProductsExtra");
        Logger::addLog($params['id_product']);
        
        // foreach ($params as $clave => $valor) {
        //     // $array[3] se actualizará con cada valor de $array...
        //     Logger::addLog("{$clave} => {$valor} ");
        //     print_r($array);
        // }

        if (Validate::isLoadedObject($product = new Product((int)$params['id_product']))) {

        Logger::addLog("hookDisplayAdminProductsExtra inside if");
        // return $this->display(__FILE__, 'newfieldstut.tpl');
            // return $this->displayForm();
        }
        // $this->context->smarty->assign([
        //     'cotizador_message' => Configuration::get('COTIZADOR_MESSAGE'),
        //     'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
        //     'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display')
        // ]);
        return $this->display(__FILE__, 'admin_prod_form.tpl');
    }

    public function hookActionProductUpdate($params)
    {
        // get all languages
        // for each of them, store the new field
        $id_product = (int)Tools::getValue('id_product');
        Logger::addLog("hookActionProductUpdate started ");
        // Logger::addLog($params['id_product']);
        // foreach ($params as $clave => $valor) {
        //     // $array[3] se actualizará con cada valor de $array...
        //     Logger::addLog("{$clave} =>  ");
        //     $val = strval($valor);
        //     Logger::addLog("{$clave} => {$val} ");
        //     // print_r($array);
        // }
        // Logger::addLog($id_product);
        // $languages = Language::getLanguages(true);
        // foreach ($languages as $lang) {
        //     if(!Db::getInstance()->update('product_lang', array('custom_field'=> pSQL(Tools::getValue('custom_field_'.$lang['id_lang']))) ,'id_lang = ' . $lang['id_lang'] .' AND id_product = ' .$id_product ))
        //         $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        // }
    
    }
    /*

    public function hookDisplayHome($params)
    {
        $this->context->smarty->assign([
            'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
            'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display')
        ]);

        /*
        return $this->setTemplate('module:cotizador/views/templates/hook/cotizador.tpl');
         
        return $this->display(__FILE__, 'cotizador.tpl');
    }

    public function hookDisplayLeftColumn($params)
    {
        $this->context->smarty->assign([
            'cotizador_name' => Configuration::get('COTIZADOR_NAME'),
            'cotizador_link' => $this->context->link->getModuleLink('cotizador', 'display')
        ]);

        return $this->display(__FILE__, 'cotizador.tpl');
    }

    public function hookDisplayRightColumn($params)
    {
        return $this->hookDisplayLeftColumn($params);
    }
    */

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

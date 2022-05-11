<?php
// use Context;

require_once _PS_ROOT_DIR_.'/modules/cotizador/src/classes/solicitud_cotizacion.php';

class CotizacionAdminController extends ModuleAdminController {
    public function __construct(){
        parent::__construct();
        // Base
        $this->bootstrap = true; // use Bootstrap CSS
        $this->className = 'SolicitudCotizacion'; // SQL table name, will be prefixed with _DB_PREFIX_
        $this->table = 'extraimagen_solicitud_cotizacion'; // SQL table name, will be prefixed with _DB_PREFIX_
        $this->identifier = 'id_cotizacion'; // SQL column to be used as primary key
        //   $this->className = 'Pasta'; // PHP class name
        $this->allow_export = true; // allow export in CSV, XLS..
        // List records
        $this->_defaultOrderBy = 'a.id_cotizacion'; // the table alias is always `a`
        $this->_defaultOrderWay = 'DESC';
        $this->_select = 'a.id_cotizacion as `id`, a.id_cotizacion as `id_cotizacion`, a.replied as `replied`, a.email as `email`, a.phone as `phone`, pl.name as `prod_name`
        , a.id_product as `id_product`, a.quantity as `quantity` , a.id_plazo_entrega as `id_plazo_entrega` , a.id_forma_pago as `id_forma_pago` , a.datetime as `datetime`, a.file as `file`
        , plazo.description as `desc_plazo`, tipo_trabajo.description as `desc_tipo_trabajo`, forma_pago.description as `desc_forma_pago`, a.comment as `comment`
        ';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'product` prod ON (prod.id_product=a.id_product)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (prod.id_product=pl.id_product and prod.id_shop_default=pl.id_shop)
            LEFT JOIN `'._DB_PREFIX_.'extraimagen_plazo_entrega` plazo ON (a.id_plazo_entrega=plazo.id_plazo_entrega)
            LEFT JOIN `'._DB_PREFIX_.'extraimagen_tipo_trabajo` tipo_trabajo ON (a.id_tipo_trabajo=tipo_trabajo.id_tipo_trabajo)
            LEFT JOIN `'._DB_PREFIX_.'extraimagen_forma_pago` forma_pago ON (a.id_forma_pago=forma_pago.id_forma_pago)
            ';
        // $this->_join = '
        //     LEFT JOIN `'._DB_PREFIX_.'category` cat ON (cat.id_category=a.id_pasta_category)
        //     LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (cat.id_category=cl.id_category and cat.id_shop_default=cl.id_shop)
        // ';
        $this->fields_list = [
            'id' => [   'title' => 'ID',
                        'class' => 'fixed-width-xs',
                        'orderby' => true,
                        'search' => true,
                    ],
            'id_cotizacion' => [
                            'title' => $this->l('Completado'),
                            'class' => 'fixed-width-xs',
                            'align' =>'text-right',
                            'search' => false,
                            'callback' => 'viewMyButton',
                        ],
            'email' => ['title' => 'Email',
                        'class' => 'fixed-width-sm'],
            'phone' => ['title' => 'Teléfono',
                        'class' => 'fixed-width-sm'],
            'id_product' => [    'title' => 'ID Producto', 
                                'filter_key'=>'pl!name', 
                                'class' => 'fixed-width-xs',
                                'filer' => true,],
            'prod_name' => [    'title' => 'Producto', 
                                'filter_key'=>'pl!name', 
                                'filer' => true,],
            'quantity' => [  'title' => 'Cantidad',
                        'class' => 'fixed-width-xs'],
            // 'id_plazo_entrega' => [ 'title' => 'Plazo',
            //                 'class' => 'fixed-width-xs'],
            'desc_forma_pago' => [ 'title' => 'Forma de pago',
                                'class' => 'fixed-width-xs',
                                'search' => false,],
            'desc_plazo' => [ 'title' => 'Plazo',
                            'class' => 'fixed-width-xs',
                            'search' => false,],
            'desc_tipo_trabajo' => [ 'title' => 'Tipo Trabajo',
                            'class' => 'fixed-width-xs',
                            'search' => false,],
            'file' => [ 'title' => 'Archivo',
                            'class' => 'fixed-width-xs',
                            'search' => false,
                            'callback' => 'downloadButton',
                        ],

            'comment' => [ 'title' => 'Comentario',
                            'search' => false,],
            'datetime' => ['title' => 'Fecha','type'=>'datetime'],
        ];

        $this->addRowAction('markCompleted');

        $this->fields_form = [
        'legend' => [
            'title' => 'Cotización de trabajo Extraimagen',
            'icon' => 'icon-list-ul'
        ],
        'input' => [
            ['name'=>'info','type'=>'html','html_content'=>'<div class="alert alert-warning">Campos no modificables</div>'],
            ['name'=>'id_cotizacion','type'=>'text','label'=>'ID COTIZACIÓN'],
            ['name'=>'email','type'=>'text','label'=>'Email'],
            ['name'=>'phone','type'=>'text','label'=>'Telefono'],
            ['name'=>'prod_name','type'=>'text','label'=>'Producto', 'callback'=> "prod"],
            ['name'=>'id_product','type'=>'hidden','label'=>'id_product'],
            ['name'=>'quantity','type'=>'text','label'=>'Cantidad'],
            ['name'=>'id_plazo_entrega','type'=>'text','label'=>'Plazo'],
            ['name'=>'comment','type'=>'text','label'=>'Comentarios'],
            ['name'=>'file','type'=>'text','label'=>'Archivo'],
            ['name'=>'datetime','type'=>'datetime','label'=>'Fecha de solicitud'],

            ],
        ];

        
    }

    public function renderForm(){
        $this->context = Context::getContext();
        $this->context->controller = $this;
        $row =  $this->context;
        $obj = $this->loadObject(true);
        if (!($obj)) {
            return;
            }
        else {
            Logger::addLog("detail form");
            // Logger::addLog((string)$row);
            // var_dump($row);die;
        }
        $solicitud = $obj; // get invoice
        // Logger::addLog($solicitud->email);
        // $order = $invoice->getOrder(); // get order
        // $customer = $order->getCustomer(); // get customer
        // $currency = new Currency($order->id_currency); // get currency
        // add some info in an HTML string
        $info = '<div class="panel">';
        $info .= '<div class="panel-heading"><i class="icon-list-ul"></i> Datos de la solicitud </div>';
        // $info .= "Id Solicitud: {$solicitud->email}<br/>";
        // $info .= "Customer: {$customer->firstname} {$customer->lastname}<br/>";
        // $info .= "Total_paid : ".Tools::displayPrice($order->total_paid, $currency)."<br/>";
        // $info .= "Get total paid : ".Tools::displayPrice($order->getTotalPaid(), $currency)."<br/>";
        // $info .= "Payment: {$order->payment}<br/>";
        // $info .= "Order state : {$order->getCurrentOrderState()->name[$this->context->language->id]}";
        $info .= '</div>';
        // push the HTML to $this->content
        $this->content .= $info;
        return parent::renderForm();
      }

    public function viewMyButton($value, $form)
    {
        // Logger::addLog("viewMyButton => started");
        // Logger::addLog($value);
        // Logger::addLog($form);
        // var_dump($form);die;
        if ($form["replied"]== 1){
            return '<span style="background-color : #00FF00; color : #000000; border-radius : 2px/2px"> COMPLETADO </span>';
        } else {
            return '<span style="background-color : #FF0000; color : #000000; border-radius : 2px/2px"> PENDIENTE </span>';
        }
        
    }

    public function downloadButton($value, $form)
    {
        // Logger::addLog("viewMyButton => started");
        // Logger::addLog($value);
        // Logger::addLog($form);
        // var_dump($form);die;
        if ($form["file"]== ""){
            return '';
        } else {
            return '<a href="http://5.161.82.244/prestashop/upload/2022_05_07_16_26_30_choose.png" target="_blank"><span style="background-color : #FFFFFF; color : #000000; border-radius : 2px/2px"> Descargar </span><a>';
        }
        
    }

    public function initProcess()
    {
        // Logger::addLog("initProcess => started");
        // var_dump($this);die;
        if (Tools::getIsset('markCompleted'.$this->table))
        {
            // Logger::addLog("initProcess => inside if");
            $this->action = 'markCompleted';
        }
        parent::initProcess();
    }

    public function displayMarkCompletedLink($token = null, $id, $name = null)
    {
        // Logger::addLog("displayMarkCompletedLink => started");
        // Logger::addLog("displayMarkCompletedLink => token: {$token}");
        // Logger::addLog("displayMarkCompletedLink => id: {$id}");
        // Logger::addLog("displayMarkCompletedLink => name: {$name}");

        $link = Context::getContext()->link;

        $symfonyUrl = $link->getAdminLink('CotizacionAdmin', true, [
            // 'route' => 'admin_product_unit_action',            
            'id_cotizacion' => $id,
            'id' => $id,],
            ['action' => 'markCompleted',
            'id_cotizacion' => $id,]
        );

        // Logger::addLog("displayMarkCompletedLink => symfonyUrl: {$symfonyUrl}");
        return '<div> <span class="btn-group-action">
                    <span class="btn-group">
                        <a class="btn btn-default" href="'.$symfonyUrl.'"><i class="icon-check"> </i>Marcar Completado&nbsp;
                        </a>
                    </span>
                </span> </div>';
    }  
    
    public function processMarkCompleted()
    {
        // Logger::addLog("processMarkCompleted => started");
        // var_dump(Context::getContext());die;
        // var_dump(Context::getContext()->queryString);die;
        $id_cotizacion = Tools::getValue('id_cotizacion');
        $id_employee = Context::getContext()->employee->id;
        if ($id_cotizacion) {
            // Logger::addLog("processMarkCompleted => id_cotizacion: {$id_cotizacion}");
            // Logger::addLog("processMarkCompleted => id_employee: {$id_employee}");
            $this->updateSolicitud($id_cotizacion);
        }
    }

    private function updateSolicitud($id_solicitud)
    {
        // Logger::addLog("updateSolicitud:  ");

        $result = Db::getInstance()->update('extraimagen_solicitud_cotizacion', [
            'replied' => 1,
        ], "id_cotizacion = {$id_solicitud}", 1, true);
        if (!$result) {
            $this->context->controller->_errors[] = Tools::displayError('Error: ').mysql_error();
        }
    }
}

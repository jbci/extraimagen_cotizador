<?php
// require_once _PS_ROOT_DIR_.'/override/classes/my_dir/Pasta.php';

class CotizacionAdminController extends ModuleAdminController {
    public function __construct(){
        parent::__construct();
        // Base
        $this->bootstrap = true; // use Bootstrap CSS
        $this->table = 'extraimagen_solicitud_cotizacion'; // SQL table name, will be prefixed with _DB_PREFIX_
        $this->identifier = 'id_cotizacion'; // SQL column to be used as primary key
        //   $this->className = 'Pasta'; // PHP class name
        $this->allow_export = true; // allow export in CSV, XLS..
        // List records
        $this->_defaultOrderBy = 'a.id_cotizacion'; // the table alias is always `a`
        $this->_defaultOrderWay = 'DESC';
        $this->_select = 'a.id_cotizacion as `id`, a.id_cotizacion as `id_cotizacion`, a.replied as `replied`, a.email as `email`, a.phone as `phone`, pl.name as `prod_name`
        , a.id_product as `id_product`, a.qty as `qty` , a.days as `days` , a.datetime as `datetime`';
        $this->_join = '
            LEFT JOIN `'._DB_PREFIX_.'product` prod ON (prod.id_product=a.id_product)
            LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (prod.id_product=pl.id_product and prod.id_shop_default=pl.id_shop)
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
                            'title' => $this->l('Completar'),
                            'class' => 'fixed-width-xs',
                            'align' =>'text-right',
                            'search' => false,
                            'callback' => 'viewMyButton',
                        ],
            'email' => ['title' => 'Email',
                        'class' => 'fixed-width-sm'],
            'phone' => ['title' => 'Teléfono',
                        'class' => 'fixed-width-sm'],
            'prod_name' => [    'title' => 'Producto', 
                                'filter_key'=>'pl!name', 
                                'filer' => true,],
            'qty' => [  'title' => 'Cantidad',
                        'class' => 'fixed-width-xs'],
            'num_days' => [ 'title' => 'Plazo',
                            'class' => 'fixed-width-xs'],
            // 'pastaName' => ['title' => 'Name', 'filter_key'=>'a!name'], // filter_key mandatory because "name" is ambiguous for SQL
            // 'categoryName' => ['title' => 'Category', 'filter_key'=>'cl!name'], // filter_key mandatory because JOIN
            'datetime' => ['title' => 'Fecha','type'=>'datetime'],
        ];
        // Read & update record
        $this->addRowAction('details');
        // $this->addRowAction('edit');
        $this->addRowAction('markCompleted');
        // $categories = Category::getCategories($this->context->language->id, $active=true, $order=false); // [0=>[id_category=>X,name=>Y]..]

        $this->fields_form = [
        'legend' => [
            'title' => 'Cotización de trabajo Extraimagen',
            'icon' => 'icon-list-ul'
        ],
        'input' => [
            ['name'=>'info','type'=>'html','html_content'=>'<div class="alert alert-warning">Campos no modificables</div>'],
            ['name'=>'replied','type'=>'text','label'=>'Contestado'],
            ['name'=>'email','type'=>'text','label'=>'Email'],
            ['name'=>'phone','type'=>'text','label'=>'Telefono'],
            ['name'=>'prod_name','type'=>'text','label'=>'Producto'],
            ['name'=>'id_product','type'=>'hidden','label'=>'id_product'],
            ['name'=>'qty','type'=>'text','label'=>'Cantidad'],
            ['name'=>'days','type'=>'text','label'=>'Plazo'],
            ['name'=>'comments','type'=>'text','label'=>'Comentarios'],
            ['name'=>'created','type'=>'datetime','label'=>'Fecha de solicitud'],

            // ['name'=>'id_pasta_category','label'=>'Category','type'=>'select','required'=>true,'class'=>'select2',
            // 'options'=>[ 'query'=>$categories,
            //     'id'=>'id_category', // use the key "id_category" as the <option> value
            //     'name'=> 'name', // use the key "name" as the <option> title
            // ]],
        ],
        // 'submit' => [
        //     'title' => $this->trans('Save', [], 'Admin.Actions'),
        // ]
        ];
    }

    public static function my_visibility_function($my_visibility_function)
    {
        // Logger::addLog("my_visibility_function => started");
        // Logger::addLog($my_visibility_function);

        if ($my_visibility_function == 1) {
            return '<span style="background-color : #00FF00; color : #ffffff; border-radius : 4px/4px"> SI </span>';
        } else {
            return '<span style="background-color : #FF0000; color : #ffffff; border-radius : 4px/4px"> NO </span>';
        }
            
    }

    public function viewMyButton($value, $form)
    {
        Logger::addLog("viewMyButton => started");
        Logger::addLog($value);
        // Logger::addLog($form);
        // var_dump($form);die;
        if ($form["replied"]== 1){
            return '<span style="background-color : #00FF00; color : #000000; border-radius : 4px/4px"> COMPLETADO </span>';
        } else {
            return '<span class="btn-group-action">
                    <span class="btn-group">
                        <a class="btn btn-default" href="'.$value.'"><i class="icon-check"></i>&nbsp;
                        </a>
                    </span>
                </span>';
        }
        
    }

    public function initProcess()
    {
        Logger::addLog("initProcess => started");
        // var_dump($this);die;
        if (Tools::getIsset('markCompleted'.$this->table))
        {
            Logger::addLog("initProcess => inside if");
            $this->action = 'markCompleted';
        }
        parent::initProcess();
    }

    public function displayMarkCompletedLink($token = null, $id, $name = null)
    {
        Logger::addLog("displayMarkCompletedLink => started");
        // Do your button processing here
    }  
    
}

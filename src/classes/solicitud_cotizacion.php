<?php

class SolicitudCotizacion extends ObjectModel
{
    public $id_cotizacion;
    public $email;
    public $phone;
    public $id_product;
    public $quantity;
    public $id_plazo_entrega;
    public $id_tipo_trabajo;
    public $id_forma_pago;
    public $comment;
    public $allow;
    public $replied;
    public $file;
    public $datetime;

    /**
    * @see ObjectModel::$definition
    */
    public static $definition = array(
        'table'     => 'extraimagen_solicitud_cotizacion',
        'primary'   => 'id_cotizacion',
        'multilang' => false,
        'fields'    => array(
            'email'      => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'size' => 100, 'required' => true),
            'phone'      => array('type' => self::TYPE_STRING, 'validate' => 'isPhoneNumber', 'size' => 100, 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'quantity' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_plazo_entrega' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_tipo_trabajo' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_forma_pago' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'comment'      => array('type' => self::TYPE_STRING, 'validate' => 'isMessage', 'size' => 100, 'required' => false),
            'allow' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'replied' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => false),
            'file'      => array('type' => self::TYPE_STRING, 'validate' => 'isFileName', 'size' => 100, 'required' => false),
            'datetime'      => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'size' => 100, 'required' => false),

        ),
    );
}
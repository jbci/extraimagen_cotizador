<?php
/**
 * <ModuleClassName> => Cotizador
 * <FileName> => execute.php
 * Format expected: <ModuleClassName><FileName>ModuleFrontController
 */
class CotizadorExecuteModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
    // In the template, we need the vars paymentId & paymentStatus to be defined
    $this->context->smarty->assign(
        array(
        'paymentId' => Tools::getValue('id'), // Retrieved from GET vars
        'paymentStatus' => "pending",
        ));

    // Will use the file modules/cheque/views/templates/front/validation.tpl
    $this->setTemplate('module:cotizador/views/templates/hook/cotizador.tpl');
    }
}
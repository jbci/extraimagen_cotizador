<!-- Block tab1 -->
  <div id="tab2" class="hidden_tab">
      {include file="./producto_cotizacion.tpl"}
    <p>
      Que cantidad desea cotizar?(* Requerido)
    </p>
    <p>
      <input type="number" id="quantity" name="quantity" value="{$min_qty}" required>(* Mínimo {$min_qty})
      <span class="required_field" id="qty_span"> </span>
    </p>
  </div>
<!-- /Block cotizador -->

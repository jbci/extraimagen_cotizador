<!-- Block tab1 -->
  <div id="tab6" class="hidden_tab">
      {include file="./producto_cotizacion.tpl"}
    <p>
      El precio para las siguientes características:
      <ul>
        <li>Producto: {$requested_product_name}</li>
        <li>Cantidad: <span id="qty_span"> </span> </li>
        <li>Plazo: <span id="time_span"> </span>  </li>
      </ul>
    </p>
    <p>
      Es de: $<span id="price_span"> </span>CLP
    </p>
  </div>
<!-- /Block cotizador -->

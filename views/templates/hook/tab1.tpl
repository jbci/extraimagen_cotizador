<!-- Block tab1 -->
  <div id="tab1" class="hidden_tab">
    
    {include file="./producto_cotizacion.tpl"}

    <p>
      Teléfono (*Requerido): <input class="text" name="phone" id="phone" class="form-control" required></input> 
      <span class="required_field" id="ph_span"> </span>
    </p>
    <p>
      Email (*Requerido): <input class="text" name="email" id="email" class="form-control" value="{$customer['email']}" required></input> 
      <span class="required_field" id="email_span"> </span>
    </p>
  </div>
<!-- /Block cotizador -->

<!-- Block tab1 -->
  <div id="tab5" class="hidden_tab">
    <p>
      Forma de pago:
    </p>
    <select id="extrai_forma_pago" name="extrai_forma_pago">
      {foreach from=$formas_pago item=f_pago}
        {if $f_pago['enabled'] == true}
          {$disabled = ""}
          {else}
          {$disabled = "disabled"}
        {/if}
        <option value="{$f_pago['id_forma_pago']}" {$disabled}>{$f_pago["description"]}</option>
        
      {/foreach}
    </select>
  </div>
<!-- /Block cotizador -->

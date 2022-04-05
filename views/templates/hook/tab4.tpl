<!-- Block tab1 -->
  <div id="tab4" class="hidden_tab">
      {include file="./producto_cotizacion.tpl"}
    <p>
      Que plazo (días habiles) requiere:
    </p>
    <select id="work_days" name="extrai_work_type">
      {foreach from=$prod_plazos item=plazo}
        {if $plazo['enabled'] == true}
          {$disabled = ""}
          {else}
          {$disabled = "disabled"}
        {/if}
        <option value="{$plazo['id_plazo_entrega']}" {$disabled}>{$plazo["description"]}({$plazo["num_days"]} hábiles)</option>
        
      {/foreach}
    </select>
  </div>
<!-- /Block cotizador -->

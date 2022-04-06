<!-- Block tab1 -->
  <div id="tab4" class="hidden_tab">
    <p>
      Que plazo (días habiles) requiere:
    </p>
    <select id="extrai_work_days" name="extrai_work_days">
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

<!-- Block tab1 -->
  <div id="tab3" class="hidden_tab">
    <p>
      Que tipo de trabajo requiere:
    </p>
    <select id="extrai_work_type" name="extrai_work_type">
      {foreach from=$tipo_trabajos item=tipo}
        {if $tipo['enabled'] == true}
          {$disabled = ""}
          {else}
          {$disabled = "disabled"}
        {/if}
        <option value="{$tipo['id_tipo_trabajo']}" {$disabled}>{$tipo["description"]}</option> 
        {$tipo['id_tipo_trabajo']}
      {/foreach}
    </select>
  </div>
<!-- /Block cotizador -->

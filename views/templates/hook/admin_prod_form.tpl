<div id="cotizador_block_admin" class="block">
{* <form action="" method="post" id="cotizador-form"> *}
    <div class="form-group">
        <label for="email_allow">
            <h2>{l s='Habilitar cotizador para este producto:' mod='cotizador'}</h2>
        </label>
        <input type="checkbox" name="allow_cotizador" id="allow_cotizador" class="form-control" value="1" {$allow_cotizador}>
    </div>
    <div class="form-group">
        <label for="base_price">
            <h2>{l s='Precio Base:' mod='cotizador'}</h2>
        </label>
        <input name="base_price" id="base_price" class="form-control" value="{$base_price}" required>
    </div>
    <div class="form-group">
        <label for="min_qty">
            <h2>{l s='Cantidad mínima:' mod='cotizador'}</h2>
        </label>
        <input name="min_qty" id="min_qty" class="form-control" value="{$min_qty}" required>
    </div>
        <div class="form-group">
            <label for="prod_plazos">
                <h2>{l s='Plazos Perimitidos:' mod='cotizador'}</h2>
            </label>
            <div class="row">
                <table>
                    <thead>
                        <tr>
                            <td>
                                Habilitado
                            </td>
                            <td>
                                
                            </td>
                            <td>
                                Factor Incremento
                            </td>
                            <td>
                                Cantidad Máxima
                            </td>
                        </tr>
                    </th>
                    {foreach from=$prod_plazos item=plazo}
                        <tr>
                            {$id_plazo_entrega = "_{$plazo["id_plazo_entrega"]}"}
                            <td>
                            {if $plazo["enabled"] == 1}
                                {$checked='checked'}
                            {else}
                                {$checked=''}
                            {/if}
                                <input type="checkbox" name="allow_plazo{$id_plazo_entrega}" id="allow_plazo{$id_plazo_entrega}" {$checked}>
                            </td>
                            <td>
                                <label for="prod_plazos">{$plazo["description"]}({$plazo["num_days"]} hábiles)</label>
                            </td>
                            <td>
                                <input name="price_factor{$id_plazo_entrega}" id="price_factor{$id_plazo_entrega}" class="form-control" value="{$plazo["price_factor"]}" required>
                            </td>
                            <td>
                                <input name="max_qty{$id_plazo_entrega}" id="max_qty{$id_plazo_entrega}" class="form-control" value="{$plazo["max_qty"]}" required>
                            </td>
                        </tr>
                    {/foreach}
                </table>
            </div>
        </div>
    <div class="form-group">
        <label for="accept_changes">
            <h2>{l s='Aceptar cambios:' mod='cotizador'}</h2>
        </label>
        <input type="checkbox" name="accept" id="accept" class="form-control" value="1" >
    </div>
        <button type="submit" class="btn btn-default button-medium" name="submit_admin_cotizador" id="submit_admin_cotizador">
            <span>Guardar <i class="icon-chevron-right right"></i></span>
        </button>
{* </form> *}
</div>
<h2>{l s='Cotizador Extraimagen' mod='cotizador'}</h4>
<div class="separation"></div>
<table>
    <tr>
        <td class="col-left">
            <label>{l s='Mostrar Cotizador para el producto:'}</label>
        </td>
        <td>
            <input type="checkbox" name="allow_cotizador" id="allow_cotizador" class="form-control">
        </td>
        <td>
            <label>{l s='Mostrar Cotizador para el producto:'}</label>
        </td>
    </tr>

<form action="" method="post" id="comment-form">
    <div class="form-group">
        <label for="email_allow">Habilitar cotizador para este producto:</label>
        <input type="checkbox" name="allow_cotizador" id="allow_cotizador" class="form-control" value="1" {$allow_cotizador}>
    </div>
    <div class="form-group">
        <label for="email">Email:</label>
        <input name="email" id="email" class="form-control" required></input>
    </div>
    <div class="form-group">
        <label for="phone">Teléfono:</label>
        <input name="phone" id="phone" class="form-control" required></input>
    </div>
    <div class="form-group">
        <label for="days">Plazo en días:</label>
        <input name="days" id="days" class="form-control" required></input>
    </div>
    <div class="form-group">
        <label for="qty">Cantidad:</label>
        <input name="qty" id="qty" class="form-control" required></input>
    </div>
    <button type="submit" class="btn btn-default button-medium" name="submit_admin_cotizador" id="submit_admin_cotizador">
        <span>Guardar <i class="icon-chevron-right right"></i></span>
    </button>
</form>
</table>
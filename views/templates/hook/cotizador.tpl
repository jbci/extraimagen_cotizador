<!-- Block cotizador -->
<div id="cotizador_block_home" class="block">
  <h4>{l s='Cotize aquí!' mod='cotizador'}</h4>
  <div class="block_content">
    <p>
           {if isset($cotizador_message) && $cotizador_message}
               {$cotizador_message}
           {/if}
    </p>
    <form action="" method="post" id="comment-form">
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
        <input type="hidden" value="" name="product_id">
        <div class="form-group">
            <label for="colors">Colores</label>
            <div class="row">
                <div class="col-xs-4">
                    <select name="colors" id="colors" class="form-control" required>
                        <option value="0">--Elija--</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">Mas</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="comment">Comentarios:</label>
            <textarea name="comment" id="comment" rows="3" class="form-control" required></textarea>
        </div>
        <div class="form-group">
            <label for="file">Fichero: </label>
            <input type="file" name="file" id="fileUpload" />
        </div>
        <div class="form-group">
        <label for="email_allow">Deseo recibir una cotización por email:</label>
            <input type="checkbox" name="email_allow" id="email_allow" class="form-control" value="1">
        </div>
        <button type="submit" class="btn btn-default button-medium" name="submit_cotizador" id="submit_cotizador">
            <span>Cotizar <i class="icon-chevron-right right"></i></span>
        </button>
    </form>
  </div>
</div>
<!-- /Block cotizador -->
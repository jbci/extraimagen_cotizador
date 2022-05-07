<!-- Block tab1 -->
  <div id="tab1" class="hidden_tab">
    {block name='product_images'}
      <div class="js-qv-mask mask">
        <ul class="product-images js-qv-product-images">
          {foreach from=$product.images item=image}
            <li class="thumb-container js-thumb-container">
              <img
                class="thumb js-thumb {if $image.id_image == $product.default_image.id_image} selected js-thumb-selected {/if}"
                data-image-medium-src="{$image.bySize.medium_default.url}"
                data-image-large-src="{$image.bySize.large_default.url}"
                src="{$image.bySize.small_default.url}"
                {if !empty($image.legend)}
                  alt="{$image.legend}"
                  title="{$image.legend}"
                {else}
                  alt="{$product.name}"
                {/if}
                loading="lazy"
                width="{$product.default_image.bySize.small_default.width}"
                height="{$product.default_image.bySize.small_default.height}"
              >
            </li>
          {/foreach}
        </ul>
      </div>
    {/block}
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

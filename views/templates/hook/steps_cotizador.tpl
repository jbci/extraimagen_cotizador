<!-- Block cotizador -->

<div id="cotizador_block_home" class="block">
  <style>
/* Popup p_container - can be anything you want */
.popup {
  position: relative;
  display: inline-block;
  cursor: pointer;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* The actual popup */
.popup .popuptext {
  visibility: hidden;
  {* width: 90%;
  height: 80%;
  background-color: #555;
  color: #fff;
  text-align: center;
  border-radius: 6px;
  padding: 8px 0;
  position: absolute;
  z-index: 1;
  bottom: 125%;
  left: 50%;
  margin-left: -80px; *}
}

{* /* Popup arrow */
.popup .popuptext::after {
  content: "";
  position: absolute;
  top: 100%;
  left: 50%;
  margin-left: -5px;
  border-width: 5px;
  border-style: solid;
  border-color: #555 transparent transparent transparent;
} *}

/* Toggle this class - hide and show the popup */
.popup .show {
  visibility: visible;
  -webkit-animation: fadeIn 1s;
  animation: fadeIn 1s;
}

.card {
    z-index: 0;
    background-color: #ECEFF1;
    padding-bottom: 20px;
    margin-top: 90px;
    margin-bottom: 90px;
    border-radius: 10px
}

.top {
    padding-top: 40px;
    padding-left: 13% !important;
    padding-right: 13% !important
}

#progressbar {
    margin-bottom: 30px;
    overflow: hidden;
    color: #455A64;
    padding-left: 0px;
    margin-top: 30px
}

#progressbar li {
    list-style-type: none;
    font-size: 13px;
    width: 14%;
    float: left;
    position: relative;
    font-weight: 400
}

#progressbar .step0:before {
    font-family: FontAwesome;
    content: "\f10c";
    color: #fff
}

#progressbar li:before {
    width: 40px;
    height: 40px;
    line-height: 45px;
    display: block;
    font-size: 20px;
    background: #C5CAE9;
    border-radius: 50%;
    margin: auto;
    padding: 0px
}

#progressbar li:after {
    content: '';
    width: 100%;
    height: 12px;
    background: #C5CAE9;
    position: absolute;
    left: 0;
    top: 16px;
    z-index: -1
}
 
#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px;
    position: absolute;
    left: -50%
}

#progressbar li:nth-child(2):after,
#progressbar li:nth-child(3):after,
#progressbar li:nth-child(4):after,
#progressbar li:nth-child(5):after,
#progressbar li:nth-child(6):after
#progressbar li:nth-child(7):after  {
    left: -50%
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px;
    position: absolute;
    left: 50%
}

#progressbar li:last-child:after {
    border-top-right-radius: 10px;
    border-bottom-right-radius: 10px
}

#progressbar li:first-child:after {
    border-top-left-radius: 10px;
    border-bottom-left-radius: 10px
} 

#progressbar li.active:before,
#progressbar li.active:after {
    background: #651FFF
}

#progressbar li.active:before {
    font-family: FontAwesome;
    content: "\f00c"
}

.icon {
    width: 60px;
    height: 60px;
    margin-right: 15px
}

.icon-content {
    padding-bottom: 20px
}

.hidden_tab {
  visibility: hidden;
  height: 0px;
}

.visible_tab {
  visibility: visible;
  margin-left: 50px;
}

.buttons_tab {
  float: right;
  margin-right: 50px;
}

.text_area {
  width: 90%
}

.required_field {
  color: red;
}
</style>



<div class="popup" >
  {if $customer_logged == true}
    <h2 onclick="openPopup()">Cotice aquí</h2>
  {else}
    <h2>Debe estar registrado para cotizar</h2>
  {/if}
  
  {* <div class="popuptext" >
    Click me to toggle the popup!
  </div> *}
  <div class="popuptext" >
  <div class="container px-1 px-md-4 py-5 mx-auto" id="cotizadorPopup">
    <div class="card">
        <div class="row d-flex justify-content-between px-3 top">
            <div class="d-flex">
            </div>
            <div class="d-flex flex-column text-sm-right" onclick="closePopup()">
                <p class="mb-0">Cerrar Cotizador</p>
                <i class="bi bi-x-square-fill"></i>
            </div>
        </div> <!-- Add class 'active' to progress -->
        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <ul id="progressbar" class="text-center">
                    <li onclick="gotoStep(1)" id="step_1" class="step0 active"></li>
                    <li onclick="gotoStep(2)" id="step_2" class="step0"></li>
                    <li onclick="gotoStep(3)" id="step_3" class="step0"></li>
                    <li onclick="gotoStep(4)" id="step_4" class="step0"></li>
                    <li onclick="gotoStep(5)" id="step_5" class="step0"></li>
                    <li onclick="gotoStep(6)" id="step_6" class="step0"></li>
                    <li onclick="gotoStep(7)" id="step_7" class="step0"></li>
                </ul>
            </div>
        </div>
        <form action="" method="post" id="comment-form">
          <div class="row d-flex justify-content-right">
            <div class="col-3">
              {include file="./tab1.tpl"}
              {include file="./tab2.tpl"}
              {include file="./tab3.tpl"}
              {include file="./tab4.tpl"}
              {include file="./tab5.tpl"}
              {include file="./tab6.tpl"}
              {include file="./tab7.tpl"}
            </div>
          </div>
          <div class="row d-flex flex-row align-items-end">
            <div class="row d-flex flex-row align-items-end buttons_tab">
              <button type="button" id="btn-next" class="btn btn-outline-primary float-right" onclick="nextStep()">Siguiente</button>
            </div>
          </div>
          <div class="row d-flex flex-row align-items-end">
            <div class="row d-flex flex-row align-items-end buttons_tab">
              <button type="submit"  id="btn-submit" class="btn btn-outline-danger float-right" name="submit_cotizador" id="submit_cotizador">
                <span>Enviar <i class="icon-chevron-right right"></i></span>
              </button>
            </div>
          </div>
        </form>
    </div>
  </div>
  </div>
</div>

<script>

function eachStep(step){
  var thisStep = new Object();
  thisStep.i = step;
  thisStep.id = 'step_' + step;
  thisStep.active =  false;
  if (step <= currentStep) {
    thisStep.active = true
  }
  stepStates.push(thisStep)
}

var popupIsOpen = false;
// When the user clicks on div, open the popup
var stepStates = []
var steps = [1,2,3,4,5,6,7]
steps.forEach(s =>  eachStep(s))
var currentStep = 1;

function openPopup() {
  var popup = document.getElementById("cotizadorPopup");
  if (popupIsOpen == false) {
    popup.classList.toggle("show");
    popupIsOpen = true;
  } else {
    popup.classList.toggle("show");
    popupIsOpen = false;
  }

  updateProgress()
}

function closePopup() {
  var popup = document.getElementById("cotizadorPopup");
  if (popupIsOpen == true) {
    popup.classList.toggle("show");
    popupIsOpen = false;
  }
}

function eachState(s) {
  var li = document.getElementById(s.id);
  if (s.active) {
    li.classList.add("active");
  }
}

function updateProgress() {
  if (popupIsOpen) {
    stepStates.forEach(s =>  eachState(s))

    var visibleTabs = document.getElementsByClassName("visible_tab");
    console.log(visibleTabs)

    if (visibleTabs.length > 0) {
      visibleTabs[0].classList.add("hidden_tab");
      visibleTabs[0].classList.remove("visible_tab");
    }

    var tab = document.getElementById("tab"+currentStep);
    console.log(tab)
    tab.classList.remove("hidden_tab");
    tab.classList.add("visible_tab");

    var btn = document.getElementById("btn-next");
    var submit = document.getElementById("btn-submit");

    if (currentStep == 6) {
      btn.hidden=true
      submit.hidden=false
      calcPrice()
    }
    if (currentStep == 7) {
      btn.disabled = true;
    } else {
      btn.disabled = false;
    }
    
  }
}

function updateEachState(s) {
  if (s.i <= currentStep) {
    s.active = true
  }
}


function validateCurrentStep() {
  if (currentStep == 1) {
    if (document.getElementById('phone').value == ""){
      var ph_span = document.getElementById('ph_span')
      ph_span.textContent = "Campo requerido"
      return false
    } 
  }

  if (currentStep == 2) {
    if (document.getElementById('quantity').value == ""){
      var ph_span = document.getElementById('qty_span')
      ph_span.textContent = "Campo requerido"
      return false
    } 
    if (parseInt(document.getElementById('quantity').value) < {$min_qty}){
      var ph_span = document.getElementById('qty_span')
      ph_span.textContent = "Mínimo {$min_qty}"
      return false
    } 
  }
  return true
}
function nextStep() {
  if (validateCurrentStep()) {
    currentStep = currentStep + 1;
    stepStates.forEach(s =>  updateEachState(s))
    updateProgress()
  }
}

function gotoStep(s) {
  currentStep = s
  updateProgress()
}

function calcPrice() {
  var base_price = Number({$base_price});
  console.log(base_price)
  var qty = Number(document.getElementById('quantity').value);
  console.log(qty)
  var select = document.getElementById('work_days')
  var plazo_id = select.selectedOptions[0].value
  console.log(plazo_id)
  var prod_plazos_js = new Object();
  {foreach from=$prod_plazos item=plazo}
    var plazo{$plazo['id_plazo_entrega']} = new Object();
    plazo{$plazo['id_plazo_entrega']}.id = {$plazo['id_plazo_entrega']};
    plazo{$plazo['id_plazo_entrega']}.enabled = {$plazo['enabled']};
    plazo{$plazo['id_plazo_entrega']}.description = "{$plazo["description"]}";
    plazo{$plazo['id_plazo_entrega']}.num_days =  {$plazo["num_days"]};
    plazo{$plazo['id_plazo_entrega']}.price_factor =  {$plazo["price_factor"]};
    prod_plazos_js["{$plazo['id_plazo_entrega']}"] = plazo{$plazo['id_plazo_entrega']};        
  {/foreach}
  console.log(prod_plazos_js)
  var days_price_factor = Number(prod_plazos_js[plazo_id.toString()].price_factor);
  console.log("precio calculado")
  console.log(days_price_factor * qty * base_price)

  var priceSpan = document.getElementById("price_span")
  priceSpan.textContent = (days_price_factor * qty * base_price).toString()
  var timeSpan = document.getElementById("time_span")
  timeSpan.textContent = prod_plazos_js[plazo_id.toString()].description
  var qtySpan = document.getElementById("qty_span")
  qtySpan.textContent = qty.toString()

}
var submit = document.getElementById("btn-submit");
submit.hidden = true;
</script>
{* {$prod_plazos} *}
</div>
<!-- /Block cotizador -->

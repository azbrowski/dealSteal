<div class="add-promotion-container">
	<h1>Dodaj nową promocję</h1>
	<span class="note">Postaraj się podać jak najwięcej informacji. Im więcej wiadomo na temat produktu tym łatwiej podjąć decyzję w sprawie zakupu.</span>
	<hr>

	<?php if(isset($err_msg)){ ?>
		<div class="error-alert-box alert-box">
			<span class="close"><i class="fa fa-times" aria-hidden="true"></i></span>
			<p><?php echo $err_msg; ?></p>
		</div>
		<script>
			$( ".close" ).click(function() {
				$( ".alert-box" ).remove();
			});
		</script>
	<?php } ?>
	
	<form id="addPromotionForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
		<div class="form-group">
			<label for="url">Adres url</label>
			<input type="text" id="url" class="long" name="url" required>
		</div>
		
		<div class="form-group">
			<label for="title">Treść promocji</label>
			<input type="text" id="title" class="long" name="title" required>
		</div>
		
		<div class="form-group">		
			<label for="dateExpiration">Data zakończenia promocji</label>
			<input type="date" id="dateExpiration" name="date-expiration"  min="<?php echo date("Y-m-d"); ?>" required>
		</div>
		
		<hr>
		
		<div class="form-radiobox">	
			<p>Typ promocji</p>
			<label for="radFirst">
				<input type="radio" id="radFirst" name="promotion-type" value="first" checked>
				Pojedynczy produkt
			</label>
			<label for="radSecond">
				<input type="radio" id="radSecond" name="promotion-type" value="second">
				Wiecej niz jeden produkt
				<span class="note">Należy podać przedział cenowy.</span>
			</label>
			<label for="radThird">
				<input type="radio" id="radThird" name="promotion-type" value="third">
				100% przecena
			</label>
		</div>
		
		<hr>
		
		<div class="form-pricebox">
			<label for="checkOld">
				<input type="checkbox" id="checkOld" name="old-price" value="first">
				Uwzględnij starą cenę
				<span class="note">(opcjonalne)</span>
			</label>
			<div>
				<span class="more">Od</span>
				<input type="text" id="oldPriceLow" class="price" name="old-price-low" required>
				<span class="more">do</span>
				<input type="text" id="oldPriceHigh" class="price" name="old-price-high" required>
			</div>
		</div>
		
		<div class="form-pricebox">
			<p>Cena po przecenie</p>
			<div>
				<span class="more">Od</span>
				<input type="text" id="newPriceLow" class="price" name="new-price-low" required>
				<span class="more">do</span>
				<input type="text" id="newPriceHigh" class="price" name="new-price-high" required>
				
				<select name="currency" class="currency">
					<option value="PLN">PLN</option>
					<option value="EUR">EUR</option>
					<option value="USD">USD</option>
				</select> 
				
			</div>
			
			<hr>
		</div>
		
		<button class="btn" type="submit" name="submit">Wyślij</button>
	</form>
</div>

<script type="text/javascript">
    $(document).ready(function() {
      jQuery.validator.addMethod("greaterThan",
      function (value, element, param) {
      var $min = $(param);
      if(!$(param).val()){
        $min = 0;
      }
      if (this.settings.onfocusout) {
        $min.off(".validate-greaterThan").on("blur.validate-greaterThan", function () {
          $(element).valid();
        });
      }
      return parseFloat(value) > parseFloat($min.val());
    }, "Podana wartośc jest nieprawidłowa.");
		
		
		$("#addPromotionForm").validate({
			ignore: ":disabled",
			ignore: ":hidden",
			rules: {
				url: {
					required: true,
					url: true
				},
				'old-price-low': {
					greaterThan: '#newPriceLow'
				},
				'old-price-high': {
					greaterThan: '#oldPriceLow'
				},
				'new-price-high': {
					greaterThan: '#newPriceLow'
				}
			},
			messages: {
				'old-price-low': {
					greaterThan: 'Wartość minimalna standardowej ceny musi być wyższa od ceny minimalnej po przecenie.'
				},
				'old-price-high': {
					greaterThan: 'Wartość maksymalna musi byc wyższa od minimalnej.'
				},
				'new-price-high': {
					greaterThan: 'Wartość maksymalna musi być wyższa od minimalnej.'
				}
			}
		});
		
		$('.price').mask('0000.00', {reverse: true});
		$('#radFirst').val(this.checked);
		$("input[name='old-price-low']").show();
		$("input[name='new-price-low']").show();
		$("input[name='old-price-high']").hide();
		$("input[name='new-price-high']").hide();
		$(".form-pricebox .more").hide();
		$("input[name='old-price-low']").prop('disabled', true);
		$("input[name='old-price-high']").prop('disabled', true);	
		
		//One product
		$('#radFirst').change(function() {
			if(this.checked) {
				//Show price inputs
				$("input[name='old-price-low']").show();
				$("input[name='new-price-low']").show();
				
				//Hide unnecessary elements
				$(".form-pricebox .more").hide();
				$("input[name='old-price-high']").hide();
				$("input[name='new-price-high']").hide();
				
				//Show rest of elements
        $(".form-pricebox .currency").show();
				$("label[for='checkOld']").show();
				$(".form-pricebox p").show();
				$(".form-pricebox hr").show();
				
				//Remove errors from validation
				$("input[name='old-price-high']").removeClass('error');
				$("input[name='new-price-high']").removeClass('error');
				$("#oldPriceHigh-error").remove();
				$("#newPriceHigh-error").remove();
			}
			$('#radFirst').val(this.checked);        
		});
		
		//More than one product
		$('#radSecond').change(function() {
			if(this.checked) {
				//Show price inputs
				$("input[name='old-price-low']").show();
				$("input[name='new-price-low']").show();
				$("input[name='old-price-high']").show();
				$("input[name='new-price-high']").show();
				
				//Show rest of elements
				$(".form-pricebox .more").show();
        $(".form-pricebox .currency").show();
				$("label[for='checkOld']").show();
				$(".form-pricebox p").show();
				$(".form-pricebox hr").show();
			}
			$('#radSecond').val(this.checked);        
		});
		
		//FREE
		$('#radThird').change(function() {
			if(this.checked) {
				//Hide price inputs
				$("input[name='old-price-low']").hide();
				$("input[name='new-price-low']").hide();
				$("input[name='old-price-high']").hide();
				$("input[name='new-price-high']").hide();
				
				//Hide rest of unnecessary elements
				$(".form-pricebox .more").hide();
        $(".form-pricebox .currency").hide();
				$("label[for='checkOld']").hide();
				$(".form-pricebox p").hide();
				$(".form-pricebox hr").hide();
				
				//Remove errors from validation
				$("input[name='old-price-low']").removeClass('error');
				$("input[name='old-price-high']").removeClass('error');
				$("input[name='new-price-low']").removeClass('error');
				$("input[name='new-price-high']").removeClass('error');
				$("#oldPriceLow-error").remove();
				$("#oldPriceHigh-error").remove();
				$("#newPriceLow-error").remove();
				$("#newPriceHigh-error").remove();
			}
			$('#radThird').val(this.checked);        
		});
		
		$('#checkOld').change(function() {
			if(this.checked) {
				//Turn on price inputs
				$("input[name='old-price-low']").prop('disabled', false);
				$("input[name='old-price-high']").prop('disabled', false);
				
				//Remove errors from validation
				$("#old-price-low-error").remove();
				$("#old-price-high-error").remove();
			} else {
				//Turn off price inputs
				$("input[name='old-price-low']").prop('disabled', true);
				$("input[name='old-price-high']").prop('disabled', true);	
				
				//Remove errors from validation
				$("input[name='old-price-low']").removeClass('error');
				$("input[name='old-price-high']").removeClass('error');
				$("#oldPriceLow-error").remove();
				$("#oldPriceHigh-error").remove();
			}
			$('#checkOld').val(this.checked);        
		});
	});
</script>
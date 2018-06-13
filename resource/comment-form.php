<div class="comment-container">

	<form id="commentForm" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">

		<div class="form-group">
				<textarea id="comment" class="long" name="comment" placeholder="Co myślisz o tej promocji?" ></textarea>
		</div>
		
		<div class="comment-controls">
    		<span class="note">Pozostało <span class="counter"></span> znaków.</span>
    		<button class="btn" type="submit" name="submit">Wyślij</button>
		</div>
	</form>
</div>

<script id="js-comment" type="text/javascript">
    function updateCountdown() {
        var remaining = 256 - jQuery('#comment').val().length;
        jQuery('.counter').text(remaining);
    }
    jQuery(document).ready(function($) {
        updateCountdown();
        $("#comment").attr('maxlength','256');
        $(".comment-controls button").attr("disabled",true);
        
        $("#comment").on( "input", function() {
            updateCountdown();
            $('.comment-controls button').prop('disabled', this.value == "" ? true : false);
        });
    });
    
</script>

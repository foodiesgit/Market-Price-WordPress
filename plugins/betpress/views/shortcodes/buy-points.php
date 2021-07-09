<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<form action="<?php echo $paypal_listener; ?>" method="post" accept-charset="utf-8" id="buy-points-submit">
    
    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="charset" value="utf-8" />
    <input type="hidden" name="business" value="<?php echo $paypal_mail; ?>" />
    <input type="hidden" name="item_name" value="<?php echo $name; ?>" />
    <input type="hidden" name="item_number" value="<?php echo $code; ?>" />
    <input type="hidden" name="amount" value="<?php echo $price; ?>" />
    <input type="hidden" name="currency_code" value="<?php echo $currency; ?>" />
    <input type="hidden" name="return" value="<?php echo $return_url_ok; ?>" />
    <input type="hidden" name="cancel_return" value="<?php echo $return_url_fail; ?>" />
    
    <div class="buy-points-wrapper">
            
        <div class="help-box" id="help-points-box" style="display:none;background-color: rgba(200,20,20,0.8);color: whitesmoke;">
            <div class="help-min-points-row"><?php printf(__('Minimal points to buy: %d', 'BetPress'), $min_points); ?></div>
            <div class="help-max-points-row"><?php printf(__('Maximum points to buy: %d', 'BetPress'), $max_points); ?></div>
            <div class="help-limit-points-row"><?php printf(__('Your limit: %d', 'BetPress'), $user_max_points_to_buy); ?></div>
        </div>
        
        <div class="buy-points-heading-row">
            
            <div class="buy-points-heading-points">
                <?php esc_attr_e('Points', 'BetPress'); ?>
                <img src="<?php echo BETPRESS_IMAGE_FOLDER ?>help-32.png" alt="<?php esc_attr_e('Help', 'BetPress'); ?>" id="help-points" width="16px" height="16px" />
            </div>
            
            <div class="buy-points-heading-price"><?php esc_attr_e('Price', 'BetPress'); ?></div>
            
            <div class="buy-points-heading-btn"></div>
            
        </div>
        
        <div class="buy-points-row">
            
            <div class="buy-points-points">
                
                <input type="text" id="quantity-input" value="<?php echo $quantity; ?>" style="width: 60px" />
                
            </div>
            
            <div class="buy-points-price">
                
                <span id="price-container"><?php echo $price; ?></span>
                
                <span id="currency-container"><?php echo $currency; ?></span>
                
            </div>
            
            <div class="buy-points-btn">
                
                <input
                    type="submit"
                    name="submit"
                    value="<?php esc_attr_e('Buy', 'BetPress'); ?>"
                    id="buy-submit" 
                    <?php echo $quantity > $user_max_points_to_buy ? 'disabled' : ''; ?> />
                
            </div>
            
        </div>
        
    </div>  
    
</form>

<script type="text/javascript">
    
    var help_points_image = document.getElementById('help-points');
    var help_points_box = document.getElementById('help-points-box');

    window.onclick = function (event) {
        
        if (event.target === help_points_image) {
            
            help_points_box.style.display = 'inline';
            
        } else {
            
            help_points_box.style.display = 'none';
            
        }
        
    };

    var quantity_input = document.getElementById('quantity-input');
    var quantity_before = quantity_input.value;
    
    quantity_input.onkeyup = function () {
              
        help_points_box.style.display = 'none';
            
        var quantity_input = document.getElementById('quantity-input');     
        var quantity = quantity_input.value;
        
        var errors = false;
        
        if (quantity.charAt(quantity.length - 1) === '.' ) {
            errors = true;
            var not_num_error = true;    
        }
        
        if (isNaN(quantity) ) {          
            errors = true;
            var not_num_error = true;
        }
        
        if (quantity === '0') {
            errors = true;
            var zero_error = true;
        }
        
        if (quantity === '') {
            errors = true;
        }
        
        if (quantity < 0) {
            errors = true;
            var neg_num_error = true;
        }
        
        var min_points = <?php echo $min_points; ?>;
        if (quantity < min_points) {
            errors = true;
        }
            
        var max_points = <?php echo $max_points; ?>;
        if (quantity > max_points) {
            errors = true;
            var over_max_error = true;
        }
        
        if ( false === errors ) {
            
            document.getElementById('buy-submit').disabled = false;
            
            var ratio = <?php echo $ratio; ?>;
            var changed_amount = parseInt(document.getElementById('quantity-input').value);
            var changed_price = (changed_amount / ratio).toFixed(2);          
            var points_name = '<?php echo $points_name; ?>';
            var price_container = document.getElementById('price-container');
            var paypal_name = document.getElementsByName('item_name')[0];
            var paypal_amount = document.getElementsByName('amount')[0];
            
            price_container.innerHTML = changed_price;
            paypal_name.value = changed_amount + ' ' + points_name;
            paypal_amount.value = changed_price;
            
            quantity_before = quantity;
            
        } else {
        
            if ( (typeof over_max_error !== 'undefined') || (typeof neg_num_error !== 'undefined') || (typeof not_num_error !== 'undefined') || (typeof zero_error !== 'undefined') ) {
                quantity_input.value = quantity_before;
            }
            
            help_points_box.style.display = 'inline';
        }
        
    };
    
    var buy_points_submit = document.getElementById('buy-points-submit');
    buy_points_submit.onsubmit = function () {
        
        var amount = parseInt(document.getElementById('quantity-input').value);
        var price = document.getElementById('price-container').innerHTML;
        var currency = document.getElementById('currency-container').innerHTML;
        
        betpress_set_cookie('betpress_points_quantity', amount, 1, '/');
        betpress_set_cookie('betpress_price', price, 1, '/');
        betpress_set_cookie('betpress_currency', currency, 1, '/');
        
        if (amount === 0 || price === '0') {
            return false;
        }
        
        return true;
        
    };
    
    function betpress_set_cookie(cookie_key, cookie_value, hours, path) {
    
        var now = new Date();
        var time = now.getTime();
        time += 3600 * 1000 * hours;
        now.setTime(time);
    
        document.cookie =
            cookie_key + '=' + cookie_value +
            '; expires=' + now.toUTCString() +
            '; path=' + path;
    }
    
</script>
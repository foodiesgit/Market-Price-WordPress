<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<script>var odds = []; var pw_ids = [];</script>

<div id="betslip-wrapper" style="position:fixed">
    
<?php if ($show_points): ?>

    <?php if (get_current_user_id()): ?>

        <div class="points-holder">
        
            <?php printf(__('Your points: %s', 'BetPress'), $user_points); ?>

        </div>
        
    <?php endif; ?>
    
<?php endif; ?>
    
    <div class="bets-holder">
        
<?php if ($bet_options): ?>
        
    <?php foreach ($bet_options as $bet_option_info): ?>    
        
        <?php betpress_get_view('widget-bet-options', '', array('bet_option_info' => $bet_option_info)); ?>
        
    <?php endforeach; ?>
        
<?php else: ?>
        
    <?php betpress_get_view('info-message', '', array('info_message' => esc_attr__('Your betting slip is empty.', 'BetPress'))); ?>
        
<?php endif; ?>

    </div>
	
	<div class="pw"><?php esc_attr_e('To return €', 'betpress'); ?><span id="pw">0.00</span></div>
    <button type="button" class="clear_all_bet_option"><?php esc_attr_e('Clear All', 'betpress'); ?></button>
    <div class="actions-holder">
    
        <div id="input-stake-holder">
            
            <input
                type="number"
                placeholder="<?php esc_attr_e('stake', 'BetPress'); ?>"
                min="1" 
                step="<?php echo get_option('bp_only_int_stakes') ? 1 : 0.01 ?>" 
                id="stake-input" 
                name="stake"
                />
        
        </div>
        
        <div class="submit-slip">
    
            <button id="submit-slip-button"><?php esc_attr_e('Submit', 'BetPress'); ?></button>
            
        </div>
        
        <div class="clear"></div>
    
    </div>
    

</div>
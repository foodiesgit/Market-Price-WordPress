<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<div id="balance-switcher-wrapper">
    
    <select id="balance-type-switcher-dropdown">
    
        <?php foreach ($types as $type_value => $type_translatable): ?>
        
            <option value="<?php echo $type_value; ?>" <?php echo $type_value === $current_balance ? 'selected' : ''; ?>>
                    
                <?php echo $type_translatable; ?>
                    
            </option>
    
        <?php endforeach; ?>
        
    </select>
    
</div>
<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<div class="wrap">
    
    <h2><?php esc_attr_e('Points log', 'BetPress'); ?></h2>
    
    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="GET" class="filter-form">
        
        <input type="hidden" name="page" value="bp-points-log" />
    
        <select name="user_id">
            
            <option value="0" selected><?php esc_attr_e('All users', 'BetPress'); ?></option>
            
            <?php foreach ($users as $user): ?>
            
            <option value="<?php echo $user['user_id']; ?>" <?php echo ( isset($_GET['user_id']) && $_GET['user_id'] == $user['user_id'] ) ? 'selected' : '' ?>>
                
                <?php echo $user['display_name']; ?>
                
            </option>
            
            <?php endforeach; ?>
            
        </select>
        
        <input type="submit" class="button-secondary" value="<?php esc_attr_e('Filter', 'BetPress'); ?>" />
    
    </form>
    
    <table class="widefat">
        
        <thead>
            
            <tr>
                
                <th class="row-title"><?php esc_attr_e('User', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Points', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Way of acquisition', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Date*', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Type', 'BetPress'); ?></th>
                
            </tr>
            
        </thead>
        
        <tbody>
            
            <?php if (empty($logs)): ?>
            
            <tr>
                
                <td>
                    
                    <h4><?php esc_attr_e('Nothing to show.', 'BetPress'); ?></h4>
                    
                </td>
                
            </tr>
            
            <?php else: ?>
            
                <?php foreach ($logs as $log): ?>
            
            <tr>
                
                <td class="row-title">
                    
                    <?php echo $log['display_name']; ?>
                    
                </td>
                
                <td>
                    
                    <span class="<?php echo $log['points_amount'] <= 0 ? 'inactive' : 'active' ;?>">
                        
                        <?php echo $log['points_amount'] > 0 ? '+' . $log['points_amount'] : $log['points_amount']; ?>
                        
                    </span>
                    
                </td>
                
                <td>
                    
                    <?php echo $log['message']; ?>
                    
                </td>
                
                <td>
                    
                    <?php echo betpress_local_tz_time($log['date']); ?>
                    
                </td>
                
                <td>
                    
                    <?php echo $log['type']; ?>
                    
                </td>
                
            </tr>
            
                <?php endforeach; ?>
            
            <?php endif; ?>
            
        </tbody>
        
        <tfoot>
            
            <tr>
                
                <th class="row-title"><?php esc_attr_e('User', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Points', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Way of acquisition', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Date*', 'BetPress'); ?></th>
                
                <th><?php esc_attr_e('Type', 'BetPress'); ?></th>
                
            </tr>
            
        </tfoot>
        
    </table>
    
    <div class="help-info">
        
        <?php esc_attr_e('*Time is showed in the timezone you have selected via Settings->General page.', 'BetPress'); ?>
        
    </div>
 
</div>


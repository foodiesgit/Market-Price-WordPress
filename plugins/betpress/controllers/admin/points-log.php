<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_points_log_controller() {
    
    if ( (isset($_GET['user_id'])) && (get_userdata($_GET['user_id'])) ) {
        
        $user_ID = betpress_sanitize($_GET['user_id']);
        $logs = betpress_get_points_logs($user_ID);
        
    } else {
        
        $logs = betpress_get_points_logs();
        
    }
    
    foreach ($logs as &$log) {
        
        // decide how the points were awarded
        if ($log['comment_id'] === '0' && $log['admin_id'] === '0') {
            
            $log['message'] = __('Bought via PayPal', 'BetPress');
            
        } elseif ($log['comment_id'] !== '0') {
            
            $log['message'] = sprintf(__('Awarded for approved comment #%s', 'BetPress'), $log['comment_id']);
            
        } elseif ($log['admin_id'] !== '0') {
            
            $admin = get_userdata($log['admin_id']);
            
            if (false === $admin) {
                
                $admin_username = __('*DELETED ACCOUNT*', 'BetPress');
                        
            } else {
            
                $admin_username = $admin->user_login;
            
            }
                
            $log['message'] = sprintf(__('Manually adjusted by admin %s', 'BetPress'), $admin_username);
            
        } else {
            
            $log['message'] = __('WARNING: Unknown way of getting the points.', 'BetPress');
            
        }
        
        // transform points type
        if (BETPRESS_POINTS === $log['type']) {
            
            $log['type'] = __('BetPress points', 'BetPress');
            
        } elseif (BETPRESS_BOUGHT_POINTS === $log['type']) {
            
            $log['type'] = __('BetPress bought points', 'BetPress');
            
        } else {
            
            $log['type'] = __('WARNING: Unknown type of points. Contact the BetPress support team.', 'BetPress');
            
        }
        
    }
    
    $pass['users'] = betpress_get_users_with_points_logs();
    $pass['logs'] = $logs;
    betpress_get_view('points-log', 'admin', $pass);
    
}


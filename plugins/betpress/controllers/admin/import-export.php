<?php

//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}

function betpress_import_export_controller() {
    
    if (isset($_POST['importing_csv'])) {
            
        $file_error = '';
        
        if ( isset($_FILES['file']) && !empty($_FILES['file']['tmp_name']) ) {
            
            if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                
                $file_name_array = explode('.', $_FILES['file']['name']);
                
                if (strcasecmp($file_name_array[count($file_name_array) - 1], 'csv') !== 0) {
                    
                    $file_error = __('Uploaded file has to be CSV.', 'BetPress');
                    
                }
                
            } else {
                
                $file_error = __('File upload attack detected.', 'BetPress');
                
            }
            
        } else {
            
            $file_error = __('Please select a file', 'BetPress');
            
        }
            
        if (strcmp($file_error, '') !== 0) {

            $pass['error_message'] = $file_error;
            betpress_get_view('error-message', 'admin', $pass);

        } else {
            
            $action = $_POST['import-action'];
            
            if (in_array($action, array(BETPRESS_IMPORT_NEW, BETPRESS_IMPORT_UPDATE), true)) {
                
                if (BETPRESS_IMPORT_NEW === $action) {
            
                    $auto_activate = (isset($_POST['auto_activate']) && $_POST['auto_activate'] === '1') ? 1 : 0; 

                    if (($handle = fopen($_FILES['file']['tmp_name'], 'r')) !== false) {

                        $errors = array();

                        $rowNumber = 0;

                        while (($row = fgetcsv($handle, 100000, ',')) !== false) {

                            $rowNumber ++;

                            $rowColumns = count($row);

                            if ($rowColumns !== 8) {

                                $errors[] = sprintf(__('Row number %d was NOT inserted because there are %d columns (we expect exactly 8 columns on every row).', 'BetPress'), $rowNumber, $rowColumns);

                                continue;
                            }

                            $sport_name = betpress_sanitize($row[0]);

                            if (strlen($sport_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT inserted because of empty sport name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            if (betpress_is_sport_exists($sport_name)) {

                                $sport_row = betpress_get_sport_by_name($sport_name);

                                $sport_ID = $sport_row['sport_id'];

                            } else {

                                $max_order = betpress_get_sports_max_order();

                                $sport_ID = betpress_insert(
                                    'sports',
                                    array(
                                        'sport_name' => $sport_name,
                                        'sport_sort_order' => ++ $max_order,
                                    )
                                );

                            }

                            if (is_numeric($sport_ID)) {

                                betpress_register_string_for_translation('sport-' . $sport_name, $sport_name);

                                $event_name = betpress_sanitize($row[1]);

                                if (strlen($event_name) < 1) {

                                    $errors [] = sprintf(__('Row number %d was NOT inserted because of empty event name.', 'BetPress'), $rowNumber);

                                    continue;

                                }

                                if (betpress_is_event_exists($event_name)) {

                                    $event_row = betpress_get_event_by_name($event_name);

                                    $event_ID = $event_row['event_id'];

                                } else {

                                    $max_order = betpress_get_events_max_order($sport_ID);

                                    $event_ID = betpress_insert(
                                        'events',
                                        array(
                                            'event_name' => $event_name,
                                            'event_sort_order' => ++ $max_order,
                                            'sport_id' => $sport_ID,
                                        )
                                    );

                                }

                                if (is_numeric($event_ID)) {

                                    betpress_register_string_for_translation('event-' . $event_name, $event_name);

                                    $bet_event_name = betpress_sanitize($row[2]);

                                    if (strlen($bet_event_name) < 1) {

                                        $errors [] = sprintf(__('Row number %d was NOT inserted because of empty bet event name.', 'BetPress'), $rowNumber);

                                        continue;

                                    }

                                    if (betpress_is_bet_event_name_exists($bet_event_name, $event_ID)) {

                                        $bet_event_row = betpress_get_bet_event_by_name($bet_event_name, $event_ID);

                                        $bet_event_ID = $bet_event_row['bet_event_id'];

                                    } else {

                                        //check is weird but otherwise strtotime will generate an error (at least php doc says so)
                                        if (( $deadline = strtotime(betpress_sanitize($row[3])) ) === false) {

                                            $errors [] = sprintf(__('Row number %d was NOT inserted because of invalid bet event\'s start date.', 'BetPress'), $rowNumber);

                                            continue;

                                        }

                                        $max_order = betpress_get_bet_events_max_order($event_ID);

                                        $bet_event_ID = betpress_insert(
                                            'bet_events', 
                                            array(
                                                'bet_event_name' => $bet_event_name,
                                                'deadline' => $deadline,
                                                'is_active' => $auto_activate,
                                                'bet_event_sort_order' => ++ $max_order,
                                                'event_id' => $event_ID,
                                            )
                                        );

                                    }

                                    if (is_numeric($bet_event_ID)) {

                                        betpress_register_string_for_translation('bet-event-' . $bet_event_name, $bet_event_name);

                                        $category_name = betpress_sanitize($row[4]);

                                        if (strlen($category_name) < 1) {

                                            $errors [] = sprintf(__('Row number %d was NOT inserted because of empty category name.', 'BetPress'), $rowNumber);

                                            continue;

                                        }

                                        if (betpress_is_category_name_exists($category_name, $bet_event_ID)) {

                                            $category_row = betpress_get_category_by_name($category_name, $bet_event_ID);

                                            $category_ID = $category_row['bet_event_cat_id'];

                                        } else {

                                            $max_order = betpress_get_cats_max_order($bet_event_ID);

                                            $category_ID = betpress_insert(
                                                    'bet_events_cats',
                                                    array(
                                                        'bet_event_cat_name' => $category_name,
                                                        'bet_event_id' => $bet_event_ID,
                                                        'bet_event_cat_sort_order' => ++ $max_order,
                                                    )
                                            );

                                        }

                                        if (is_numeric($category_ID)) {

                                            betpress_register_string_for_translation('cat-' . $category_name, $category_name);

                                            $bet_option_name = betpress_sanitize($row[5]);

                                            if (strlen($bet_option_name) < 1) {

                                                $errors [] = sprintf(__('Row number %d was NOT inserted because of empty bet option name.', 'BetPress'), $rowNumber);

                                                continue;

                                            }

                                            if ( ! betpress_is_bet_option_name_exists($bet_option_name, $category_ID)) {

                                                $bet_option_odd = betpress_floordec(betpress_sanitize($row[6]));

                                                if ($bet_option_odd < 1) {

                                                    $errors [] = sprintf(__('Row number %d was NOT inserted because the odd can NOT be less than 1.', 'BetPress'), $rowNumber);

                                                    continue;
                                                }

                                                $status = strtolower(betpress_sanitize($row[7]));

                                                $validStatuses = array(
                                                    BETPRESS_STATUS_AWAITING,
                                                    BETPRESS_STATUS_WINNING,
                                                    BETPRESS_STATUS_LOSING,
                                                    BETPRESS_STATUS_CANCELED,
                                                );

                                                if ( ! in_array($status, $validStatuses, true) ) {

                                                    $errors [] = sprintf(__('Row number %d was NOT inserted because the status is incorrect. Possible statuses: %s', 'BetPress'), $rowNumber, implode(' , ', $validStatuses));

                                                    continue;

                                                }

                                                $max_order = betpress_get_bet_options_max_order($category_ID);

                                                $is_inserted = betpress_insert(
                                                        'bet_options',
                                                        array(
                                                            'bet_option_name' => $bet_option_name,
                                                            'bet_option_odd' => $bet_option_odd,
                                                            'bet_option_sort_order' => ++ $max_order,
                                                            'bet_event_cat_id' => $category_ID,
                                                            'status' => $status,
                                                        )
                                                );

                                                if ($is_inserted) {

                                                    betpress_register_string_for_translation('bet-option-' . $bet_option_name, $bet_option_name);
                                                    betpress_register_string_for_translation('status-' . $status, $status);

                                                } else {

                                                    $errors [] = sprintf(__('Row number %d was NOT inserted because of database error.', 'BetPress'), $rowNumber);

                                                    continue;
                                                }
                                            }
                                        } else {

                                            $errors [] = sprintf(__('Row number %d was NOT inserted because of database error.', 'BetPress'), $rowNumber);

                                            continue;
                                        }
                                    } else {

                                        $errors [] = sprintf(__('Row number %d was NOT inserted because of database error.', 'BetPress'), $rowNumber);

                                        continue;
                                    }
                                } else {

                                    $errors [] = sprintf(__('Row number %d was NOT inserted because of database error.', 'BetPress'), $rowNumber);

                                    continue;
                                }
                            } else {

                                $errors [] = sprintf(__('Row number %d was NOT inserted because of database error.', 'BetPress'), $rowNumber);

                                continue;
                            }

                        }

                        if ($errors) {

                            foreach ($errors as $error) {

                                $pass['error_message'] = $error;
                                betpress_get_view('error-message', 'admin', $pass);
                            }

                        } else {

                            $pass['update_message'] = __('The file has been successfully imported.', 'BetPress');
                            betpress_get_view('updated-message', 'admin', $pass);

                        }

                        fclose($handle);
                    }

                } elseif (BETPRESS_IMPORT_UPDATE === $action) {

                    if (($handle = fopen($_FILES['file']['tmp_name'], 'r')) !== false) {

                        $errors = array();

                        $rowNumber = 0;

                        while (($row = fgetcsv($handle, 100000, ',')) !== false) {

                            $rowNumber ++;

                            $rowColumns = count($row);

                            if ($rowColumns !== 8) {

                                $errors[] = sprintf(__('Row number %d was NOT updated because there are %d columns (we expect exactly 8 columns on every row).', 'BetPress'), $rowNumber, $rowColumns);

                                continue;
                            }

                            $sport_name = betpress_sanitize($row[0]);

                            if (strlen($sport_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT updated because of empty sport name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            $event_name = betpress_sanitize($row[1]);

                            if (strlen($event_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT updated because of empty event name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            $bet_event_name = betpress_sanitize($row[2]);

                            if (strlen($bet_event_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT updated because of empty bet event name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            $category_name = betpress_sanitize($row[4]);

                            if (strlen($category_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT updated because of empty category name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            $bet_option_name = betpress_sanitize($row[5]);

                            if (strlen($bet_option_name) < 1) {

                                $errors [] = sprintf(__('Row number %d was NOT updated because of empty bet option name.', 'BetPress'), $rowNumber);

                                continue;

                            }

                            if ($bet_option_row = betpress_get_bet_option_by_all_details($sport_name, $event_name, $bet_event_name, $category_name, $bet_option_name)) {

                                if (count($bet_option_row) !== 1) {

                                    $errors [] = sprintf(__('Row number %d was NOT updated because of dublicated bet options. Please contact the support team to check your database!', 'BetPress'), $rowNumber);

                                    continue;

                                }

                                $bet_option_row = $bet_option_row[0];

                                $bet_option_odd = betpress_floordec(betpress_sanitize($row[6]));

                                if ($bet_option_odd < 1) {

                                    $errors [] = sprintf(__('Row number %d was NOT updated because the odd can NOT be less than 1.', 'BetPress'), $rowNumber);

                                    continue;

                                }

                                //check is weird but otherwise strtotime will generate an error (at least php doc says so)
                                if (($deadline = strtotime(betpress_sanitize($row[3]))) === false) {

                                    $errors [] = sprintf(__('Row number %d was NOT updated because of invalid bet event\'s start date.', 'BetPress'), $rowNumber);

                                    continue;

                                }

                                if ($deadline != $bet_option_row['deadline']) {

                                    $errors [] = sprintf(__('Row number %d was NOT updated because the starts date in the CSV does NOT match the current starts date.', 'BetPress'), $rowNumber);

                                    continue;

                                }

                                $status = strtolower(betpress_sanitize($row[7]));

                                $validStatuses = array(
                                    BETPRESS_STATUS_AWAITING,
                                    BETPRESS_STATUS_WINNING,
                                    BETPRESS_STATUS_LOSING,
                                    BETPRESS_STATUS_CANCELED,
                                );

                                if ( ! in_array($status, $validStatuses, true) ) {

                                    $errors [] = sprintf(__('Row number %d was NOT updated because the status is incorrect. Possible statuses: %s', 'BetPress'), $rowNumber, implode(' , ', $validStatuses));

                                    continue;

                                }

                                $update = betpress_update('bet_options', array('status' => $status), array('bet_option_id' => $bet_option_row['bet_option_id']));
                                
                                if (false === $update) {
                                    
                                    $errors [] = sprintf(__('Row number %d was NOT updated because of database error.', 'BetPress'), $rowNumber);

                                    continue;
                                    
                                }

                            } else {

                                $errors [] = sprintf(__('Row number %d was NOT updated because we could NOT find a row with these details.', 'BetPress'), $rowNumber);

                                continue;

                            }

                        }

                        if ($errors) {

                            foreach ($errors as $error) {

                                $pass['error_message'] = $error;
                                betpress_get_view('error-message', 'admin', $pass);
                            }

                        } else {

                            $pass['update_message'] = __('All rows of the file was successfully updated.', 'BetPress');
                            betpress_get_view('updated-message', 'admin', $pass);

                        }

                        fclose($handle);
                    }

                }
            
            } else {
                
                $pass['error_message'] = __('Invalid action.', 'BetPress');
                betpress_get_view('error-message', 'admin', $pass);
            }
        }
    }

    betpress_get_view('import-export', 'admin');
    
}
<?php
//don't allow direct access via url
if ( ! defined('ABSPATH') ) {
    exit();
}
?>
<div class="wrap">
    
    <h2><?php esc_attr_e('Import/Export', 'BetPress'); ?></h2>
    
    <h3><?php esc_attr_e('Import CSV data', 'BetPress'); ?></h3>
    
    <form enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="POST">
    
    <table class="form-table">

        <tr valign="top">

            <th scope="row"><?php esc_attr_e('Notes', 'BetPress'); ?></th>

            <td class="help-info">
                
                <a href="#" id="betpress-import-export-notes-trigger"><?php esc_attr_e('Click me for detailed information.', 'BetPress'); ?></a>
                    
                <div class="help-box" id="betpress-import-export-notes-box" style="display: none; background-color: rgba(200,20,20,0.9); color: whitesmoke;">
                    
                    <div><?php esc_attr_e('Only CSV files allowed.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('Each row must have 8 columns: Sport, Event, Bet event, Start date, Category, Bet option, Odd (in decimals), Status.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('Dates must be in GMT and must follow the format YYYY-MM-DD HH:MM.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('Do not write a header, just rows with data.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('"Updating data" is meant to update only the Status column.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('If a row error is displayed, the rest of the file is processed.', 'BetPress'); ?></div>

                    <div><?php esc_attr_e('Importing same file more than once (or same rows more than once in single file) is not a problem, the data will not be dublicated.', 'BetPress'); ?></div>
                    
                    <div>
                        <a href="https://docs.google.com/spreadsheets/d/1lXP9MaFPJaboeDjgnhScj1T1xQ9VvQ1c-tsrP4RLu-E/edit?usp=sharing" target="_blank">
                            <?php esc_attr_e('View an example', 'BetPress'); ?>
                        </a>
                    </div>

                </div>
            </td>

        </tr>

        <tr valign="top">

            <th scope="row">

                <label for="file"><?php esc_attr_e('Choose a file', 'BetPress'); ?></label>

            </th>

            <td>
                
                <input name="file" type="file" id="file" />

            </td>

        </tr>

        <tr valign="top">

            <th scope="row">

                <label for="betpress-import-action"><?php esc_attr_e('New data or updating?', 'BetPress'); ?></label>

            </th>

            <td>
                
                <select name="import-action" id="betpress-import-action">
                    
                    <option value="<?php echo BETPRESS_IMPORT_NEW; ?>"><?php esc_attr_e('New data', 'BetPress'); ?></option>
                    
                    <option value="<?php echo BETPRESS_IMPORT_UPDATE; ?>"><?php esc_attr_e('Updating data', 'BetPress'); ?></option>
                    
                </select>

            </td>

        </tr>
        
        <tr valign="top" id="betpress-auto-activating-csv-imported-data">

            <th scope="row">

                <label for="auto-activate"><?php esc_attr_e('Auto activate', 'BetPress'); ?></label>

            </th>

            <td>
                    
                <input 
                    type="checkbox"
                    id="auto-activate"
                    name="auto_activate"
                    value="1"
                />

                <span class="help-info"><?php esc_attr_e('If checked, you don\'t need to manually activate every bet event.', 'BetPress'); ?></span>

            </td>

        </tr>
        
        <tr>
            
            <th>
                
                <input type="submit" name="importing_csv" value="<?php esc_attr_e('Import', 'BetPress'); ?>" class="button-primary" />
                
            </th>
            
        </tr>

    </table>
    
    </form>
    
</div>
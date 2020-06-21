<div class="sct-cleanup-left">
    <p>
        <?php _e("Here you can see all the statistics for all cleanup tools saved in the database.", "smart-cleanup-tools"); ?><br/>
    </p>
    <div class="sct-cleanup-totals">
    <h3><?php _e("Cleanup Totals", "smart-cleanup-tools"); ?></h3>
    <?php
        echo __("Number of cleanups", "smart-cleanup-tools").': <strong>'.$globals['counter'].'</strong><br/>';
        echo __("Records removed", "smart-cleanup-tools").': <strong>'.$globals['rows'].'</strong><br/>';
        echo __("Overhead removed", "smart-cleanup-tools").': <strong>'.sct_size_format($globals['overhead']).'</strong><br/>';
        echo __("Space recovered", "smart-cleanup-tools").': <strong>'.sct_size_format($globals['space']).'</strong><br/>';
    ?>
    </div>
    <div class="sct-cleanup-totals">
    <h3><?php _e("Cron Cleanup Totals", "smart-cleanup-tools"); ?></h3>
    <?php
        echo __("Number of cleanups", "smart-cleanup-tools").': <strong>'.$global_crons['counter'].'</strong><br/>';
        echo __("Records removed", "smart-cleanup-tools").': <strong>'.$global_crons['rows'].'</strong><br/>';
        echo __("Overhead removed", "smart-cleanup-tools").': <strong>'.sct_size_format($global_crons['overhead']).'</strong><br/>';
        echo __("Space recovered", "smart-cleanup-tools").': <strong>'.sct_size_format($global_crons['space']).'</strong><br/>';
    ?>
    </div>
</div>
<div class="sct-cleanup-right" style="padding: 15px 0 15px 5px">
    <h2 class="sct-cleanup-stats-title"><?php _e("Cleanup Tools", "smart-cleanup-tools") ?></h2>
    <div class="sct-cleanup-stats">
        <?php

        if (empty($statistics[$scopes[0]])) {
            echo '<em>'.__("No data yet.", "smart-cleanup-tools").'</em>';
        } else {
            foreach ($statistics[$scopes[0]] as $code => $data) {
                $render = '<div class="sct-cleanup-box sct-stats-box">';
                    $render.= '<h3>'.$data['label'].'</h3>';
                    $render.= '<div class="sct-stats-last">';
                        $render.= __("Last run", "smart-cleanup-tools").': <strong>'.date('r', $data['last_run']).'</strong><br/>';
                        $render.= $data['last_display'].'<br/>';
                        $render.= __("Execution time", "smart-cleanup-tools").': <strong>'.$data['last_time'].' '.__("seconds", "smart-cleanup-tools").'</strong>';
                    $render.= '</div>';
                    $render.= '<div class="sct-stats-total">';
                        $render.= __("Executed", "smart-cleanup-tools").': <strong>'.$data['counter'].' '._n("time", "times", $data['counter'], "smart-cleanup-tools").'</strong><br/>';
                        $render.= __("Total", "smart-cleanup-tools").' '.$data['display'].'<br/>';
                        $render.= __("Total Execution time", "smart-cleanup-tools").': <strong>'.$data['time'].' '.__("seconds", "smart-cleanup-tools").'</strong>';
                    $render.= '</div>';
                $render.= '</div>';

                echo $render;
            }
        }

        ?>
    </div>
    <h2 class="sct-cleanup-stats-title"><?php _e("Reset Tools", "smart-cleanup-tools") ?></h2>
    <div class="sct-cleanup-stats">
        <?php

        if (empty($statistics[$scopes[1]])) {
            echo '<em>'.__("No data yet.", "smart-cleanup-tools").'</em>';
        } else {
            foreach ($statistics[$scopes[1]] as $code => $data) {
                $render = '<div class="sct-cleanup-box sct-stats-box">';
                    $render.= '<h3>'.$data['label'].'</h3>';
                    $render.= '<div class="sct-stats-last">';
                        $render.= __("Last run", "smart-cleanup-tools").': <strong>'.date('r', $data['last_run']).'</strong><br/>';
                        $render.= $data['last_display'].'<br/>';
                        $render.= __("Execution time", "smart-cleanup-tools").': <strong>'.$data['last_time'].' '.__("seconds", "smart-cleanup-tools").'</strong>';
                    $render.= '</div>';
                    $render.= '<div class="sct-stats-total">';
                        $render.= __("Executed", "smart-cleanup-tools").': <strong>'.$data['counter'].' '._n("time", "times", $data['counter'], "smart-cleanup-tools").'</strong><br/>';
                        $render.= __("Total", "smart-cleanup-tools").' '.$data['display'].'<br/>';
                        $render.= __("Total Execution time", "smart-cleanup-tools").': <strong>'.$data['time'].' '.__("seconds", "smart-cleanup-tools").'</strong>';
                    $render.= '</div>';
                $render.= '</div>';

                echo $render;
            }
        }

        ?>
    </div>
</div>

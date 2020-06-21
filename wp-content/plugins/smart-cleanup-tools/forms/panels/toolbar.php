<div class="sct-cleanup-left">
    <p>
        <?php _e("Results for quick cleanup.", "smart-cleanup-tools"); ?>
    </p>
</div>
<div class="sct-cleanup-right">
    <?php

    if ($scope == 'site') {
        $scope = array('site', 'reset');
    } else {
        $scope = array('network', 'netreset');
    }
    
    $nonce = $_GET['_nonce'];
    $action = $_GET['action'];

    if (wp_verify_nonce($nonce, 'sct-toolbar-option')) {
        require_once(SCT_PATH.'core/worker.php');

        $worker = new sct_worker();

        $out = false;
        switch ($action) {
            case 'transients':
                $out = $worker->run_tool($scope, 'transient');
                break;
            case 'rewrite':
                $out = $worker->run_tool($scope, 'rewrite_rules');
                break;
        }

        if ($out === false) {
            _e("This request is invalid.", "smart-cleanup-tools");
        } else {
            echo '<h3 style="margin-top: 0;">'.$out['label'].'</h3>';
            echo '<ul>';
            echo '<li>'.$out['last_display'].'</li>';
            echo '<li>'.__("Processing Time", "smart-cleanup-tools").': <strong>'.$out['last_time'].' '.__("seconds", "smart-cleanup-tools").'</strong></li>';
            echo '</ul>';
        }
    } else {
        _e("This request is invalid.", "smart-cleanup-tools");
    }

    ?>
</div>

<?php

$settings->add(new admin_setting_configtext('mod_memorybank_rebuildtime', 'Rebuild time',
                   'The hour of the day when schedules are re-calculated.', 15, PARAM_INT));
$settings->add(new admin_setting_configtext('mod_memorybank_teachers', 'Teachers',
                   'Users who can create questions', 'ganderson,bwoodman,connorgrosenick,admin,dchapin,saasmath', PARAM_TEXT,100));
$settings->add(new admin_setting_configtext('mod_memorybank_defaulthalflife', 'Default half-life',
                   'Estimate of time (in seconds) that there is a 50% chance of recall', 60*60*24*7, PARAM_INT));
?>
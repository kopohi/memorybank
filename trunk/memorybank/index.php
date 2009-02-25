<?php
/**
 * This page lists all the instances of memorybank in a particular course
 *
 * @Gary Anderson Copyright 2009, All Rights Reserved.  Not yet licenced as GPL
 * @version 0.96 alpha
 * @package memorybank
 **/


    require_once("../../config.php");
    require_once("lib.php");

    $id = required_param('id', PARAM_INT);   // course

    if (! $course = get_record("course", "id", $id)) {
        error("Course ID is incorrect");
    }

    require_login($course->id);

    add_to_log($course->id, "memorybank", "view all", "index.php?id=$course->id", "");

    $strmemorybanks = get_string("modulenameplural", "memorybank");
    $strmemorybank  = get_string("modulename", "memorybank");


/// Print the header

    $navlinks = array();
    $navlinks[] = array('name' => $strmemorybanks, 'link' => '', 'type' => 'activity');
    $navigation = build_navigation($navlinks);

    print_header_simple("$strmemorybanks", "", $navigation, "", "", true, "", navmenu($course));

/// Get all the appropriate data

    if (! $memorybanks = get_all_instances_in_course("memorybank", $course)) {
        notice("There are no memorybank instances", "../../course/view.php?id=$course->id");
        die;
    }

/// Print the list of instances (your module will probably extend this)

    $strname  = get_string("name");
    $strweek  = get_string("week");
    $strtopic  = get_string("topic");

    if ($course->format == "weeks") {
        $table->head  = array ($strweek, $strname);
        $table->align = array ("center", "left");
    } else if ($course->format == "topics") {
        $table->head  = array ($strtopic, $strname);
        $table->align = array ("center", "left", "left", "left");
    } else {
        $table->head  = array ($strname);
        $table->align = array ("left", "left", "left");
    }

    foreach ($memorybanks as $memorybank) {
		
        if (!$memorybank->visible) {
            //Show dimmed if the mod is hidden
            $link = "<a class=\"dimmed\" href=\"view.php?id=$memorybank->coursemodule\">$memorybank->name</a>";
        } else {
            //Show normal if the mod is visible
            $link = "<a href=\"view.php?id=$memorybank->coursemodule\">$memorybank->name</a>";
        }

        if ($course->format == "weeks" or $course->format == "topics") {
            $table->data[] = array ($memorybank->section, $link);
        } else {
            $table->data[] = array ($link);
        }
    }

    echo "<br />";

    print_table($table);

/// Finish the page

    print_footer($course);

?>

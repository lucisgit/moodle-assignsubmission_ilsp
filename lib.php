<?php
/**
 * This file contains the moodle hooks for the ilsp submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
define('ilsp_coversheets_dyslexia',0);
define('ilsp_coversheets_dyscalculia',1);
define('ilsp_coversheets_deafness',2);
define('ilsp_coversheets_visualimpairment',3);

/**
 * Serves cover sheets.
 *
 * @param mixed $course course or id of the course
 * @param mixed $cm course module or id of the course module
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options - List of options affecting file serving.
 * @return bool false if file not found, does not return if found - just send the file
 */
function assignsubmission_ilsp_pluginfile($course,
                                          $cm,
                                          context $context,
                                          $filearea,
                                          $args,
                                          $forcedownload,
                                          array $options=array()) {

    require_login($course, false, $cm);
    $itemid = (int)array_shift($args);

    $relativepath = implode('/', $args);

    $fullpath = "/1/assignsubmission_ilsp/ilsp_coversheets/$itemid/$relativepath";

    $fs = get_file_storage();
    if (!($file = $fs->get_file_by_hash(sha1($fullpath))) || $file->is_directory()) {
        return false;
    }

    // Download MUST be forced - security!
    send_stored_file($file, 0, 0, true, $options);
}

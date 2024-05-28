<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This file contains the moodle hooks for the ILSP submission plugin.
 *
 * @package assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('ILSP_COVERSHEETS_DYSLEXIA', 0);
define('ILSP_COVERSHEETS_DYSCALCULIA', 1);
define('ILSP_COVERSHEETS_DEAFNESS', 2);
define('ILSP_COVERSHEETS_VISUALIMPAIRMENT', 3);

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
function assignsubmission_ilsp_pluginfile($course, $cm, context $context, $filearea, $args, $forcedownload, array $options=[]) {
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

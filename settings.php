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
 * This file defines the admin settings for this plugin
 *
 * @package   assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/assign/submission/ilsp/lib.php');

$settings->add(new admin_setting_configcheckbox('assignsubmission_ilsp/default',
        new lang_string('default', 'assignsubmission_ilsp'),
        new lang_string('default_help', 'assignsubmission_ilsp'), 1));

$settings->add(new admin_setting_heading('assignsubmission_ilsp_cover_header',
        get_string('assignsubmission_ilsp_cover_header', 'assignsubmission_ilsp'), ''));

$settings->add(new admin_setting_configstoredfile('assignsubmission_ilsp/dyslexia_cover',
        get_string('assignsubmission_ilsp_dyslexia_cover', 'assignsubmission_ilsp'),
        get_string('assignsubmission_ilsp_dyslexia_cover_desc', 'assignsubmission_ilsp'), 'ilsp_coversheets',
        ILSP_COVERSHEETS_DYSLEXIA, array('maxfiles' => 1, 'accepted_types' => array('.pdf'))));
$settings->add(new admin_setting_configstoredfile('assignsubmission_ilsp/dyscalculia_cover',
        get_string('assignsubmission_ilsp_dyscalculia_cover', 'assignsubmission_ilsp'),
        get_string('assignsubmission_ilsp_dyscalculia_cover_desc', 'assignsubmission_ilsp'), 'ilsp_coversheets',
        ILSP_COVERSHEETS_DYSCALCULIA, array('maxfiles' => 1, 'accepted_types' => array('.pdf'))));
$settings->add(new admin_setting_configstoredfile('assignsubmission_ilsp/deafness_cover',
        get_string('assignsubmission_ilsp_deafness_cover', 'assignsubmission_ilsp'),
        get_string('assignsubmission_ilsp_deafness_cover_desc', 'assignsubmission_ilsp'), 'ilsp_coversheets',
        ILSP_COVERSHEETS_DEAFNESS, array('maxfiles' => 1, 'accepted_types' => array('.pdf'))));
$settings->add(new admin_setting_configstoredfile('assignsubmission_ilsp/visualimpairment_cover',
        get_string('assignsubmission_ilsp_visualimpairment_cover', 'assignsubmission_ilsp'),
        get_string('assignsubmission_ilsp_visualimpairment_cover_desc', 'assignsubmission_ilsp'), 'ilsp_coversheets',
        ILSP_COVERSHEETS_VISUALIMPAIRMENT, array('maxfiles' => 1, 'accepted_types' => array('.pdf'))));

// Assessment ILSP Public Name.
$settings->add(new admin_setting_heading('assignsubmission_ilsp_name_header',
        get_string('assignsubmission_ilsp_name_header', 'assignsubmission_ilsp'), ''));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/dyslexia_name',
        get_string('assessmentilspnamedyslexia', 'assignsubmission_ilsp'),
        get_string('assessmentilspnamedyslexia_desc', 'assignsubmission_ilsp'), 'SpLD'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/dyscalculia_name',
        get_string('assessmentilspnamedyscalculia', 'assignsubmission_ilsp'),
        get_string('assessmentilspnamedyscalculia_desc', 'assignsubmission_ilsp'), 'Dyscalculia'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/deafness_name',
        get_string('assessmentilspnamedeafness', 'assignsubmission_ilsp'),
        get_string('assessmentilspnamedeafness_desc', 'assignsubmission_ilsp'), 'Deafness'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/visualimpairment_name',
        get_string('assessmentilspnamevisualimpairment', 'assignsubmission_ilsp'),
        get_string('assessmentilspnamevisualimpairment_desc', 'assignsubmission_ilsp'), 'Visual Impairment'));

// Assessment group sync.
$settings->add(new admin_setting_heading('assignsubmission_ilsp_profile_header',
        get_string('assignsubmission_ilsp_profile_header', 'assignsubmission_ilsp'), ''));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/dyslexia_profile',
        get_string('assessmentilspprofiledyslexia', 'assignsubmission_ilsp'),
        get_string('assessmentilspprofiledyslexia_desc', 'assignsubmission_ilsp'), 'dyslexia'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/dyscalculia_profile',
        get_string('assessmentilspprofiledyscalculia', 'assignsubmission_ilsp'),
        get_string('assessmentilspprofiledyscalculia_desc', 'assignsubmission_ilsp'), 'dyscalculia'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/deafness_profile',
        get_string('assessmentilspprofiledeafness', 'assignsubmission_ilsp'),
        get_string('assessmentilspprofiledeafness_desc', 'assignsubmission_ilsp'), 'deafness'));
$settings->add(new admin_setting_configtext('assignsubmission_ilsp/visualimpairment_profile',
        get_string('assessmentilspprofilevisualimpairment', 'assignsubmission_ilsp'),
        get_string('assessmentilspprofilevisualimpairment_desc', 'assignsubmission_ilsp'), 'visualimpairment'));

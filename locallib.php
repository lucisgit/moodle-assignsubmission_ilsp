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
 * This file contains the definition for the library class for ilsp submission plugin
 *
 * This class provides all the functionality for the new assign module.
 *
 * @package assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once("$CFG->dirroot/user/profile/lib.php");
require_once($CFG->dirroot . '/mod/assign/submission/ilsp/renderable.php');
require_once($CFG->dirroot . '/mod/assign/submission/ilsp/lib.php');
require_once($CFG->dirroot . '/user/lib.php');

class assign_submission_ilsp extends assign_submission_plugin {

    /**
     * @var array Coversheet cache.
     */
    protected $coversheets = [];

    /**
     * Get the name of the ilsp submission plugin
     * @return string
     */
    public function get_name() {
        return get_string('ilsp', 'assignsubmission_ilsp');
    }

    /**
     * Get the settings for ilsp submission plugin
     *
     * @param MoodleQuickForm $mform The form to add elements to
     * @return void
     */
    public function get_settings(MoodleQuickForm $mform) {
        global $CFG, $COURSE;
    }

    /**
     * Save the settings for ilsp submission plugin
     *
     * @param stdClass $data
     * @return bool
     */
    public function save_settings(stdClass $data) {
        return true;
    }

    /**
     * Add form elements for settings
     *
     * @param mixed $submission can be null
     * @param MoodleQuickForm $mform
     * @param stdClass $data
     * @return true if elements were added to the form
     */
    public function get_form_elements($submission, MoodleQuickForm $mform, stdClass $data) {
        return true;
    }

    /**
     * Editor format options
     *
     * @return array
     */
    private function get_edit_options() {
        $editoroptions = array();
        return $editoroptions;
    }

    /**
     * Save data to the database and trigger plagiarism plugin,
     * if enabled, to scan the uploaded content via events trigger
     *
     * @param stdClass $submission
     * @param stdClass $data
     * @return bool
     */
    public function save(stdClass $submission, stdClass $data) {
        return true;
    }

    /**
     * Return a list of the text fields that can be imported/exported by this plugin.
     * This appears on the grading worksheet.
     *
     * @return array An array of field names and descriptions. (name=>description, ...)
     */
    public function get_editor_fields() {
        return array('ilsp' => get_string('pluginname', 'assignsubmission_ilsp'));
    }

    /**
     * Load the submission object from it's id.
     *
     * @param int $submissionid The id of the submission we want
     * @return stdClass The submission
     */
    protected function get_submission($submissionid) {
        global $DB;

        $params = array('assignment' => $this->assignment->get_instance()->id, 'id' => $submissionid);
        return $DB->get_record('assign_submission', $params, '*', MUST_EXIST);
    }

    /**
     * Get the saved text content from the editor.
     * This appears on the grading worksheet.
     *
     * @param string $name
     * @param int $submissionid
     * @return string
     */
    public function get_editor_text($name, $submissionid) {
        // Get the submission back so we can get the user.
        $submission = $this->get_submission($submissionid);
        $user = user_get_users_by_id(array($submission->userid))[$submission->userid];

        // Get the valid ilsps and map to their names.
        $ilsps = $this->get_ilsp($user);
        $ilsps = array_map(function ($ilsps) {
                return $ilsps['name'];
        }, $ilsps);

        return implode(', ', $ilsps);
    }

    /**
     * Get the content format for the editor
     *
     * @param string $name
     * @param int $submissionid
     * @return int
     */
    public function get_editor_format($name, $submissionid) {
        return 0;
    }

    /**
     * Internal function - creates htmls structure suitable for YUI tree.
     *
     * @param array $dir
     * @return string
     */
    protected function htmllize_tree($dir) {
        if (empty($dir['files'])) {
            return '';
        }

        // Build a ul of the file list (not really a tree).
        $result = '<ul>';
        foreach ($dir['files'] as $file) {
            $result .= '<li>' .
                '<div>' . ' ' .
                $file->fileurl . ' ' . '</div>' .
                '</li>';
        }

        $result .= '</ul>';
        return $result;
    }

     /**
      * Display ilsp word count in the submission status table
      *
      * @param stdClass $submission
      * @param bool $showviewlink - If the summary has been truncated set this to true
      * @return string
      */
    public function view_summary(stdClass $submission, &$showviewlink) {
        global $PAGE;
        $html = '';

        // Get users to get ilsps.
        $user = user_get_users_by_id(array($submission->userid))[$submission->userid];
        $ilsps = $this->get_ilsp($user);
        $ilspfiles = new ilsp_files($ilsps);
        $hasilsps = count($ilsps) > 0;

        // Put a header on the grading panel page.
        if ($PAGE->pagetype == 'mod-assign-gradingpanel' && $hasilsps) {
            $html .= '<h3>ILSPs</h3>';
            $html .= '<p>'.get_string('assignsubmission_ilsp_description', 'assignsubmission_ilsp').'</p>';
        }
        $this->htmlid = html_writer::random_id('assign_ilsp');
        $html .= '<div id="'.$this->htmlid.'">';
        $html .= $this->htmllize_tree($ilspfiles->dir);
        $html .= '</div>';
        return $html;
    }

    /**
     * Generate the ilsps for this user.
     *
     * @param $user stdClass Current user
     * @return array|void
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function get_ilsp ($user) {
        $this->generate_coversheets();
        $ilsps = $this->coversheets;

        if (empty($profiledyslexia = get_config('assignsubmission_ilsp', 'dyslexia_profile')) ||
            empty($profiledyscalculia = get_config('assignsubmission_ilsp', 'dyscalculia_profile')) ||
            empty($profiledeafness = get_config('assignsubmission_ilsp', 'deafness_profile')) ||
            empty($profilevisualimpairment = get_config('assignsubmission_ilsp', 'visualimpairment_profile'))) {
            return;
        }

        // Get the extra profile fields.
        profile_load_data($user);

        // Set the enabled flags and use them to trim the ilsp array to only return enabled ones.
        $ilsps['ilsp_coversheets_dyslexia']['enabled'] = !empty($user->{'profile_field_'.$profiledyslexia});
        $ilsps['ilsp_coversheets_dyscalculia']['enabled'] = !empty($user->{'profile_field_'.$profiledyscalculia});
        $ilsps['ilsp_coversheets_deafness']['enabled'] = !empty($user->{'profile_field_'.$profiledeafness});
        $ilsps['ilsp_coversheets_visualimpairment']['enabled'] = !empty($user->{'profile_field_'.$profilevisualimpairment});

        foreach ($ilsps as $type => $value) {
            if (!$value['enabled'] || !isset($value['itemid'])) {
                unset($ilsps[$type]);
            }
        }
        return $ilsps;
    }

    /**
     * Generate the generic coversheet information.
     *
     * @throws coding_exception
     * @throws dml_exception
     */
    protected function generate_coversheets() {
        if (count($this->coversheets) == 0) {
            // Get the config and set up the basic parts of the coversheet array.
            if (empty($namedyslexia = get_config('assignsubmission_ilsp', 'dyslexia_name')) ||
                empty($namedyscalculia = get_config('assignsubmission_ilsp', 'dyscalculia_name')) ||
                empty($namedeafness = get_config('assignsubmission_ilsp', 'deafness_name')) ||
                empty($namevisualimpairment = get_config('assignsubmission_ilsp', 'visualimpairment_name'))) {
                return;
            }

            $ilsps['ilsp_coversheets_dyslexia']['itemid'] = ILSP_COVERSHEETS_DYSLEXIA;
            $ilsps['ilsp_coversheets_dyscalculia']['itemid'] = ILSP_COVERSHEETS_DYSCALCULIA;
            $ilsps['ilsp_coversheets_deafness']['itemid'] = ILSP_COVERSHEETS_DEAFNESS;
            $ilsps['ilsp_coversheets_visualimpairment']['itemid'] = ILSP_COVERSHEETS_VISUALIMPAIRMENT;

            $ilsps['ilsp_coversheets_dyslexia']['name'] = $namedyslexia;
            $ilsps['ilsp_coversheets_dyscalculia']['name'] = $namedyscalculia;
            $ilsps['ilsp_coversheets_deafness']['name'] = $namedeafness;
            $ilsps['ilsp_coversheets_visualimpairment']['name'] = $namevisualimpairment;

            // Load the coversheet files from the file store.
            $fs = get_file_storage();
            $syscontext = context_system::instance();

            foreach ($ilsps as $key => $ilsp) {
                $files = $fs->get_area_files($syscontext->id,
                    'assignsubmission_ilsp',
                    'ilsp_coversheets',
                    $ilsp['itemid'],
                    'timemodified',
                    false);

                if (!count($files)) {
                    unset($ilsps[$key]);
                    continue;
                }
                $ilsps[$key]['file'] = array_shift($files);
            }
            $this->coversheets = $ilsps;
        }
    }

    /**
     * Produce a list of files suitable for export that represent this submission.
     *
     * @param stdClass $submission - For this is the submission data
     * @param stdClass $user - This is the user record for this submission
     * @return array - return an array of files indexed by filename
     */
    public function get_files(stdClass $submission, stdClass $user) {
        global $DB;

        $ilsps = $this->get_ilsp($user);

        // Iterate over the enabled ilsps and return the appropriate coversheet files.
        $result = [];
        foreach ($ilsps as $ilsp) {
            $file = $ilsp['file'];
            // Do we return the full folder path or just the file name?
            if (isset($submission->exportfullpath) && $submission->exportfullpath == false) {
                $result[$file->get_filename()] = $file;
            } else {
                $result[$file->get_filepath().$file->get_filename()] = $file;
            }
        }

        return $result;
    }

    /**
     * Display the saved text content from the editor in the view table
     *
     * @param stdClass $submission
     * @return string
     */
    public function view(stdClass $submission) {
        return '';
    }

    /**
     * Return true if this plugin can upgrade an old Moodle 2.2 assignment of this type and version.
     *
     * @param string $type old assignment subtype
     * @param int $version old assignment version
     * @return bool True if upgrade is possible
     */
    public function can_upgrade($type, $version) {
        return false;
    }


    /**
     * Upgrade the settings from the old assignment to the new plugin based one
     *
     * @param context $oldcontext - the database for the old assignment context
     * @param stdClass $oldassignment - the database for the old assignment instance
     * @param string $log record log events here
     * @return bool Was it a success?
     */
    public function upgrade_settings(context $oldcontext, stdClass $oldassignment, & $log) {
        return true;
    }

    /**
     * Formatting for log info
     *
     * @param stdClass $submission The new submission
     * @return string
     */
    public function format_for_log(stdClass $submission) {
        return '';
    }

    /**
     * The assignment has been deleted - cleanup
     *
     * @return bool
     */
    public function delete_instance() {
        return true;
    }

    /**
     * No text is set for this plugin
     *
     * @param stdClass $submission
     * @return bool
     */
    public function is_empty(stdClass $submission) {
        return true;
    }

    /**
     * If true, the plugin will appear on the module settings page and can be
     * enabled/disabled per assignment instance.
     *
     * @return bool
     */
    public function is_configurable() {
        return false;
    }

    /**
     * The ILSP plugin has no submission component so should not be counted
     * when determining whether to show the edit submission link.
     *
     * @return bool
     */
    public function allow_submissions() {
        return false;
    }

    /**
     * Allows hiding this plugin from the submission/feedback screen if it is not enabled.
     *
     * @return bool - if false - this plugin will not accept submissions / feedback
     */
    public function is_enabled() {
        if ($this->assignment->has_instance() && $this->assignment->get_instance()->teamsubmission) {
            return false;
        }
        return true;
    }

    /**
     * Determine if a submission is empty
     *
     * This is distinct from is_empty in that it is intended to be used to
     * determine if a submission made before saving is empty.
     *
     * @param stdClass $data The submission data
     * @return bool
     */
    public function submission_is_empty(stdClass $data) {
        return false;
    }

    /**
     * Return the plugin configs for external functions.
     *
     * @return array the list of settings
     */
    public function get_config_for_external() {
        return (array) $this->get_config();
    }

    /**
     * Get file areas returns a list of areas this plugin stores files
     * @return array - An array of fileareas (keys) and descriptions (values)
     */
    public function get_file_areas() {
        return array('ilsp_coversheets' => $this->get_name());
    }
}



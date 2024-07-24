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
 * This file contains the definition for the renderable classes for the ILSP submission plugin.
 *
 * @package assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A file class that extends the renderable class and is used by the ILSP plugin.
 *
 * @package assignsubmission_ilsp
 * @copyright 2019 Lancaster University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ilsp_files implements renderable {

    /** @var context $context */
    public context $context;
    /** @var array $dir */
    public array $dir;
    /** @var stdClass $cm course module */
    public stdClass $cm;
    /** @var stdClass $course */
    public stdClass $course;

    /**
     * The constructor.
     *
     * @param $ilsps
     */
    public function __construct($ilsps) {
        GLOBAL $CFG;
        $syscontext = context_system::instance();

        $this->dir['dirname'] = "";
        $this->dir['subdirs'] = [];

        $component = 'assignsubmission_ilsp';
        $filearea = 'assignsubmission_ilsp';

        foreach ($ilsps as $ilsp) {
            $file = $ilsp['file'];
            $name = $ilsp['name'];
            $path = '/' .
                $syscontext->id .
                '/' .
                $component .
                '/' .
                $filearea .
                '/' .
                $file->get_itemid() .
                $file->get_filepath() .
                $file->get_filename();
            $url = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false);
                file_encode_url("$CFG->wwwroot/pluginfile.php", $path, true);
            $file->fileurl = html_writer::link($url, $name, [
                'target' => '_blank',
            ]);
            $this->dir['files'][] = $file;
        }
    }

}

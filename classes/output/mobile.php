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

namespace assignsubmission_ilsp\output;

class mobile {

    /**
     * Returns shared (global) templates and information for the mobile app feature.
     *
     * @param array $args Arguments (empty)
     * @return array Array with information required by app
     */
    public static function assignsubmission_ilsp_view(array $args) : array {
        global $CFG, $OUTPUT;

        return [
            'templates' => [
                [
                    'id' => 'main',
                    'html' => $OUTPUT->render_from_template('assignsubmission_ilsp/mobile_view_ilsp', null),
                ],
            ],
            'javascript' => file_get_contents($CFG->dirroot . '/mod/assign/submission/ilsp/appjs/mobile_init.js'),
            'otherdata' => '',
            'files' => []
        ];
    }

}

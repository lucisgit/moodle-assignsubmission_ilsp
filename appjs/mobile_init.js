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
 * Initialize assignsubmission_ilsp with submission data in the mobile app.
 *
 * @package    assignsubmission_ilsp
 * @copyright  2021 Lancaster University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
var result = {
    componentInit: function() {
        var plugins = this.submission.plugins;
        setTimeout(function() {
            var template = document.getElementById('assignsubmission_ilsp_coversheets');
            if (template !== null) {
                // We don't want the ILSP template to display at all on the edit page.
                if (document.getElementsByTagName('page-addon-mod-assign-edit').length > 0) {
                    template.remove();
                } else {
                    plugins.forEach(function(plugin) {
                        if (plugin.type === 'ilsp') {
                            plugin.editorfields.forEach(function(field) {
                                if (field.name === 'ilsp') {
                                    template.querySelector('p').innerHTML = field.text;
                                }
                            });
                        }
                    });
                    // We also don't want to display the template if it's empty.
                    setTimeout(function() {
                        if (template.querySelector('p').innerHTML === '') {
                            template.remove();
                        }
                    });
                }
            }
        }, 500);
    },
    isEnabledForEdit: function() {
        return true;
    }
};

result;

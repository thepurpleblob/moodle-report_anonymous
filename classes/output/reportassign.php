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
 * Output class for assignment listing
 *
 * @package    report_anonymous
 * @copyright  2019 Howard Miller <howardsmiller@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_anonymous\output;

defined('MOODLE_INTERNAL') || die;

use renderable;
use renderer_base;
use templatable;
use context;
use context_course;

class reportassign implements renderable, templatable {

    protected $course;

    protected $context;

    protected $fullurl;

    protected $submissions;

    protected $assignment;

    public function __construct($course, $context, $fullurl, $submissions, $assignment) {
        $this->course = $course;
        $this->context = $context;
        $this->fullurl = $fullurl;
        $this->submissions = $submissions;
        $this->assignment = $assignment;
    }

    /**
     * Profile fields
     * @return array
     */
    protected function get_profilefields() {
        $fields = explode(',', get_config('report_anonymous', 'profilefields'));
        $profilefields = [];
        foreach ($fields as $field) {
            $profilefields[] = get_string($field);
        }

        return $profilefields;
    }

    public function export_for_template(renderer_base $output) {
        global $CFG;

        // Group mode?
        $cm = get_coursemodule_from_instance('assign', $this->assignment->id);
        $groupmode = $cm->groupmode;
        $groups = groups_get_all_groups($this->course->id);

        return [
            'canrevealnames' => has_capability('report/anonymous:shownames', $this->context) && $this->assignment->blindmarking,
            'canexport' => has_capability('report/anonymous:export', $this->context),
            'baseurl' => $this->fullurl,
            'submissions' => array_values($this->submissions),
            'assignment' => $this->assignment,
            'enableplagiarism' => !empty($CFG->enableplagiarism),
            'turnitinenabled' => \report_anonymous\lib::turnitin_enabled($this->assignment->id),
            'urkundenabled' => \report_anonymous\lib::urkund_enabled($this->assignment->id),
            'groupselect' => $groupmode != 0,
            'groups' => array_values($groups),
            'profilefields' => $this->get_profilefields(),
        ];
    }

}
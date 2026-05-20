<?php
namespace local_dailyreminder\task;

defined('MOODLE_INTERNAL') || die();

class send_daily_reminder extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('taskname', 'local_dailyreminder');
    }

    public function execute() {
        global $DB, $CFG;

        $enabled = get_config('local_dailyreminder', 'enabled');

        if (!$enabled) {
            mtrace('[Daily Reminder] Plugin is disabled.');
            return;
        }

        $sendhour = (int)get_config('local_dailyreminder', 'sendhour');
        $currenthour = (int)date('G');

        if ($sendhour < 0 || $sendhour > 23) {
            mtrace('[Daily Reminder] Invalid send hour setting.');
            return;
        }

        if ($currenthour !== $sendhour) {
            mtrace('[Daily Reminder] Not sending now. Current hour: ' . $currenthour . ', send hour: ' . $sendhour);
            return;
        }

        $today = date('Y-m-d');
        $lastsentdate = get_config('local_dailyreminder', 'lastsentdate');

        if ($lastsentdate === $today) {
            mtrace('[Daily Reminder] Reminder already sent today.');
            return;
        }

        $subject = get_config('local_dailyreminder', 'emailsubject');
        $bodytemplate = get_config('local_dailyreminder', 'emailbody');

        if (empty($subject)) {
            $subject = get_string('defaultsubject', 'local_dailyreminder');
        }

        if (empty($bodytemplate)) {
            $bodytemplate = get_string('defaultbody', 'local_dailyreminder');
        }

        $sql = "
            SELECT DISTINCT
                u.id,
                u.firstname,
                u.lastname,
                u.email,
                u.emailstop,
                u.deleted,
                u.suspended,
                u.mailformat,
                u.lang,
                u.timezone
            FROM {user} u
            JOIN {role_assignments} ra ON ra.userid = u.id
            JOIN {role} r ON r.id = ra.roleid
            JOIN {context} ctx ON ctx.id = ra.contextid
            WHERE u.deleted = 0
              AND u.suspended = 0
              AND u.emailstop = 0
              AND u.email <> ''
              AND r.shortname = :studentrole
              AND ctx.contextlevel = :coursecontext
            ORDER BY u.firstname ASC
        ";

        $params = [
            'studentrole' => 'student',
            'coursecontext' => CONTEXT_COURSE
        ];

        $students = $DB->get_records_sql($sql, $params);

        if (empty($students)) {
            mtrace('[Daily Reminder] No students found.');
            set_config('lastsentdate', $today, 'local_dailyreminder');
            return;
        }

        $fromuser = \core_user::get_noreply_user();
        $sentcount = 0;
        $failedcount = 0;

        foreach ($students as $student) {
            $messageplain = $this->replace_placeholders($bodytemplate, $student);
            $messagehtml = '<p>' . nl2br(s($messageplain)) . '</p>';

            $success = email_to_user(
                $student,
                $fromuser,
                $subject,
                $messageplain,
                $messagehtml
            );

            if ($success) {
                $sentcount++;
                mtrace('[Daily Reminder] Sent to: ' . fullname($student) . ' <' . $student->email . '>');
            } else {
                $failedcount++;
                mtrace('[Daily Reminder] Failed to send to: ' . fullname($student) . ' <' . $student->email . '>');
            }
        }

        set_config('lastsentdate', $today, 'local_dailyreminder');

        mtrace('[Daily Reminder] Finished.');
        mtrace('[Daily Reminder] Sent: ' . $sentcount);
        mtrace('[Daily Reminder] Failed: ' . $failedcount);
    }

    private function replace_placeholders($template, $user) {
        global $SITE;

        $fullname = fullname($user);
        $date = date('d-m-Y');

        $replace = [
            '{firstname}' => $user->firstname,
            '{lastname}' => $user->lastname,
            '{fullname}' => $fullname,
            '{date}' => $date,
            '{site}' => $SITE->fullname
        ];

        return strtr($template, $replace);
    }
}
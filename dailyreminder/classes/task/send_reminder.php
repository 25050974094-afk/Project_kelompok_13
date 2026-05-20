<?php
namespace local_dailyreminder\task;

defined('MOODLE_INTERNAL') || die();

class send_reminder extends \core\task\scheduled_task {

    public function get_name() {
        return get_string('sendreminder', 'local_dailyreminder');
    }

    public function execute() {
        global $DB;

        $users = $DB->get_records('user', ['deleted' => 0]);

        foreach ($users as $user) {

            if (isguestuser($user)) continue;

            $message = "Halo {$user->firstname}, jangan lupa belajar hari ini!";

            $eventdata = new \core\message\message();
            $eventdata->component = 'local_dailyreminder';
            $eventdata->name = 'reminder';
            $eventdata->userfrom = \core_user::get_noreply_user();
            $eventdata->userto = $user;
            $eventdata->subject = 'Reminder Belajar';
            $eventdata->fullmessage = $message;
            $eventdata->fullmessageformat = FORMAT_PLAIN;
            $eventdata->notification = 1;

            message_send($eventdata);
        }
    }
}
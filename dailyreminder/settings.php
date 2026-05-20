<?php
defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    $settings = new admin_settingpage(
        'local_dailyreminder',
        'Daily Learning Reminder'
    );

    $ADMIN->add('localplugins', $settings);

    $settings->add(new admin_setting_configcheckbox(
        'local_dailyreminder/enabled',
        'Enable daily reminder',
        'Enable or disable daily email reminder.',
        1
    ));

    $settings->add(new admin_setting_configtext(
        'local_dailyreminder/sendhour',
        'Send hour',
        'Set reminder hour using 24-hour format. Example: 7 or 19.',
        '19',
        PARAM_INT
    ));

    $settings->add(new admin_setting_configtext(
        'local_dailyreminder/emailsubject',
        'Email subject',
        'Subject for reminder email.',
        'Reminder Belajar Harian',
        PARAM_TEXT
    ));

    $settings->add(new admin_setting_configtextarea(
        'local_dailyreminder/emailbody',
        'Email body',
        'Use placeholders: {firstname}, {lastname}, {fullname}, {date}, {site}',
        "Halo {firstname},\n\nJangan lupa belajar hari ini di {site}.\nSilakan lanjutkan pembelajaranmu.\n\nTanggal: {date}\n\nTetap semangat!",
        PARAM_RAW
    ));
}
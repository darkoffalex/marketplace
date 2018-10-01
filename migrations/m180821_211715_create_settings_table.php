<?php

use yii\db\Migration;

/**
 * Handles the creation of table `settings`.
 */
class m180821_211715_create_settings_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('settings', [
            'id' => $this->primaryKey(),
            'name' => $this->string(),
            'value_text' => $this->text(),
            'value_numeric' => $this->integer(),
        ]);

        // Facebook default settings

        $this->insert('settings',[
            'name' => 'fb_auth_client_id',
            'value_text' => '303970500407028',
        ]);

        $this->insert('settings',[
            'name' => 'fb_auth_app_secret',
            'value_text' => '25483980ab16864505fd22076f9876b5',
        ]);

        // Notifications templates

        $this->insert('settings',[
            'name' => 'notification_template_advertisement_confirmation_fb',
            'value_text' => "Your advertisement's \"{name}\" (id - {id}) status was changed to \"{status}\"",
        ]);

        $this->insert('settings',[
            'name' => 'notification_template_advertisement_confirmation_email',
            'value_text' => "Your advertisement's \"{name}\" (id - {id}) status was changed to \"{status}\"",
        ]);

        $this->insert('settings',[
            'name' => 'notification_template_marketplace_confirmation_fb',
            'value_text' => "Your marketplace's request's {group_name} ({request_id}) status was changed to {status}",
        ]);

        $this->insert('settings',[
            'name' => 'notification_template_marketplace_confirmation_email',
            'value_text' => "Your marketplace's request's {group_name} ({request_id}) status was changed to {status}",
        ]);

        $this->insert('settings',[
            'name' => 'notification_template_new_advertisement_fb',
            'value_text' => "You have new advertisement to verify (id - {id})",
        ]);

        $this->insert('settings',[
            'name' => 'notification_template_new_advertisement_email',
            'value_text' => "You have new advertisement to verify (id - {id})",
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('settings');
    }
}

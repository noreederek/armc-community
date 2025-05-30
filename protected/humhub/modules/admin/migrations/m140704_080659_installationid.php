<?php



use humhub\components\Migration;

class m140704_080659_installationid extends Migration
{
    public function up()
    {
        if (!$this->isInitialInstallation()) {
            $this->insert('setting', [
                'name' => 'installationId',
                'value' => md5(uniqid("", true)),
                'module_id' => 'admin',
            ]);
        }
    }

    public function down()
    {
        echo "m140704_080659_installationid does not support migration down.\n";

        return false;
    }
}

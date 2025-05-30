<?php



use humhub\components\Migration;

/**
 * Class uninstall
 */
class uninstall extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // enable output
        $this->compact = false;

        $this->safeDropTable('test_module_with_migration');

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "uninstall cannot be reverted.\n";

        return false;
    }
}

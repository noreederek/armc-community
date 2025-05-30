<?php



namespace humhub\commands;

use humhub\components\SettingsManager;
use humhub\models\Setting;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;

/**
 * SettingsController provides CLI access to database settings
 *
 * @since 1.4
 * @author Luke
 */
class SettingsController extends Controller
{
    /**
     * Lists all stored settings
     *
     * @return int the exit code
     * @throws \Exception
     */
    public function actionList()
    {
        $this->stdout("\n*** Showing all stored settings\n\n", Console::FG_GREY);

        $settings = [];

        foreach (Setting::find()->all() as $setting) {
            $settings[] = [$setting->name, $setting->value, $setting->module_id];
        }

        echo Table::widget(['headers' => ['Name', 'Value', 'Module ID'], 'rows' => $settings]);

        return ExitCode::OK;

    }


    /**
     * Lists stored settings by given module id
     *
     * @param string $moduleId
     * @return int the exit code
     * @throws \Exception
     */
    public function actionListModule($moduleId)
    {

        $module = $this->ansiFormat($moduleId, Console::FG_YELLOW);
        $this->stdout("\n*** Showing settings for module: $module\n\n", Console::FG_GREY);

        $settings = [];

        foreach (Setting::find()->andWhere(['module_id' => $moduleId])->all() as $setting) {
            $settings[] = [$setting->name, $setting->value];
        }

        echo Table::widget(['headers' => ['Name', 'Value'], 'rows' => $settings]);

        return ExitCode::OK;

    }


    /**
     * Adds or updates a stored setting
     *
     * @param string $moduleId
     * @param string $name
     * @param $value
     * @return int the exit code
     */
    public function actionSet($moduleId, $name, $value)
    {
        $settingsManager = new SettingsManager(['moduleId' => $moduleId]);
        $settingsManager->set($name, $value);

        $this->stdout("\n*** Successfully set setting\n\n", Console::FG_GREEN);
        $this->stdout("Name:\t\t" . $name . "\n");
        $this->stdout("Module ID:\t" . $moduleId . "\n\n");
        $this->stdout("Value:\t\t" . $value . "\n");

        return ExitCode::OK;
    }

    /**
     * Deletes a stored setting
     *
     * @param string $moduleId
     * @param string $name
     * @return int the exit code
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($moduleId, $name)
    {

        $settingsManager = new SettingsManager(['moduleId' => $moduleId]);
        $settingsManager->delete($name);

        $this->stdout("\n*** Successfully deleted setting\n\n", Console::FG_GREEN);
        $this->stdout("Name:\t\t" . $name . "\n");
        $this->stdout("Module ID:\t" . $moduleId . "\n\n");

        return ExitCode::OK;
    }
}

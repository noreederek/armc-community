<?php



namespace humhub\components\bootstrap;

use humhub\components\Application;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * ModuleAutoLoader automatically searches for config.php files in module folder an executes them.
 *
 * @author luke
 */
class ModuleAutoLoader implements BootstrapInterface
{
    public const CACHE_ID = 'module_configs';
    public const CONFIGURATION_FILE = 'config.php';

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param Application $app the application currently running
     *
     * @throws InvalidConfigException Module a configuration does not have both an id and class attribute
     * @throws ErrorException On invalid module autoload path
     */
    public function bootstrap($app)
    {
        $modules = self::locateModules();
        Yii::$app->moduleManager->registerBulk($modules);
    }

    /**
     * Find available modules
     *
     * @return array[] Array of module configurations with module ID as index.
     *          Read from cache if available and YII_DEBUG is disabled
     * @throws ErrorException On invalid module autoload path
     */
    public static function locateModules(): array
    {
        $modules = Yii::$app->cache->get(self::CACHE_ID);

        if ($modules === false || YII_DEBUG) {
            $modules = static::findModules(Yii::$app->params['moduleAutoloadPaths']);
            Yii::$app->cache->set(self::CACHE_ID, $modules);
        }

        return $modules;
    }

    /**
     * Find all modules with configured paths
     *
     * @param string[] $paths
     *
     * @return array[] Array of module configurations with module ID as index
     * @throws ErrorException On invalid module autoload path
     */
    private static function findModules(iterable $paths): array
    {
        $folders = [];
        foreach ($paths as $path) {
            try {
                $folders = array_merge($folders, self::findModulesByPath($path));
            } catch (InvalidArgumentException $ex) {
                throw new ErrorException('Invalid module autoload path: ' . $path);
            }
        }

        $modules = [];
        $moduleIdFolders = [];
        $preventDuplicatedModules = Yii::$app->moduleManager->preventDuplicatedModules;

        foreach ($folders as $folder) {
            try {
                $moduleConfig = static::getModuleConfigByPath($folder);
                if ($preventDuplicatedModules && isset($moduleIdFolders[$moduleConfig['id']])) {
                    Yii::error('Duplicated module "' . $moduleConfig['id'] . '"(' . $folder . ') is already loaded from the folder "' . $moduleIdFolders[$moduleConfig['id']] . '"');
                } else {
                    $modules[$folder] = $moduleConfig;
                    $moduleIdFolders[$moduleConfig['id']] = $folder;
                }
            } catch (\Throwable $e) {
                Yii::error($e);
            }
        }

        if ($preventDuplicatedModules) {
            // Overwrite module paths from config
            foreach (Yii::$app->moduleManager->overwriteModuleBasePath as $overwriteModuleId => $overwriteModulePath) {
                if (isset($moduleIdFolders[$overwriteModuleId]) && $moduleIdFolders[$overwriteModuleId] !== $overwriteModulePath) {
                    try {
                        $moduleConfig = static::getModuleConfigByPath($overwriteModulePath);

                        Yii::info('Overwrite path of the module "' . $overwriteModuleId . '" to the folder "' . $overwriteModulePath . '"');
                        // Remove original config
                        unset($modules[$moduleIdFolders[$overwriteModuleId]]);
                        // Use config from the overwritten path
                        $modules[$overwriteModulePath] = $moduleConfig;
                        $moduleIdFolders[$overwriteModuleId] = $overwriteModulePath;
                    } catch (\Throwable $e) {
                        Yii::error($e);
                    }
                }
            }
        }

        return $modules;
    }

    private static function getModuleConfigByPath(string $modulePath): array
    {
        return include $modulePath . DIRECTORY_SEPARATOR . self::CONFIGURATION_FILE;
    }


    /**
     * Find all directories with a configuration file inside
     *
     * @param string $path
     *
     * @return string[]
     * @throws InvalidArgumentException
     */
    private static function findModulesByPath(string $path): array
    {
        $hasConfigurationFile = static function ($path) {
            return is_file($path . DIRECTORY_SEPARATOR . self::CONFIGURATION_FILE);
        };

        return FileHelper::findDirectories(
            Yii::getAlias($path, true),
            ['filter' => $hasConfigurationFile, 'recursive' => false],
        );
    }
}

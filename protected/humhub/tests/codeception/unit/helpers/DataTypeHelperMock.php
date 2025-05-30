<?php



/**
 * @noinspection PhpIllegalPsrClassPathInspection
 */

namespace humhub\tests\codeception\unit\helpers;

use humhub\helpers\DataTypeHelper;

/**
 * Class DataTypeHelperTest
 */
class DataTypeHelperMock extends DataTypeHelper
{
    public static function matchTypeHelper($typeToCheck, &$input, string $inputType, ?array &$inputTraits = null): ?string
    {
        return parent::matchTypeHelper($typeToCheck, $input, $inputType, $inputTraits);
    }

    public static function parseTypes(&$allowedTypes, ?bool &$allowNull = false, ?array &$checkTraits = null, bool $allowCallables = true, bool $allowGetTypes = true): array
    {
        return parent::parseTypes($allowedTypes, $allowNull, $checkTraits, $allowCallables, $allowGetTypes);
    }
}

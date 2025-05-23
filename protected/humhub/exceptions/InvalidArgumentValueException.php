<?php



namespace humhub\exceptions;

use yii\base\InvalidArgumentException as BaseInvalidArgumentException;

/**
 * @since 1.15
 */
class InvalidArgumentValueException extends BaseInvalidArgumentException
{
    use InvalidArgumentExceptionTrait;
}

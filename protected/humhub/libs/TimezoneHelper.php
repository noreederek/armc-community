<?php



namespace humhub\libs;

use DateTime;
use DateTimeZone;
use Yii;
use yii\db\Exception;

/**
 * TimezoneHelpers
 *
 * @author luke
 */
class TimezoneHelper
{
    /**
     *
     * // Modified version of the timezone list function from http://stackoverflow.com/a/17355238/507629
     * // Includes current time for each timezone (would help users who don't know what their timezone is)
     *
     * @staticvar array $regions
     * @param bool $includeUTC whether or not to include UTC timeZone
     * @param bool $withOffset whether or not to add offset information
     * @return array
     * @throws \Exception
     */
    public static function generateList($includeUTC = false, $withOffset = true)
    {
        $regions = [
            DateTimeZone::AFRICA,
            DateTimeZone::AMERICA,
            DateTimeZone::ANTARCTICA,
            DateTimeZone::ASIA,
            DateTimeZone::ATLANTIC,
            DateTimeZone::AUSTRALIA,
            DateTimeZone::EUROPE,
            DateTimeZone::INDIAN,
            DateTimeZone::PACIFIC,
        ];

        if ($includeUTC) {
            $regions[] = DateTimeZone::UTC;
        }

        $timezones = [];
        foreach ($regions as $region) {
            $timezones = array_merge($timezones, DateTimeZone::listIdentifiers($region));
        }

        $timezone_offsets = [];
        foreach ($timezones as $timezone) {
            $tz = new DateTimeZone($timezone);
            $timezone_offsets[$timezone] = $tz->getOffset(new DateTime());
        }

        // sort timezone by timezone name
        asort($timezone_offsets);

        $timezone_list = [];

        foreach ($timezone_offsets as $timezone => $offset) {
            if ($withOffset) {
                $offset_prefix = $offset < 0 ? '-' : '+';
                $offset_formatted = gmdate('H:i', abs($offset));
                $pretty_offset = 'UTC' . $offset_prefix . $offset_formatted;
                $timezone_list[$timezone] = $pretty_offset . ' - ' . $timezone;
            } else {
                $timezone_list[$timezone] = $timezone;
            }
        }

        return $timezone_list;
    }

    /**
     * Returns the date time from the database connection
     *
     * @return DateTime
     * @deprecated since 1.17 because it is not used anymore
     */
    public static function getDatabaseConnectionTime(): DateTime
    {
        $timestamp = Yii::$app->db->createCommand('SELECT NOW()')->queryScalar();
        return DateTime::createFromFormat("Y-m-d H:i:s", $timestamp);
    }

    /**
     * Get a time value from time zone title
     *
     * @param string $timeZone
     * @return string
     * @since v1.17
     */
    public static function convertToTime(string $timeZone): string
    {
        try {
            $offset = (new DateTimeZone($timeZone))->getOffset(new DateTime());
            $offset_prefix = $offset < 0 ? '-' : '+';
            return $offset_prefix . gmdate('G:i', abs($offset));
        } catch (\Exception $e) {
            Yii::error('Wrong time zone: ' . $e->getMessage());
            return '+0:00';
        }
    }
}

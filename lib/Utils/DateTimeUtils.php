<?php

declare(strict_types=1);

namespace Netgen\BlockManager\Utils;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;

final class DateTimeUtils
{
    /**
     * Creates a new \DateTimeImmutable instance from provided timestamp and timezone identifiers.
     *
     * Current timestamp and timezones are used if not provided.
     */
    public static function createFromTimestamp(?int $timestamp = null, ?string $timeZone = null): DateTimeInterface
    {
        $dateTimeZone = is_string($timeZone) ? new DateTimeZone($timeZone) : null;
        $timestamp = is_int($timestamp) ? $timestamp : time();

        $dateTime = new DateTimeImmutable('now', $dateTimeZone);

        return $dateTime->setTimestamp($timestamp);
    }

    /**
     * Creates a new \DateTimeImmutable instance from provided array.
     *
     * The array needs to contain two keys with string values:
     * 1) "datetime" - for date & time in one of the valid formats
     * 2) "timezone" - a valid timezone identifier
     *
     * Returns null if provided array is not of valid format.
     */
    public static function createFromArray(array $datetime): ?DateTimeInterface
    {
        if (empty($datetime['datetime']) || !is_string($datetime['datetime'])) {
            return null;
        }

        if (empty($datetime['timezone']) || !is_string($datetime['timezone'])) {
            return null;
        }

        return new DateTimeImmutable($datetime['datetime'], new DateTimeZone($datetime['timezone']));
    }

    /**
     * Returns if the provided DateTime instance is between the provided dates.
     */
    public static function isBetweenDates(?DateTimeInterface $date = null, ?DateTimeInterface $from = null, ?DateTimeInterface $to = null): bool
    {
        $date = $date ?? self::createFromTimestamp();

        if ($from instanceof DateTimeInterface && $date < $from) {
            return false;
        }

        if ($to instanceof DateTimeInterface && $date > $to) {
            return false;
        }

        return true;
    }

    /**
     * Returns the formatted list of all timezones, separated by regions.
     */
    public static function getTimeZoneList(): array
    {
        $timeZoneList = [];
        foreach (DateTimeZone::listIdentifiers() as $timeZone) {
            list($region, $name) = self::parseTimeZone($timeZone);

            $offset = self::buildOffsetString($timeZone);
            $name = sprintf('%s (%s)', str_replace('_', ' ', $name), $offset);

            $timeZoneList[$region][$name] = $timeZone;
        }

        return $timeZoneList;
    }

    /**
     * Returns the array with human readable region and timezone name for the provided
     * timezone identifier.
     */
    private static function parseTimeZone(string $timeZone): array
    {
        $parts = explode('/', $timeZone);

        if (count($parts) > 2) {
            return [$parts[0], $parts[1] . ' / ' . $parts[2]];
        }

        if (count($parts) > 1) {
            return [$parts[0], $parts[1]];
        }

        return ['Other', $parts[0]];
    }

    /**
     * Returns the formatted UTC offset for the provided timezone identifier
     * in the form of (+/-)HH:mm.
     */
    private static function buildOffsetString(string $timeZone): string
    {
        $offset = self::createFromTimestamp(null, $timeZone)->getOffset();

        $hours = intdiv($offset, 3600);
        $minutes = intdiv($offset % 3600, 60);

        return sprintf('%s%02d:%02d', $offset >= 0 ? '+' : '-', abs($hours), abs($minutes));
    }
}

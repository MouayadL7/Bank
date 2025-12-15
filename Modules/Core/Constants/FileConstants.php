<?php

declare(strict_types=1);

namespace Modules\Core\Constants;

/**
 * Constants for file-related configurations
 */
final class FileConstants
{
    // Base units in bytes
    private const KB = 1024;

    private const MB = self::KB * 1024;

    private const GB = self::MB * 1024;

    // Maximum file sizes
    public const MAX_SIZE_1MB = self::MB;

    public const MAX_SIZE_2MB = self::MB * 2;

    public const MAX_SIZE_5MB = self::MB * 5;

    public const MAX_SIZE_10MB = self::MB * 10;

    public const MAX_SIZE_20MB = self::MB * 20;

    public const MAX_SIZE_50MB = self::MB * 50;

    public const MAX_SIZE_100MB = self::MB * 100;

    // Document file sizes
    public const MAX_SIZE_DOC_DEFAULT = self::MB * 10;

    public const MAX_SIZE_PDF = self::MB * 20;

    public const MAX_SIZE_PRESENTATION = self::MB * 50;

    /**
     * Convert size in megabytes to bytes
     */
    public static function mbToBytes(int $mb): int
    {
        return $mb * self::MB;
    }

    /**
     * Convert size in kilobytes to bytes
     */
    public static function kbToBytes(int $kb): int
    {
        return $kb * self::KB;
    }

    /**
     * Convert size in gigabytes to bytes
     */
    public static function gbToBytes(int $gb): int
    {
        return $gb * self::GB;
    }

    /**
     * Convert bytes to human readable format
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), $precision).' '.$units[$pow];
    }

    /**
     * Convert bytes to kilobytes (for Laravel validation)
     */
    public static function toKilobytes(int $bytes): int
    {
        return (int) ceil($bytes / self::KB);
    }
}

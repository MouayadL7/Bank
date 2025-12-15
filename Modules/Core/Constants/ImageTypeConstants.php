<?php

declare(strict_types=1);

namespace Modules\Core\Constants;

/**
 * Constants for image types (mime types and extensions)
 */
final class ImageTypeConstants
{
    // Mime types
    public const MIME_JPEG = 'image/jpeg';

    public const MIME_PNG = 'image/png';

    public const MIME_GIF = 'image/gif';

    public const MIME_SVG = 'image/svg+xml';

    public const MIME_WEBP = 'image/webp';

    public const MIME_TYPES_ALL = [
        self::MIME_JPEG,
        self::MIME_PNG,
        self::MIME_GIF,
        self::MIME_SVG,
        self::MIME_WEBP,
    ];

    public const MIME_TYPES_PHOTO = [
        self::MIME_JPEG,
        self::MIME_PNG,
        self::MIME_WEBP,
    ];

    public const MIME_TYPES_VECTOR = [
        self::MIME_SVG,
    ];

    // File extensions
    public const EXT_JPG = 'jpg';

    public const EXT_JPEG = 'jpeg';

    public const EXT_PNG = 'png';

    public const EXT_GIF = 'gif';

    public const EXT_SVG = 'svg';

    public const EXT_WEBP = 'webp';

    public const EXTENSIONS_ALL = [
        self::EXT_JPG,
        self::EXT_JPEG,
        self::EXT_PNG,
        self::EXT_GIF,
        self::EXT_SVG,
        self::EXT_WEBP,
    ];

    public const EXTENSIONS_PHOTO = [
        self::EXT_JPG,
        self::EXT_JPEG,
        self::EXT_PNG,
        self::EXT_WEBP,
    ];

    public const EXTENSIONS_VECTOR = [
        self::EXT_SVG,
    ];

    /**
     * Get mime types as comma-separated string
     */
    public static function getMimeTypesString(?array $mimeTypes = null): string
    {
        return implode(',', $mimeTypes ?? self::MIME_TYPES_ALL);
    }

    /**
     * Get extensions as comma-separated string
     */
    public static function getExtensionsString(?array $extensions = null): string
    {
        return implode(',', $extensions ?? self::EXTENSIONS_ALL);
    }

    /**
     * Get mime type for extension
     */
    public static function getMimeTypeForExtension(string $extension): ?string
    {
        $map = [
            self::EXT_JPG => self::MIME_JPEG,
            self::EXT_JPEG => self::MIME_JPEG,
            self::EXT_PNG => self::MIME_PNG,
            self::EXT_GIF => self::MIME_GIF,
            self::EXT_SVG => self::MIME_SVG,
            self::EXT_WEBP => self::MIME_WEBP,
        ];

        return $map[strtolower($extension)] ?? null;
    }

    /**
     * Get extension for mime type
     */
    public static function getExtensionForMimeType(string $mimeType): ?string
    {
        $map = [
            self::MIME_JPEG => self::EXT_JPEG,
            self::MIME_PNG => self::EXT_PNG,
            self::MIME_GIF => self::EXT_GIF,
            self::MIME_SVG => self::EXT_SVG,
            self::MIME_WEBP => self::EXT_WEBP,
        ];

        return $map[strtolower($mimeType)] ?? null;
    }
}

<?php

namespace Modules\Shared\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    /**
     * Get all translations relation.
     */
    public function translations(): HasMany
    {
        $translationModel = $this->getTranslationModel();
        return $this->hasMany($translationModel)
                    ->where('locale', app()->getLocale());
    }

    /**
     * Get a translation for a specific locale (or current app locale).
     */
    public function translation(?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        // If loaded, use in-memory collection (no extra DB queries)
        if ($this->relationLoaded('translations')) {
            return $this->translations->first();
        }

        // Otherwise, fallback to lazy-load one translation
        return $this->translations()->where('locale', $locale)->first();
    }

    /**
     * Return the fully-qualified translation model class name.
     * Must be implemented in the using model.
     */
    abstract protected function getTranslationModel(): string;
}

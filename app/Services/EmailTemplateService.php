<?php

namespace App\Services;

use App\Models\EmailTemplate;

class EmailTemplateService
{
    public function render(string $code, array $replacements, array $fallback): array
    {
        $template = EmailTemplate::query()
            ->where('code', $code)
            ->where('is_active', true)
            ->first();

        if (! $template) {
            return $fallback;
        }

        return $template->render($replacements);
    }
}

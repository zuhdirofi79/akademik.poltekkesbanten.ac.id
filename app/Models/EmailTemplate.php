<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'code',
        'name',
        'subject',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function render(array $replacements): array
    {
        $subject = $this->subject;
        $body = $this->body;

        foreach ($replacements as $key => $value) {
            $subject = str_replace('{{'.$key.'}}', (string) $value, $subject);
            $body = str_replace('{{'.$key.'}}', (string) $value, $body);
        }

        return [
            'subject' => $subject,
            'body' => $body,
        ];
    }
}

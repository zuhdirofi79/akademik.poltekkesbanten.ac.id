<?php

namespace App\Services;

class WhatsAppNormalizer
{
    public function normalize(?string $number): string
    {
        $digits = preg_replace('/\D+/', '', (string) $number);

        if (str_starts_with($digits, '62')) {
            return $digits;
        }

        if (str_starts_with($digits, '0')) {
            return '62'.substr($digits, 1);
        }

        return '62'.$digits;
    }

    public function waLink(string $number, string $message): string
    {
        return 'https://wa.me/'.$this->normalize($number).'?text='.urlencode($message);
    }
}

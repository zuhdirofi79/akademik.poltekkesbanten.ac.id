<?php

namespace App\Enums;

enum UploadedBy: string
{
    case Applicant = 'applicant';
    case Admin = 'admin';
}

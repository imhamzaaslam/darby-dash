<?php

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'Super Admin';
    case ADMIN = 'Admin';
    case PROJECT_MANAGER = 'Project Manager';
    case CLIENT = 'Client User';
    case STAFF = 'Staff';
    case DEVELOPER = 'Developer';
    case DESIGNER = 'Designer';
}

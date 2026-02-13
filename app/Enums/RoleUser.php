<?php

namespace App\Enums;

use App\Traits\EnumsToArray;

enum RoleUser:string {

    use EnumsToArray;
    case SUPER = "super";
    case ADMIN = "admin";
    case USER = "user";
    case PETUGAS = "petugas";
    case PPOB = "ppob";
    case PENAGIH = "penagih";
    case PEMBACA = "pembaca";
    case GUEST = "guest";   
    case ALL_ROLES = "all_roles";


    public function getDescription() : string
    {
        return match ($this) {
            self::SUPER => "Super Admin",
            self::ADMIN => "Administrator",
            self::USER => "Basic User",
            self::PETUGAS => "Petugas",
            self::PPOB => "PPOB",
            self::PENAGIH => "Penagih",
            self::PEMBACA => "Pembaca",
            self::GUEST => "Guest",
            self::ALL_ROLES => "All Roles",
        };
    }
}

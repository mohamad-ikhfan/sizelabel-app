<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Sushi\Sushi;

class CalendarHolidayId extends Model
{
    use Sushi;

    public function getRows()
    {
        return [
            [
                "date" => "2024-01-01",
                "localName" => "Tahun Baru Masehi",
                "name" => "New Year's Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-03-29",
                "localName" => "Wafat Isa Almasih",
                "name" => "Good Friday",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-03-31",
                "localName" => "Paskah",
                "name" => "Easter Sunday",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-05-01",
                "localName" => "Hari Buruh Internasional",
                "name" => "Labour Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-05-09",
                "localName" => "Kenaikan Isa Almasih",
                "name" => "Ascension Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-06-01",
                "localName" => "Hari Lahir Pancasila",
                "name" => "Pancasila Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-08-17",
                "localName" => "Hari Ulang Tahun Kemerdekaan Republik Indonesia",
                "name" => "Independence Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ],
            [
                "date" => "2024-12-25",
                "localName" => "Hari Raya Natal",
                "name" => "Christmas Day",
                "countryCode" => "ID",
                "fixed" => false,
                "global" => true,
            ]
        ];
    }
}

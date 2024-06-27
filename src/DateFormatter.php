<?php

namespace GlpiPlugin\Fillglpi;

class DateFormatter {
    public static function formatToBr($date) {
        $formatedDate = explode(" ", $date);
        $formatedDate[0] = implode('/', array_reverse(explode('-', $formatedDate[0])));  
        $formatedDate[1] = implode(':', array_slice(explode(':', $formatedDate[1]), 0, 2));    
        $formatedDate = implode('  ', $formatedDate);

        return $formatedDate;
    }
}
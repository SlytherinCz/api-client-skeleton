<?php

namespace SlytherinCz\ApiClient\Service;

class DateFormatter
{
    /**
     * @param \DateTime $date
     * @return string|null
     */
    public static function getJavascriptDateString(?\DateTimeInterface $date): ?string
    {
        if(is_null($date)) {
            return $date;
        }
        $copy = clone $date;
        $copy->setTimezone(new \DateTimeZone("UTC"));
        return $copy ? str_replace("+00:00",'Z',$copy->format(\DateTime::RFC3339_EXTENDED)) : null;
    }

    /**
     * @param \DateTime $date
     * @return string|null
     */
    public static function getDateTimeFromJavascriptDateString(string $date): \DateTime
    {
        return\DateTime::createFromFormat(DATE_RFC3339_EXTENDED, str_replace('Z', '+00:00', $date));
    }
}
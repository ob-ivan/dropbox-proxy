<?php
namespace Ob_Ivan\Cache\Driver;

use DateTime;

trait ExpiryTrait
{
    protected function normalizeExpiry($expiry)
    {
        if ($expiry instanceof DateTime) {
            return $expiry;
        }
        if (! $expiry) {
            return new DateTime('+1000years');
        }
        if (is_numeric($expiry)) {
            return new DateTime(intval($expiry) . 'sec');
        }
        throw new Exception('Unknown expiry format. Integer value or DateTime object was expected.');
    }

    protected function isExpired($expiry)
    {
        return $this->normalizeExpiry($expiry) < (new DateTime);
    }
}

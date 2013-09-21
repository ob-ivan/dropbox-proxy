<?php
/**
 * TODO: Eliminate code duplication with MemcacheDriver on key normalization.
**/
namespace Ob_Ivan\Cache\Driver;

use DateTime;
use Exception as AnyException;
use Ob_Ivan\Cache\StorageInterface;

class FileDriver implements StorageInterface
{
    const DATE_FORMAT       = 'Y-m-d H:i:s';
    const EXPIRY_SEPARATOR  = '#';
    const FILE_EXTENSION    = '.txt';
    const MAX_KEY_LENGTH    = 100;
    const NORMALIZE_PREFIX  = 'n';

    protected $normalizedKeys = [];
    protected $params;

    /**
     *  @param  [
     *      'file_prefix' => <string Absolute path and a file prefix for storage files, e.g. '/cache/file_'>,
     *  ]   $params
    **/
    public function __construct($params)
    {
        $this->params = $params;
    }

    // public : StorageInterface //

    public function delete($key)
    {
        $this->unlink($this->getFileName($key));
    }

    public function get($key)
    {
        $filename = $this->getFileName($key);
        if (! is_readable($filename)) {
            return null;
        }
        $contents = file_get_contents($filename);
        $separatorPos = strpos($contents, static::EXPIRY_SEPARATOR);
        if (false === $separatorPos) {
            $this->unlink($filename);
            return null;
        }
        $serializedExpiry = substr($contents, 0, $separatorPos);
        $expiry = DateTime::createFromFormat(static::DATE_FORMAT, $serializedExpiry);
        if (! $expiry instanceof DateTime) {
            $this->unlink($filename);
            return null;
        }
        if ($expiry < (new DateTime)) {
            $this->unlink($filename);
            return null;
        }
        $serializedValue = substr($contents, $separatorPos + 1);
        try {
            $value = unserialize($serializedValue);
        } catch (AnyException $e) {
            $this->unlink($filename);
            return null;
        }
        return $value;
    }

    public function set($key, $value, $duration = null)
    {
        $filename = $this->getFileName($key);
        if (! is_writable($filename)) {
            mkdir(dirname($filename), 0777, true);
        }
        $writtenBytes = file_put_contents(
            $filename,
            implode(static::EXPIRY_SEPARATOR, [
                $this->getExpiry($duration)->format(static::DATE_FORMAT),
                serialize($value),
            ]),
            LOCK_EX
        );
        return $writtenBytes !== false;
    }

    // protected //

    // TODO: Eliminate code reduplication with MemoryDriver.
    protected function getExpiry($duration = null)
    {
        return new DateTime(
            $duration > 0
            ? '+' . intval($duration) . 'sec'
            : '+10000years'
        );
    }

    protected function getFileName($key)
    {
        return implode('', [
            $this->params['file_prefix'],
            $this->normalizeKey($key),
            static::FILE_EXTENSION
        ]);
    }

    protected function normalizeKey($key)
    {
        if (! isset($this->normalizedKeys[$key])) {
            $this->normalizedKeys[$key] = strlen($key) > static::MAX_KEY_LENGTH
                ? static::NORMALIZE_PREFIX . md5($key)
                : $key;
        }
        return $this->normalizedKeys[$key];
    }

    protected function unlink($filename)
    {
        return unlink($filename);
    }
}

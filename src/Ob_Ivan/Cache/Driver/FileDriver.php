<?php
namespace Ob_Ivan\Cache\Driver;

use DateTime;
use Exception as AnyException;
use Ob_Ivan\Cache\StorageInterface;

class FileDriver implements StorageInterface
{
    use ExpiryTrait;

    const DATE_FORMAT       = 'Y-m-d H:i:s';
    const EXPIRY_SEPARATOR  = '#';
    const FILE_EXTENSION    = '.txt';

    /**
     *  @var [<string key> => <string filename>]
    **/
    protected $filenames = [];

    protected $params;

    /**
     *  @param  [
     *      'cache_dir' => <string Absolute path to directory, e.g. '/www/myproject/cache/'>,
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
        if ($this->isExpired($expiry)) {
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

    public function set($key, $value, $expiry = null)
    {
        if ($this->isExpired($expiry)) {
            return false;
        }
        $filename = $this->getFileName($key);
        $dirname = dirname($filename);
        if (! is_dir($dirname)) {
            mkdir($dirname, 0777, true);
        }
        $writtenBytes = file_put_contents(
            $filename,
            implode(static::EXPIRY_SEPARATOR, [
                $this->normalizeExpiry($expiry)->format(static::DATE_FORMAT),
                serialize($value),
            ]),
            LOCK_EX
        );
        return $writtenBytes !== false;
    }

    // protected //

    /**
     * Hashes key to make sure it will not contain any special chars.
     *
     * Also divides it into 3-level subfolders.
    **/
    protected function getFileName($key)
    {
        if (! isset($this->filenames[$key])) {
            $hash = md5($key);
            $this->filenames[$key] = implode(DIRECTORY_SEPARATOR, [
                rtrim($this->params['cache_dir'], ' /\\'),
                $hash[0],
                $hash[1],
                $hash[2],
                $hash . static::FILE_EXTENSION,
            ]);
        }
        return $this->filenames[$key];
    }

    protected function unlink($filename)
    {
        return unlink($filename);
    }
}

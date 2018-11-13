<?php

/**
 * 文件缓存类
 */
class W2FileCache
{
    /**
     * @var string a string prefixed to every cache key. This is needed when you store
     * cache data under the same [[cachePath]] for different applications to avoid
     * conflict.
     *
     * To ensure interoperability, only alphanumeric characters should be used.
     */
    public $keyPrefix = 'fc';
    /**
     * @var string the directory to store cache files. You may use path alias here.
     * If not set, it will use the "cache" subdirectory under the application runtime path.
     */
    public $cachePath = '';
    /**
     * @var string cache file suffix. Defaults to '.bin'.
     */
    public $cacheFileSuffix = '.bin';
    /**
     * @var integer the level of sub-directories to store cache files. Defaults to 1.
     * If the system has huge number of cache files (e.g. one million), you may use a bigger value
     * (usually no bigger than 3). Using sub-directories is mainly to ensure the file system
     * is not over burdened with a single directory having too many files.
     */
    public $directoryLevel = 1;
    /**
     * @var integer the probability (parts per million) that garbage collection (GC) should be performed
     * when storing a piece of data in the cache. Defaults to 10, meaning 0.001% chance.
     * This number should be between 0 and 1000000. A value 0 means no GC will be performed at all.
     */
    public $gcProbability = 10;
    /**
     * @var integer the permission to be set for newly created cache files.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * If not set, the permission will be determined by the current environment.
     */
    public $fileMode;
    /**
     * @var integer the permission to be set for newly created directories.
     * This value will be used by PHP chmod() function. No umask will be applied.
     * Defaults to 0775, meaning the directory is read-writable by owner and group,
     * but read-only for other users.
     */
    public $dirMode = 0775;

    private static $_instance = null;

    /**
     * 得到本类实例
     *
     * @return Ambiguous
     */
    public static function getInstance()
    {
        if(self::$_instance === null)
        {
            self::$_instance = new self();
        }
        // D(self::$_instance);
        // exit;
        return self::$_instance;
    }

    /**
     * 初始化
    */
    function __construct()
    {
        // $this->cachePath = HT::$cacheRoot.'htcache';
        // $this->cachePath = '/data/web/njart/devapi/logs/cache/';
        $this->cachePath = W2LOG_PATH .'cache/';
    }

    function FileCache()
    {
        $this->__construct();
    }

    /**
     * Stores a value identified by a key into cache.
     * If the cache already contains such a key, the existing value and
     * expiration time will be replaced with the new ones, respectively.
     *
     * @param mixed $key a key identifying the value to be cached. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @param mixed $value the value to be cached
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * the corresponding value in the cache will be invalidated when it is fetched via [[get()]].
     * This parameter is ignored if [[serializer]] is false.
     * @return boolean whether the value is successfully stored into cache
     */
    public function set($key, $value, $duration = 0)
    {
        $value = serialize([$value]);
        $key = $this->buildKey($key);
        return $this->setValue($key, $value, $duration);
    }
    public static function setKey($key, $value, $duration = 0)
    {
        return static::getInstance()->set($key,$value, $duration);
    }

    /**
     * Retrieves a value from cache with a specified key.
     * @param mixed $key a key identifying the cached value. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @return mixed the value stored in cache, false if the value is not in the cache, expired,
     * or the dependency associated with the cached data has changed.
     */
    public function get($key)
    {
        $key = $this->buildKey($key);
        $value = $this->getValue($key);
        if ($value === null) {
            return $value;
        } else {
            $value = unserialize($value);
        }
        if (is_array($value)) {
            return $value[0];
        } else {
            return null;
        }
    }
    public static function getKey($key)
    {
        return static::getInstance()->get($key);
    }

    /**
     * Deletes a value with the specified key from cache
     * @param mixed $key a key identifying the value to be deleted from cache. This can be a simple string or
     * a complex data structure consisting of factors representing the key.
     * @return boolean if no error happens during deletion
     */
    public function delete($key)
    {
        $key = $this->buildKey($key);

        return $this->deleteValue($key);
    }

    /**
     * Builds a normalized cache key from a given key.
     *
     * If the given key is a string containing alphanumeric characters only and no more than 32 characters,
     * then the key will be returned back prefixed with [[keyPrefix]]. Otherwise, a normalized key
     * is generated by serializing the given key, applying MD5 hashing, and prefixing with [[keyPrefix]].
     *
     * @param mixed $key the key to be normalized
     * @return string the generated cache key
     */
    public function buildKey($key)
    {
        if (is_string($key)) {
            $key = ctype_alnum($key) && mb_strlen($key, '8bit') <= 32 ? $key : md5($key);
        } else {
            $key = md5(json_encode($key, JSON_NUMERIC_CHECK));
        }
        return $this->keyPrefix . $key;
    }

    /**
     * Stores a value identified by a key in cache.
     * This is the implementation of the method declared in the parent class.
     *
     * @param string $key the key identifying the value to be cached
     * @param string $value the value to be cached
     * @param integer $duration the number of seconds in which the cached value will expire. 0 means never expire.
     * @return boolean true if the value is successfully stored into cache, false otherwise
     */
    protected function setValue($key, $value, $duration)
    {
        $this->gc();
        $cacheFile = $this->getCacheFile($key);
        if ($this->directoryLevel > 0) {
            if(!is_dir(dirname($cacheFile)))
            {
                @mkdir(dirname($cacheFile), $this->dirMode, true);
            }
        }
        if (@file_put_contents($cacheFile, $value, LOCK_EX) !== false) {
            if ($this->fileMode !== null) {
                @chmod($cacheFile, $this->fileMode);
            }
            if ($duration <= 0) {
                $duration = 31536000; // 1 year
            }

            return @touch($cacheFile, $duration + time());
        } else {
            $error = error_get_last();
            // HT::warning("Unable to write cache file '{$cacheFile}': {$error['message']}", __METHOD__);
            print_r("Unable to write cache file '{$cacheFile}': {$error['message']}", __METHOD__);
            return false;
        }
    }

    /**
     * Retrieves a value from cache with a specified key.
     * This is the implementation of the method declared in the parent class.
     * @param string $key a unique key identifying the cached value
     * @return string|boolean the value stored in cache, false if the value is not in the cache or expired.
     */
    protected function getValue($key)
    {
        $cacheFile = $this->getCacheFile($key);
        if (file_exists($cacheFile) && @filemtime($cacheFile) > time()) {
            $fp = @fopen($cacheFile, 'r');
            if ($fp !== false) {
                @flock($fp, LOCK_SH);
                $cacheValue = @stream_get_contents($fp);
                @flock($fp, LOCK_UN);
                @fclose($fp);
                return $cacheValue;
            }
        }

        return null;
    }

    /**
     * Deletes a value with the specified key from cache
     * This is the implementation of the method declared in the parent class.
     * @param string $key the key of the value to be deleted
     * @return boolean if no error happens during deletion
     */
    protected function deleteValue($key)
    {
        $cacheFile = $this->getCacheFile($key);

        return @unlink($cacheFile);
    }

    /**
     * Returns the cache file path given the cache key.
     * @param string $key cache key
     * @return string the cache file path
     */
    protected function getCacheFile($key)
    {
        if ($this->directoryLevel > 0) {
            $base = $this->cachePath;
            for ($i = 0; $i < $this->directoryLevel; ++$i) {
                if (($prefix = substr($key, $i + $i, 2)) !== false) {
                    $base .= DIRECTORY_SEPARATOR . $prefix;
                }
            }

            return $base . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
        } else {
            return $this->cachePath . DIRECTORY_SEPARATOR . $key . $this->cacheFileSuffix;
        }
    }

    /**
     * Removes expired cache files.
     * @param boolean $force whether to enforce the garbage collection regardless of [[gcProbability]].
     * Defaults to false, meaning the actual deletion happens with the probability as specified by [[gcProbability]].
     * @param boolean $expiredOnly whether to removed expired cache files only.
     * If false, all cache files under [[cachePath]] will be removed.
     */
    public function gc($force = false, $expiredOnly = true)
    {
        if ($force || mt_rand(0, 1000000) < $this->gcProbability) {
            $this->gcRecursive($this->cachePath, $expiredOnly);
        }
    }

    /**
     * Recursively removing expired cache files under a directory.
     * This method is mainly used by [[gc()]].
     * @param string $path the directory under which expired cache files are removed.
     * @param boolean $expiredOnly whether to only remove expired cache files. If false, all files
     * under `$path` will be removed.
     */
    protected function gcRecursive($path, $expiredOnly)
    {
        if (($handle = opendir($path)) !== false) {
            while (($file = readdir($handle)) !== false) {
                if ($file[0] === '.') {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($fullPath)) {
                    $this->gcRecursive($fullPath, $expiredOnly);
                    if (!$expiredOnly) {
                        if (!@rmdir($fullPath)) {
                            $error = error_get_last();
                            // HT::warning("Unable to remove directory '{$fullPath}': {$error['message']}", __METHOD__);
                            print_r("Unable to remove directory '{$fullPath}': {$error['message']}", __METHOD__);
                        }
                    }
                } elseif (!$expiredOnly || $expiredOnly && @filemtime($fullPath) < time()) {
                    if (!@unlink($fullPath)) {
                        $error = error_get_last();
                        // HT::warning("Unable to remove file '{$fullPath}': {$error['message']}", __METHOD__);
                        print_r("Unable to remove file '{$fullPath}': {$error['message']}", __METHOD__);
                    }
                }
            }
            closedir($handle);
        }
    }
}


// $fileObj = new W2FileCache();
// $fileObj = W2FileCache::getInstance();
// $set1 = $fileObj->set('test-cache',[1,2,31],5);
// D($set1);
// $get1 = $fileObj->get('test-cache');
// D($get1);


// $f_s = microtime (true);
// $data = [];
// for ($i = 0; $i < 5000; $i++) {
//  $data[] = $i;
//  $set1 = $fileObj->set('test-cache',$data,1000);
//  // D($data);
//  // exit;
// }
// $f_e = microtime (true);
// D('写',$f_e-$f_s);

// $f_s = microtime (true);
// $data = [];
// for ($i = 0; $i < 5000; $i++) {
//  $get1 = $fileObj->get('test-cache');
// }
// $f_e = microtime (true);
// D('读',$f_e-$f_s);
// exit;

// D($get1);
// D($fileObj);
// exit;

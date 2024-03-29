<?php

namespace KolayIK\Auth\Providers\Storage;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use Psr\SimpleCache\InvalidArgumentException;

/**
 * Class Illuminate
 * @package KolayIK\Auth\Providers\Storage
 */
class Illuminate implements StorageInterface
{
    /** @var CacheContract */
    protected $cache;

    const TOKEN_PREFIX = 'kolayik.kolayauth.';
    const REFRESH_TOKEN_PREFIX = 'kolayik.kolayauth.refresh.';

    /**
     * Illuminate constructor.
     * @param CacheContract $cache
     */
    public function __construct(CacheContract $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Add a new item into storage.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  int  $minutes
     *
     * @return void
     */
    public function add($prefix, $key, $value, $minutes)
    {
        $this->cache()->put($prefix . $key, $value, $minutes);
    }

    /**
     * Add a new item into storage forever.
     *
     * @param  string  $key
     * @param  mixed  $value
     *
     * @return void
     */
    public function forever($key, $value)
    {
        $this->cache()->forever(self::TOKEN_PREFIX . $key, $value);
    }

    /**
     * Get an item from storage.
     *
     * @param  string  $key
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function get($prefix, $key)
    {
        return $this->cache()->get($prefix . $key);
    }

    /**
     * Remove an item from storage.
     *
     * @param  string  $key
     *
     * @return bool
     */
    public function destroy($key): bool
    {
        return $this->cache()->forget(self::TOKEN_PREFIX . $key);
    }

    /**
     * Remove all items associated with the tag.
     *
     * @return void
     */
    public function flush()
    {
        $this->cache()->flush();
    }

    /**
     * Return the cache instance with tags attached.
     *
     * @return CacheContract
     */
    protected function cache(): CacheContract
    {
        return $this->cache;
    }
}

<?php

namespace KolayIK\Auth\Providers\Storage;

interface StorageInterface
{
    /**
     * @param  string $key
     * @param  mixed $value
     * @param  int $minutes
     *
     * @return void
     */
    public function add($prefix, $key, $value, $minutes);

    /**
     * @param  string $key
     * @param  mixed $value
     *
     * @return void
     */
    public function forever($key, $value);

    /**
     * @param  string $key
     *
     * @return mixed
     */
    public function get($prefix, $key);

    /**
     * @param  string $key
     *
     * @return bool
     */
    public function destroy($key);

    /**
     * @return void
     */
    public function flush();
}

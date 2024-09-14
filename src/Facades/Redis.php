<?php

namespace PulseFrame\Facades;

use \Redis as RedisClass;
use PulseFrame\Facades\Env;

/**
 * Class RedisFacade
 * 
 * @category facades
 * @name RedisFacade
 * 
 * This class is responsible for interacting with Redis. It provides static methods to connect to Redis,
 * set, get, delete values, and manage expiration for keys in the Redis store. The class utilizes the Env 
 * facade to load necessary Redis configurations such as the host, port, and authentication details.
 */
class Redis
{
  /**
   * The Redis instance.
   *
   * @var Redis
   */
  private static $redis;

  /**
   * Initialize the Redis connection.
   *
   * @category facades
   * 
   * @return void
   * 
   * This method initializes the Redis connection using the configuration stored in environment variables. 
   * It retrieves the host, port, and optional password from the Env facade, and establishes a connection 
   * to the Redis server.
   */
  private static function initialize()
  {
    if (!self::$redis) {
      self::$redis = new RedisClass();
      $host = Env::get('redis.host') ?? '127.0.0.1';
      $port = Env::get('redis.port') ?? 6379;
      $password = Env::get('redis.password');

      self::$redis->connect($host, $port);

      if ($password) {
        self::$redis->auth($password);
      }
    }
  }

  /**
   * Set a value in Redis with optional TTL.
   *
   * @category facades
   * 
   * @param string $key The key under which to store the value.
   * @param mixed $value The value to store.
   * @param int|null $ttl Time to live for the key, in seconds (optional).
   * @return bool True if the value was successfully set, false otherwise.
   *
   * This method sets a value in Redis under the specified key. Optionally, a TTL (time to live) can be provided 
   * to automatically expire the key after a given number of seconds.
   */
  public static function set($key, $value, $ttl = null)
  {
    self::initialize();

    if ($ttl) {
      return self::$redis->setex($key, $ttl, $value);
    }

    return self::$redis->set($key, $value);
  }

  /**
   * Get a value from Redis.
   *
   * @category facades
   * 
   * @param string $key The key to retrieve the value for.
   * @return mixed The value stored under the key, or false if the key does not exist.
   *
   * This method retrieves the value stored under the specified key in Redis. If the key does not exist, 
   * it returns false.
   */
  public static function get($key)
  {
    self::initialize();
    return self::$redis->get($key);
  }

  /**
   * Delete a key from Redis.
   *
   * @category facades
   * 
   * @param string $key The key to delete.
   * @return int The number of keys that were deleted.
   *
   * This method deletes the specified key from Redis.
   */
  public static function delete($key)
  {
    self::initialize();
    return self::$redis->del($key);
  }

  /**
   * Check if a key exists in Redis.
   *
   * @category facades
   * 
   * @param string $key The key to check.
   * @return bool True if the key exists, false otherwise.
   *
   * This method checks if the specified key exists in Redis.
   */
  public static function exists($key)
  {
    self::initialize();
    return self::$redis->exists($key);
  }

  /**
   * Set the expiration time for a key in Redis.
   *
   * @category facades
   * 
   * @param string $key The key for which to set the expiration time.
   * @param int $ttl The time to live (TTL) in seconds.
   * @return bool True if the expiration time was successfully set, false otherwise.
   *
   * This method sets the expiration time for the specified key in Redis. The key will be automatically 
   * deleted after the TTL expires.
   */
  public static function expire($key, $ttl)
  {
    self::initialize();
    return self::$redis->expire($key, $ttl);
  }

  /**
   * Set a hash value in Redis.
   *
   * @category facades
   * 
   * @param string $hash The hash key under which to store the value.
   * @param string $key The key within the hash to set.
   * @param mixed $value The value to store.
   * @return int 1 if the field was newly added and the value was set, or 0 if the field already existed and the value was updated.
   *
   * This method sets a value within a hash in Redis.
   */
  public static function hset($hash, $key, $value)
  {
    self::initialize();
    return self::$redis->hset($hash, $key, $value);
  }

  /**
   * Get a hash value from Redis.
   *
   * @category facades
   * 
   * @param string $hash The hash key.
   * @param string $key The key within the hash to retrieve.
   * @return mixed The value stored under the hash and key, or false if the hash or key does not exist.
   *
   * This method retrieves a value stored within a hash in Redis.
   */
  public static function hget($hash, $key)
  {
    self::initialize();
    return self::$redis->hget($hash, $key);
  }

  /**
   * Delete a key within a hash from Redis.
   *
   * @category facades
   * 
   * @param string $hash The hash key.
   * @param string $key The key within the hash to delete.
   * @return int The number of fields that were removed from the hash.
   *
   * This method deletes a specific field from a hash in Redis.
   */
  public static function hdel($hash, $key)
  {
    self::initialize();
    return self::$redis->hdel($hash, $key);
  }
}

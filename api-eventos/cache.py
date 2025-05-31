import redis
import os
from dotenv import load_dotenv

load_dotenv()
redis_url = os.getenv("REDIS_URL")
client = redis.Redis.from_url(redis_url)

def cache_get(key):
    return client.get(key)

def cache_set(key, value, ttl=60):
    client.setex(key, ttl, value)

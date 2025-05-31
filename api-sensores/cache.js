const redis = require('redis');
const { promisify } = require('util');
const dotenv = require('dotenv');
dotenv.config();

const client = redis.createClient({
  url: process.env.REDIS_URL
});
client.connect().catch(console.error);

async function get(key) {
  return await client.get(key);
}

async function set(key, value, ttlSeconds = 60) {
  await client.setEx(key, ttlSeconds, value);
}

module.exports = { get, set };

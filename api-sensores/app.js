const express = require('express');
const dotenv = require('dotenv');
const cache = require('./cache');
const { getSensorData } = require('./sensorService');
const { sendAlert } = require('./alertService');

dotenv.config();
const app = express();
app.use(express.json());

const PORT = process.env.PORT || 3001;

app.get('/sensor-data', async (req, res) => {
  const cachedData = await cache.get('sensorData');

  if (cachedData) {
    return res.json(JSON.parse(cachedData));
  }

  const data = getSensorData();
  await cache.set('sensorData', JSON.stringify(data), 10); // TTL de 10 segundos
  res.json(data);
});

app.post('/alert', async (req, res) => {
  const { message, level } = req.body;

  if (!message || !level) {
    return res.status(400).json({ error: 'message and level are required' });
  }

  try {
    await sendAlert({ message, level });
    res.json({ status: 'Alert sent to Python API' });
  } catch (err) {
    res.status(500).json({ error: 'Failed to send alert' });
  }
});

app.listen(PORT, () => {
  console.log(`Sensor API running on port ${PORT}`);
});

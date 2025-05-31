const axios = require('axios');
const dotenv = require('dotenv');
dotenv.config();

async function sendAlert(alertData) {
  const response = await axios.post(process.env.PYTHON_API_URL, alertData);
  return response.data;
}

module.exports = { sendAlert };

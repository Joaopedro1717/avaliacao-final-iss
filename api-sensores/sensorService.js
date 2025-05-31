function getSensorData() {
  const temperature = (Math.random() * 100).toFixed(2); // Ex: 78.65 °C
  const pressure = (Math.random() * 200).toFixed(2);    // Ex: 145.30 psi

  return {
    temperature: `${temperature} °C`,
    pressure: `${pressure} psi`,
    timestamp: new Date().toISOString()
  };
}

module.exports = { getSensorData };

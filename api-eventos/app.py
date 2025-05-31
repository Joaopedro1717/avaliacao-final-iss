from flask import Flask, request, jsonify
from events import add_event, get_all_events
from cache import cache_get, cache_set
from rabbitmq_consumer import start_consumer_thread
import json

app = Flask(__name__)

@app.route('/event', methods=['POST'])
def receive_event():
    data = request.json
    if not data or 'message' not in data or 'level' not in data:
        return jsonify({'error': 'message and level are required'}), 400

    add_event(data)
    cache_set('events', json.dumps(get_all_events()))
    return jsonify({'status': 'event stored'}), 201

@app.route('/events', methods=['GET'])
def list_events():
    cached = cache_get('events')
    if cached:
        return jsonify(json.loads(cached))
    return jsonify(get_all_events())

if __name__ == '__main__':
    start_consumer_thread()
    app.run(port=3002)

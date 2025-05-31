import pika
import json
import threading
from dotenv import load_dotenv
import os
from events import add_event

load_dotenv()
RABBITMQ_URL = os.getenv("RABBITMQ_URL")
QUEUE_NAME = os.getenv("RABBITMQ_QUEUE")

def callback(ch, method, properties, body):
    event = json.loads(body)
    print("[RabbitMQ] Mensagem recebida:", event)
    add_event(event)
    ch.basic_ack(delivery_tag=method.delivery_tag)

def start_consumer():
    connection = pika.BlockingConnection(pika.URLParameters(RABBITMQ_URL))
    channel = connection.channel()
    channel.queue_declare(queue=QUEUE_NAME)

    channel.basic_consume(queue=QUEUE_NAME, on_message_callback=callback, auto_ack=False)
    print("[RabbitMQ] Aguardando mensagens...")
    channel.start_consuming()

# Executar em thread separada
def start_consumer_thread():
    thread = threading.Thread(target=start_consumer)
    thread.daemon = True
    thread.start()

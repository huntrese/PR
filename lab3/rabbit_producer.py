import pika
import time
import json

# Connection parameters
credentials = pika.PlainCredentials('admin', 'password')
connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost',
                                                               credentials=credentials))
channel = connection.channel()

# Declare a queue
queue_name = 'hello_queue'
channel.queue_declare(queue=queue_name)

def send_message(message):
    channel.basic_publish(exchange='',
                          routing_key=queue_name,
                          body=message)
    print(f" [x] Sent '{message}'")

try:
    while True:
        # Read the JSON file
        with open(r"C:\Users\vlad\Documents\github\PR\lab3\formats\message.json", 'r') as file:
            json_string = file.read()
        
        # Parse the JSON string
        message = json.loads(json_string)
        
        # Convert the parsed JSON object to a string before sending
        send_message(str(message).encode('utf-8'))
        time.sleep(10)
except KeyboardInterrupt:
    print("Server stopped by user")
finally:
    connection.close()

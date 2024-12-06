import pika
import requests
# Connection parameters
credentials = pika.PlainCredentials('admin', 'password')  # Replace with your actual credentials
connection = pika.BlockingConnection(pika.ConnectionParameters(host='localhost',
                                                               credentials=credentials))
channel = connection.channel()

# Declare a queue
queue_name = 'hello_queue'
channel.queue_declare(queue=queue_name)

def callback(ch, method, properties, body):
    decoded= body.decode()
    print(f" [x] Received smth")
    response = requests.post("http://127.0.0.1:8000/lab-3/post",{"json_data":decoded})
    with open("response","w") as file:
        file.write(response.text)

print(' [*] Waiting for messages. To exit press CTRL+C')
try:
    channel.basic_consume(
        queue=queue_name,
        auto_ack=True,
        on_message_callback=callback)
    channel.start_consuming()
except KeyboardInterrupt:
    print("Client stopped by user")
finally:
    connection.close()

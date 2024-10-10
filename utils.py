import socket
import ssl
import json 
import gzip

def send_https_request(host, port=443, path='/', headers=None):
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        context = ssl.create_default_context()
        secure_sock = context.wrap_socket(sock, server_hostname=host)
        secure_sock.connect((host, port))
        
        request = f"GET {path} HTTP/1.1\r\nHost: {host}\r\n"
        if headers:
            for key, value in headers.items():
                request += f"{key}: {value}\r\n"
        request += "Connection: close\r\n\r\n"
        
        secure_sock.sendall(request.encode())

        response = b""
        while True:
            chunk = secure_sock.recv(4096)
            if not chunk:
                break
            response += chunk

        secure_sock.close()

        headers, body = response.split(b'\r\n\r\n', 1)
        headers = headers.decode('utf-8')

        is_gzipped = 'Content-Encoding: gzip' in headers

        if is_gzipped:
            body = gzip.decompress(body)

        try:
            body = body.decode('utf-8')
        except UnicodeDecodeError:
            print("Unable to decode response as UTF-8. Returning raw bytes.")
            return body

        return body

    except Exception as e:
        print(f"An error occurred: {e}")
        return None

def get_market_search_results(start, count):
    host = "steamcommunity.com"
    path = f"/market/search/render/?query=&start={start}&count={count}&search_descriptions=0&sort_column=popular&sort_dir=desc&appid=570"
    headers = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36",
        "Accept": "application/json",
        "Accept-Encoding": "gzip, deflate, br",
        "Accept-Language": "en-US,en;q=0.9",
        "Referer": "https://steamcommunity.com/market/search?appid=570"
    }
    
    response = send_https_request(host, 443, path, headers)
    if response:
        try:
            data = json.loads(response)
            return data
        except json.JSONDecodeError:
            print("Failed to parse JSON response")
            return None
    return None
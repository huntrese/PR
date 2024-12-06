import socket
import threading
import time
import random

# Node states
FOLLOWER = "Follower"
CANDIDATE = "Candidate"
LEADER = "Leader"

class RaftNode:
    def __init__(self, node_id, nodes):
        self.node_id = node_id
        self.nodes = nodes
        self.state = FOLLOWER
        self.current_term = 0
        self.voted_for = None
        self.timeout = random.uniform(5, 10)  # Election timeout in seconds
        self.last_heartbeat = time.time()
        self.vote_count = 0
        self.sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        self.sock.bind(("localhost", 5000 + node_id))
        self.running = True

    def log(self, message):
        print(f"[Node {self.node_id} | {self.state}] {message}")

    def send_message(self, target_id, message):
        self.sock.sendto(message.encode(), ("localhost", 5000 + target_id))

    def broadcast(self, message):
        for node_id in self.nodes:
            if node_id != self.node_id:
                self.send_message(node_id, message)

    def handle_message(self, message):
        parts = message.split("|")
        if parts[0] == "HEARTBEAT":
            term = int(parts[1])
            leader_id = int(parts[2])
            if term >= self.current_term:
                self.state = FOLLOWER
                self.current_term = term
                self.voted_for = leader_id
                self.last_heartbeat = time.time()
                self.log(f"Received heartbeat from Leader {leader_id} (term {term}).")

        elif parts[0] == "REQUEST_VOTE":
            term = int(parts[1])
            candidate_id = int(parts[2])
            if term > self.current_term:
                self.current_term = term
                self.voted_for = candidate_id
                self.state = FOLLOWER
                self.log(f"Voted for Candidate {candidate_id} (term {term}).")
                self.send_message(candidate_id, f"VOTE|{self.node_id}")

        elif parts[0] == "VOTE":
            if self.state == CANDIDATE:
                self.vote_count += 1
                self.log(f"Received vote from Node {parts[1]}. Total votes: {self.vote_count}.")
                if self.vote_count > len(self.nodes) // 2:
                    self.state = LEADER
                    self.log("Elected as Leader!")

    def run(self):
        threading.Thread(target=self.receive_messages).start()
        while self.running:
            time.sleep(1)
            if self.state == FOLLOWER:
                if time.time() - self.last_heartbeat > self.timeout:
                    self.state = CANDIDATE
                    self.current_term += 1
                    self.vote_count = 1  # Vote for self
                    self.voted_for = self.node_id
                    self.log("Starting election.")
                    self.broadcast(f"REQUEST_VOTE|{self.current_term}|{self.node_id}")

            elif self.state == LEADER:
                self.broadcast(f"HEARTBEAT|{self.current_term}|{self.node_id}")
                self.log("Sent heartbeat.")

    def receive_messages(self):
        while self.running:
            try:
                message, _ = self.sock.recvfrom(1024)
                self.handle_message(message.decode())
            except Exception as e:
                self.log(f"Error: {e}")

    def stop(self):
        self.running = False
        self.sock.close()

if __name__ == "__main__":
    nodes = [0, 1, 2, 3, 4]  # Node IDs
    raft_nodes = [RaftNode(node_id, nodes) for node_id in nodes]

    # Start all nodes
    threads = []
    for node in raft_nodes:
        thread = threading.Thread(target=node.run)
        thread.start()
        threads.append(thread)

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        print("Shutting down...")
        for node in raft_nodes:
            node.stop()
        for thread in threads:
            thread.join()

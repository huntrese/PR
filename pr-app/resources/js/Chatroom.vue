<template>
    <div class="flex flex-col items-center min-h-screen p-6 bg-base-200">
      <h1 class="text-2xl font-bold mb-4">Chatroom</h1>
      <div class="chat chat-start">
        <div class="chat-bubble">
          <span v-for="message in messages" :key="message.id">{{ message.user }}: {{ message.text }}</span>
        </div>
      </div>
      <input
        v-model="newMessage"
        @keydown.enter="sendMessage"
        type="text"
        placeholder="Type a message..."
        class="input input-bordered w-full max-w-xs mt-4"
      />
    </div>
  </template>
  
  <script>
  import Echo from "laravel-echo";
  window.Pusher = require("pusher-js"); // Or use Laravel Websockets if you have that set up
  
  export default {
    data() {
      return {
        messages: [],
        newMessage: "",
        echo: null,
      };
    },
    mounted() {
      // Initialize Laravel Echo and listen for messages
      this.echo = new Echo({
        broadcaster: "pusher",
        key: process.env.MIX_PUSHER_APP_KEY,
        cluster: process.env.MIX_PUSHER_APP_CLUSTER,
        forceTLS: true,
      });
  
      // Replace 'chat' with your actual channel name
      this.echo.channel("chat")
        .listen("MessageSent", (event) => {
          this.messages.push(event.message);
        });
    },
    methods: {
      async sendMessage() {
        if (!this.newMessage.trim()) return;
  
        try {
          await axios.post("/api/messages", {
            message: this.newMessage,
          });
          this.newMessage = "";
        } catch (error) {
          console.error("Error sending message:", error);
        }
      },
    },
  };
  </script>
  
  <style scoped>
  .chatroom {
    max-width: 600px;
    margin: auto;
  }
  </style>
  
<?php $title = "Chatbot"; ?>
<?php include 'templates/header.php'; ?>

<div class="max-w-3xl mx-auto mt-10 p-4">

    <div class="bg-white shadow rounded-xl h-[500px] flex flex-col">

        <!-- HEADER -->
        <div class="bg-blue-700 text-white p-3 rounded-t-xl">
            Chatbot Bengkel
        </div>

        <!-- CHAT AREA -->
        <div id="chat-area" class="flex-1 p-3 overflow-y-auto text-sm">
            <div class="mb-2">
                🤖 Halo! Ada yang bisa dibantu?
            </div>
        </div>

        <!-- INPUT -->
        <form id="chat-form" class="flex border-t">
            <input 
                type="text" 
                id="message" 
                class="flex-1 p-2 outline-none"
                placeholder="Tulis pesan..." 
                required
            >
            <button class="px-4 bg-blue-600 text-white">
                Kirim
            </button>
        </form>

    </div>

</div>

<script>
document.getElementById("chat-form").addEventListener("submit", function(e){
    e.preventDefault();

    const input = document.getElementById("message");
    const chatArea = document.getElementById("chat-area");

    const userMsg = input.value;

    // tampilkan pesan user
    chatArea.innerHTML += `<div>🧑 ${userMsg}</div>`;

    fetch("chatbot.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: "message=" + encodeURIComponent(userMsg)
    })
    .then(res => res.text())
    .then(botReply => {
        chatArea.innerHTML += `<div>🤖 ${botReply}</div>`;
        chatArea.scrollTop = chatArea.scrollHeight;
    });

    input.value = "";
});
</script>

<?php include 'templates/footer.php'; ?>
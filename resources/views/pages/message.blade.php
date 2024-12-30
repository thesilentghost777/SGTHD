@vite(['resources/css/message.css','resources/js/message.js'])
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Message Form</title>
   
</head>

<body>
    <div class="container">
        <h1>Send a Message</h1>
        <select id="messageCategory">
            <option value="" disabled selected>Select a category</option>
            <option value="complaint-private">Complaint (Private  privee))</option>
            <option value="suggestion">Suggestion</option>
            <option value="report">Report (signalement)</option>
        </select>
        <textarea id="messageContent" rows="5" placeholder="Write your message here..."></textarea>
        <button id="sendButton">Send</button>
        <div id="confirmation" class="cancel-button">
            <p>Are you sure you want to send this message? <span class="countdown" id="countdown"></span></p>
            <button id="cancelButton">Cancel</button>
        </div>
    </div>

    <script>
        let countdownTimer;

        document.getElementById("sendButton").addEventListener("click", function() {
            const confirmationDiv = document.getElementById("confirmation");
            confirmationDiv.style.display = "block";
            let timeLeft = 10; // Countdown modified to 10 seconds
            const countdownElem = document.getElementById("countdown");
            countdownElem.textContent = timeLeft + " seconds remaining";

            // Start countdown
            countdownTimer = setInterval(function() {
                timeLeft--;
                countdownElem.textContent = timeLeft + " seconds remaining";
                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    // Send message logic
                    sendMessage();
                    confirmationDiv.style.display = "none"; // Hide confirmation after sending
                }
            }, 1000);
        });

        document.getElementById("cancelButton").addEventListener("click", function() {
            clearInterval(countdownTimer);
            document.getElementById("confirmation").style.display = "none"; // Hide confirmation
        });

        async function sendMessage() {
            const category = document.getElementById("messageCategory").value;
            const content = document.getElementById("messageContent").value;

            const response = await fetch('{{ route('message-post') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    category: category,
                    content: content
                })
            });

            if (response.ok) {
                alert("Message sent!");
            } else {
                alert("There was an error sending your message.");
            }
        }
    </script>
</body>

</html>
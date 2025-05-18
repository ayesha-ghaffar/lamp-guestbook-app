<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'db.php';

// Handle form submission for new messages
$newMessageId = null;
$toast = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST["name"]);
    $message = htmlspecialchars($_POST["message"]);

    if (!empty($name) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (name, message) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $message);

        if ($stmt->execute()) {
            $newMessageId = $stmt->insert_id;
            $toast = 'Message sent!';
        }

        $stmt->close();
    }
}

// Get all messages from the database
$result = $conn->query("SELECT id, name, message, created_at FROM messages ORDER BY id DESC");

// Helper function to format timestamp
function formatTimestamp($timestamp) {
    $datetime = new DateTime($timestamp);
    return $datetime->format('M j, g:i a');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guestbook App</title>
    <!-- Include Tailwind CSS via CDN for styling -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .message-new {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { background-color: rgba(59, 130, 246, 0.2); }
            to { background-color: transparent; }
        }

        .toast {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border-radius: 4px;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .toast.show {
            opacity: 1;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex flex-col h-screen max-w-2xl mx-auto p-4">
        <header class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-600">Guestbook App</h1>
            <p class="text-gray-500">Leave a message for others</p>
        </header>

        <div class="flex-1 overflow-y-auto bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-100">
            <?php if ($result->num_rows == 0): ?>
                <div class="h-full flex items-center justify-center">
                    <p class="text-gray-400 text-center">No messages yet. Be the first to leave a message!</p>
                </div>
            <?php else: ?>
                <div class="space-y-3">
                    <?php while($row = $result->fetch_assoc()): ?>
                        <div class="p-3 rounded-lg border border-gray-200 <?php echo ($row['id'] === $newMessageId) ? 'message-new' : ''; ?>">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-semibold text-blue-700"><?php echo $row["name"]; ?></span>
                                <span class="text-xs text-gray-500"><?php echo isset($row["created_at"]) ? formatTimestamp($row["created_at"]) : ""; ?></span>
                            </div>
                            <p class="text-gray-800"><?php echo $row["message"]; ?></p>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
            <div id="messagesEnd"></div>
        </div>

        <div class="sticky bottom-4 bg-white p-4 rounded-lg border border-gray-100 shadow-sm">
            <form method="POST" action="" class="flex flex-col space-y-2">
                <div class="flex space-x-2">
                    <input
                        type="text"
                     name="name"
                        placeholder="Your name"
                        required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>
                <div class="flex space-x-2">
                    <input
                        type="text"
                        name="message"
                        placeholder="Type your message..."
                        required
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast notification -->
    <?php if ($toast): ?>
        <div id="toast" class="toast"><?php echo $toast; ?></div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toast = document.getElementById('toast');
                toast.classList.add('show');
                setTimeout(function() {
                    toast.classList.remove('show');
                }, 2000);

                // Scroll to bottom of messages
                const messagesEnd = document.getElementById('messagesEnd');
                if (messagesEnd) {
                    messagesEnd.scrollIntoView({ behavior: 'smooth' });
                }
            });
        </script>
    <?php endif; ?>
</body>

</html>

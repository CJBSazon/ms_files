<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swipe Transition Loop</title>
    <!-- Load Architects Daughter font from Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Architects+Daughter&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Keyframes for swiping out and in */
        @keyframes swipeOut {
            0% {
                transform: translateY(0);
            }
            100% {
                transform: translateY(-100%);
            }
        }
        
        @keyframes swipeIn {
            0% {
                transform: translateY(100%);
            }
            100% {
                transform: translateY(0);
            }
        }

        /* Add classes for the swipe animations */
        .swipe-out {
            animation: swipeOut 1s ease-in-out forwards;
        }

        .swipe-in {
            animation: swipeIn 1s ease-in-out forwards;
        }

        /* Apply the Architects Daughter font */
        body {
            font-family: 'Architects Daughter', cursive;
        }
    </style>
</head>
<body class="flex justify-center items-center min-h-screen bg-gray-100">

    <div id="swipe-container" class="relative overflow-hidden w-auto">
        <div id="text" class="text-4xl font-bold transition-transform">
            Mabsi
        </div>
    </div>

    <script>
        // Define the words and set up an infinite loop
        const words = ['Mabsi', 'Soy'];
        let currentIndex = 0;
        
        // Function to trigger the swipe transition
        function changeText() {
            const textElement = document.getElementById('text');
            const container = document.getElementById('swipe-container');
            
            // Add the swipe-out animation for the current word
            textElement.classList.add('swipe-out');
            
            // Wait for the swipe-out animation to finish before changing text
            setTimeout(() => {
                currentIndex = (currentIndex + 1) % words.length; // Toggle between 0 and 1
                textElement.textContent = words[currentIndex]; // Update text
                
                // Add swipe-in animation for the new word
                textElement.classList.remove('swipe-out');
                textElement.classList.add('swipe-in');
            }, 1000); // Match the duration of the swipe-out animation (1 second)
        }

        // Start the loop with an interval
        setInterval(changeText, 3000); // Change every 3 seconds

    </script>

</body>
</html>

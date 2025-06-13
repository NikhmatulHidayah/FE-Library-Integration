<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Integration Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #status-message {
        position: fixed;
        top: 20px;
        right: -500px;
        transition: right 0.5s ease-in-out; 
        z-index: 9999;
        padding: 10px 20px;
        font-size: 16px;
        }
        
        #status-message.show {
            right: 20px; 
        }
        
        #status-message.hide {
            right: -500px;
        }
    </style>
</head>
<body>
    <div class="container">
        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const statusMessage = document.getElementById('status-message');

            if (statusMessage) {
                statusMessage.classList.add('show');

                setTimeout(function() {
                    statusMessage.classList.add('hide');
                }, 3000); 
            }
        });
    </script>

</body>
</html>

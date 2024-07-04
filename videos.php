<?php
include 'db.php';

$videos_result = $conn->query("SELECT * FROM videos ORDER BY uploaded_at ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reproducción de Videos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        video {
            width: 100%;
            height: auto;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var videos = <?php
                $video_files = [];
                while ($row = $videos_result->fetch_assoc()) {
                    $video_files[] = $row['file_path'];
                }
                echo json_encode($video_files);
            ?>;
            var videoIndex = 0;
            var videoPlayer = document.getElementById('video-player');

            function playNextVideo() {
                if (videoIndex < videos.length) {
                    videoPlayer.src = videos[videoIndex];
                    videoPlayer.play();
                }
            }

            videoPlayer.addEventListener('loadedmetadata', function() {
                this.currentTime = 0;
            });

            videoPlayer.addEventListener('timeupdate', function() {
                if (this.currentTime >= this.duration - 0.5) {  // Ajuste para evitar problemas de precisión
                    this.pause();
                    videoIndex++;
                    playNextVideo();
                }
            });

            playNextVideo();
        });
    </script>
</head>
<body>
<div class="container">
    <h1>Reproducción de Videos</h1>
    <video id="video-player" controls autoplay>
        <source src="" type="video/mp4">
        Your browser does not support the video tag.
    </video>
</div>
</body>
</html>
<?php $conn->close(); ?>

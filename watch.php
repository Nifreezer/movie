<?php 
include 'config.php';

if (isset($_GET['id'])) {
    $movieId = $_GET['id'];
    $stmt = $pdo->prepare("SELECT m.*, g.name as genre FROM movies m LEFT JOIN genres g ON m.genre_id = g.id WHERE m.id = ?");
    $stmt->execute([$movieId]);
    $movie = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$movie) {
        header("Location: index.php");
        exit();
    }
    
    // Record view
    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $pdo->prepare("INSERT INTO views (movie_id, ip_address) VALUES (?, ?)");
    $stmt->execute([$movieId, $ip]);
} elseif (isset($_GET['slide'])) {
    $slideId = $_GET['slide'];
    $stmt = $pdo->prepare("SELECT * FROM slides WHERE id = ?");
    $stmt->execute([$slideId]);
    $slide = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$slide) {
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($movie) ? $movie['title'] : $slide['title'] ?> | MovieFlix</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .video-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            background: #000;
        }
        .video-container iframe, 
        .video-container video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">MovieFlix</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (isset($movie)): ?>
            <div class="video-container mb-4">
                <video controls autoplay class="w-100">
                    <source src="<?= $movie['video_url'] ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <h1><?= $movie['title'] ?></h1>
                    <div class="mb-3">
                        <span class="badge bg-secondary"><?= $movie['genre'] ?></span>
                        <span class="badge bg-info text-dark ms-1"><?= $movie['release_year'] ?></span>
                        <span class="badge bg-light text-dark ms-1"><?= floor($movie['duration']/60) ?>h <?= $movie['duration']%60 ?>m</span>
                    </div>
                    <p><?= $movie['description'] ?></p>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?= $movie['poster_url'] ?>" class="card-img-top" alt="<?= $movie['title'] ?>">
                    </div>
                </div>
            </div>
        <?php elseif (isset($slide)): ?>
            <div class="video-container mb-4">
                <video controls autoplay class="w-100">
                    <source src="<?= $slide['video_url'] ?>" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
            <h1><?= $slide['title'] ?></h1>
            <p><?= $slide['description'] ?></p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
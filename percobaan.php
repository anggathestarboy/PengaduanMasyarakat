<?php
session_start();
require_once "config/db.php";

// 🔒 cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ambil data pengaduan milik user login
$query = "SELECT * FROM pengaduan 
          WHERE user_id = '$user_id'
          ORDER BY date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengaduan Saya</title>
</head>
<body>

<h2>Pengaduan Saya</h2>

<?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($p = mysqli_fetch_assoc($result)): ?>

        <div style="border:1px solid #ccc; padding:10px; margin-bottom:10px;">
            
            <h3><?= htmlspecialchars($p['title']); ?></h3>
            
            <p><?= htmlspecialchars($p['description']); ?></p>
            
            <p><strong>Tanggal:</strong> <?= $p['date']; ?></p>
            
            <p><strong>Status:</strong> 
                <?= $p['status'] ?? 'menunggu'; ?>
            </p>

            <?php if (!empty($p['admin_note'])): ?>
                <p><strong>Catatan Admin:</strong> <?= htmlspecialchars($p['admin_note']); ?></p>
            <?php endif; ?>

            <?php if (!empty($p['img'])): ?>
                <img src="uploads/<?= $p['img']; ?>" width="200">
            <?php endif; ?>

        </div>

    <?php endwhile; ?>

<?php else: ?>
    <p>Kamu belum punya pengaduan 😢</p>
<?php endif; ?>

</body>
</html>
<?php require_once __DIR__ . '/includes/admin_auth.php';
$id=(int)$_GET['id'];
$stmt=$pdo->prepare("SELECT* FROM contact_messages WHERE id=?");
$stmt->execute([$id]);
$msg=$stmt->fetch();

$pdo->prepare("UPDATE contact_messages SET status='Read' WHERE id=?")->execute([$id]);

?>
<div class="admin-card" style="max-width:700px;margin:30px auto;">
    <h2>message Details</h2>
    <p><b>Name:</b> <?=$msg['name']?></p>
    <p><b>Email:</b> <?=$msg['email']?></p>
    <p><b>Subject:</b> <?=$msg['subject']?></p>
    <p><b>Date:</b> <?=$msg['created_at']?></p>
    <hr>
    <p><b>Message:</b><p>
    <div style="background:#f9fafb; padding:50px; border-radius:8px; white-space:pre-wrap;">
        <?=nl2br($msg['message'])?>
    </div><br>
    <a href="contact_messages.php" class="btn-view">IJ Back to Messages</a>

</div>

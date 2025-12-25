<?php
include '../includes/db_connect.php';
// We need to start session before header to check role
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'mod')) {
    header("Location: ../index.php");
    exit();
}

// Handle Actions
if (isset($_POST['action']) && isset($_POST['recipe_id'])) {
    $id = intval($_POST['recipe_id']);
    $status = ($_POST['action'] === 'approve') ? 'approved' : 'rejected';
    
    $stmt = $conn->prepare("UPDATE recipes SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    $msg = "Recipe " . $status . "!";
}

// Fetch Pending
$result = $conn->query("SELECT r.*, u.username FROM recipes r JOIN users u ON r.author_id = u.id WHERE r.status = 'pending' ORDER BY r.created_at ASC");
?>
<?php include '../includes/header.php'; ?>

<main class="container py-5">
    <h2 class="section-title mb-4">Moderation Queue</h2>
    
    <?php if(isset($msg)): ?>
        <div class="alert alert-success"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="card bg-dark border-secondary shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-dark table-hover mb-0 align-middle">
                    <thead class="bg-secondary text-light">
                        <tr>
                            <th class="p-3">Recipe</th>
                            <th>Author</th>
                            <th>Submitted</th>
                            <th class="text-end p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="p-3">
                                        <div class="fw-bold"><?php echo htmlspecialchars($row['title']); ?></div>
                                        <small class="text-muted"><?php echo substr($row['description'], 0, 50); ?>...</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary"><?php echo htmlspecialchars($row['username']); ?></span>
                                    </td>
                                    <td><?php echo date('M d, H:i', strtotime($row['created_at'])); ?></td>
                                    <td class="text-end p-3">
                                        <a href="../recipes/view.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-info me-2" target="_blank"><i class="fas fa-eye"></i></a>
                                        
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="recipe_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="action" value="approve" class="btn btn-sm btn-success me-1"><i class="fas fa-check"></i></button>
                                            <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No pending recipes. Great job!</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>

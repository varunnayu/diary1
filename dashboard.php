<?php
session_start();

if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

require_once "config.php";

$search = $start_date = $end_date = "";
$where_clause = "WHERE user_id = ?";
$params = array($_SESSION["id"]);
$param_types = "i";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!empty($_GET["search"])) {
        $search = $_GET["search"];
        $where_clause .= " AND (title LIKE ? OR content LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
        $param_types .= "ss";
    }
    
    if (!empty($_GET["start_date"])) {
        $start_date = $_GET["start_date"];
        $where_clause .= " AND created_at >= ?";
        $params[] = $start_date;
        $param_types .= "s";
    }
    
    if (!empty($_GET["end_date"])) {
        $end_date = $_GET["end_date"];
        $where_clause .= " AND created_at <= ?";
        $params[] = $end_date . " 23:59:59";
        $param_types .= "s";
    }
}

$sql = "SELECT id, title, created_at FROM notes $where_clause ORDER BY created_at DESC";
$notes = [];

if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, $param_types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        
        while ($row = mysqli_fetch_assoc($result)) {
            $notes[] = $row;
        }
    } else {
        echo "Oops! Something went wrong. Please try again later.";
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Personal Diary</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Personal Diary</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="dashboard.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_note.php">Add Note</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-4">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="mb-4">
            <div class="form-row">
                <div class="col-md-4 mb-3">
                    <input type="text" name="search" class="form-control" placeholder="Search notes" value="<?php echo $search; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="date" name="start_date" class="form-control" placeholder="Start Date" value="<?php echo $start_date; ?>">
                </div>
                <div class="col-md-3 mb-3">
                    <input type="date" name="end_date" class="form-control" placeholder="End Date" value="<?php echo $end_date; ?>">
                </div>
                <div class="col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary w-100">Filter</button>
                </div>
            </div>
        </form>

        <div class="row mt-4">
            <?php foreach ($notes as $note): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($note["title"]); ?></h5>
                            <p class="card-text">
                                <small class="text-muted">
                                    <?php echo date("F j, Y", strtotime($note["created_at"])); ?>
                                </small>
                            </p>
                            <a href="view_note.php?id=<?php echo $note["id"]; ?>" class="btn btn-primary">View Note</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


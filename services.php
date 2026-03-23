<?php
/**
 * services.php  –  Dynamic Services Page
 * ----------------------------------------
 * TASK REQUIREMENTS MET:
 *
 * 1. DATABASE TABLE  (tbl_content)
 *    Defined in DATABASE.sql with columns:
 *    id | title | description | image_url | category
 *
 * 2. CONNECTION FILE  (db_connect.php)
 *    A separate config file handles the database connection.
 *    This page just includes it — credentials are in one place.
 *
 * 3. DYNAMIC LOOP
 *    All static repeated HTML cards have been removed.
 *    One PHP while loop fetches every row and renders one
 *    card template per row automatically.
 *
 * SUCCESS CRITERIA:
 *  ✔  Add a row in phpMyAdmin → new card appears instantly, no HTML edits needed.
 *  ✔  Empty tbl_content → friendly "No services found" message shown.
 */

session_start();
include "db_connect.php";   // <-- THE CONNECTION FILE (Step 2)

// ── STEP 1: Query tbl_content ────────────────────────────────────────────────
// Fetch every row from tbl_content, newest first.
// $result holds the full result set; $total tells us how many rows came back.
$result = mysqli_query($conn, "SELECT * FROM tbl_content ORDER BY id ASC");
$total  = mysqli_num_rows($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Our Services – Homeland Hospital</title>
<link rel="stylesheet" href="style.css">
<style>
  /* ── Service card grid ─────────────────────────────────── */
  .svc-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
    gap: 26px;
    margin-top: 28px;
  }

  .svc-card {
    background: var(--white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    display: flex;
    flex-direction: column;
    transition: transform .22s, box-shadow .22s;
  }
  .svc-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg);
  }

  /* Card image */
  .svc-card img {
    width: 100%;
    height: 185px;
    object-fit: cover;
    display: block;
  }
  /* Placeholder shown when image_url is empty */
  .svc-card .img-placeholder {
    width: 100%;
    height: 185px;
    background: linear-gradient(135deg, #e0eafc, #cfdef3);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
  }

  /* Card body */
  .svc-body {
    padding: 20px 22px;
    flex: 1;
  }
  .svc-body h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--primary);
    margin: 0 0 10px;
  }
  .svc-body p {
    font-size: 0.875rem;
    color: var(--text3);
    line-height: 1.75;
    margin: 0;
  }

  /* Card footer */
  .svc-foot {
    padding: 11px 22px;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .svc-badge {
    font-size: 0.72rem;
    font-weight: 700;
    background: rgba(37,99,235,.1);
    color: var(--primary);
    padding: 4px 11px;
    border-radius: 20px;
    letter-spacing: .3px;
  }
  .svc-id {
    font-size: 0.72rem;
    color: var(--gray-400);
  }

  /* ── Result count pill ─────────────────────────────────── */
  .result-pill {
    display: inline-block;
    background: rgba(37,99,235,.08);
    color: var(--primary);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: .85rem;
    font-weight: 600;
    margin-bottom: 4px;
  }
</style>
</head>
<body>

<!-- ── Navigation ──────────────────────────────────────────────────────── -->
<nav>
  <span class="nav-brand">🏥 Homeland</span>
  <a href="index.php">🏠 Home</a>
  <?php if (isset($_SESSION['user_id'])): ?>
    <a href="dashboard.php">📊 Dashboard</a>
  <?php endif; ?>
  <a href="services.php" class="active">📋 Services</a>
</nav>

<!-- ── Banner ──────────────────────────────────────────────────────────── -->
<div class="banner">
  <img src="https://images.unsplash.com/photo-1586773860418-d37222d8fce3?w=1200&auto=format&fit=crop&q=70"
       alt="Our Services">
  <h2>📋 Our Services</h2>
</div>

<!-- ── Main content ────────────────────────────────────────────────────── -->
<div class="wrap">

  <h2 class="page-title">What We Offer</h2>

  <!-- Result count — updates automatically when rows are added/removed -->
  <p class="page-sub">
    <span class="result-pill"><?php echo $total; ?> service<?php echo $total !== 1 ? 's' : ''; ?></span>
    &nbsp;and departments ready to serve you.
  </p>

  <?php if ($total === 0): ?>

    <!-- ── EMPTY STATE ─────────────────────────────────────────────────
         Shown when tbl_content has no rows.
         SUCCESS CRITERIA: the page handles an empty table gracefully.
    ──────────────────────────────────────────────────────────────────── -->
    <div class="empty">
      <span class="eic">📋</span>
      <h3>No services listed yet</h3>
      <p>
        Add rows to <strong>tbl_content</strong> in phpMyAdmin<br>
        and they will appear here automatically.
      </p>
    </div>

  <?php else: ?>

    <!-- ── DYNAMIC LOOP ────────────────────────────────────────────────
         STEP 3 of the task: the while loop below replaces all
         the old hard-coded repeated <div> blocks.

         How it works:
           • mysqli_fetch_assoc() grabs the next row as an array.
           • The loop body is ONE card template.
           • PHP fills in title, description, image_url, category
             from $row for each iteration.
           • When no more rows remain, the loop ends automatically.

         To add a new service card:
           → INSERT a row into tbl_content in phpMyAdmin.
           → Refresh this page. Done. No HTML changes required.
    ──────────────────────────────────────────────────────────────────── -->
    <div class="svc-grid">

      <?php while ($row = mysqli_fetch_assoc($result)): ?>

        <div class="svc-card a1">

          <!-- Image: show the URL from the database, or a placeholder -->
          <?php if (!empty($row['image_url'])): ?>
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>"
                 alt="<?php echo htmlspecialchars($row['title']); ?>">
          <?php else: ?>
            <div class="img-placeholder">🏥</div>
          <?php endif; ?>

          <!-- Body: title and description come directly from the database -->
          <div class="svc-body">
            <h3><?php echo htmlspecialchars($row['title']); ?></h3>
            <p><?php echo htmlspecialchars($row['description']); ?></p>
          </div>

          <!-- Footer: category badge + row ID -->
          <div class="svc-foot">
            <?php if (!empty($row['category'])): ?>
              <span class="svc-badge">
                <?php echo htmlspecialchars($row['category']); ?>
              </span>
            <?php else: ?>
              <span></span>
            <?php endif; ?>
            <span class="svc-id">#<?php echo (int)$row['id']; ?></span>
          </div>

        </div><!-- end .svc-card -->

      <?php endwhile; ?>

    </div><!-- end .svc-grid -->

  <?php endif; ?>

</div><!-- end .wrap -->

</body>
</html>

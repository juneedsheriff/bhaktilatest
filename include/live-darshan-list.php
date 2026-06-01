<?php
$query = "SELECT * FROM live_darshan ORDER BY temple_name ASC LIMIT 3";
$result = mysqli_query($DatabaseCo->dbLink, $query);
?>
<section class="live-darshan-section">
    <div class="container">
        <div class="d-inline-block font-caveat fs-1 fw-medium section-header__subtitle text-capitalize text-primary"><span class="status-badge2 status-live">LIVE</span> Live Darshan</div>
      
        <div class="row">

            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>

                    <div class="col-md-4 col-sm-6 mb-4">
                        <div class="darshan-card">

                            <div class="darshan-thumb">
                                <?php if (!empty($row['thumbnail'])): ?>
                                    <img src="<?php echo htmlspecialchars($row['thumbnail']); ?>" 
                                         alt="<?php echo htmlspecialchars($row['temple_name']); ?>" onerror="this.src='assets/images/default-image.png'">
                                <?php else: ?>
                                    <img src="assets/img/default-temple.jpg" alt="Live Darshan" onerror="this.src='assets/images/default-image.png'">
                                <?php endif; ?>

                                <span class="status-badge status-live">LIVE</span>
                            </div>

                            <div class="darshan-content">
                                <h5 class="temple-name">
                                    <?php echo htmlspecialchars($row['temple_name']); ?>
                                </h5>

                                <p class="temple-location">
                                    📍 <?php echo htmlspecialchars($row['location']); ?>
                                </p>

                                <a href="<?php echo htmlspecialchars($row['live_url']); ?>" 
                                   target="_blank"
                                   class="btn btn-danger btn-sm w-100">
                                    Watch Live Darshan
                                </a>
                            </div>

                        </div>
                    </div>

                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p>No live darshan available at the moment.</p>
                </div>
            <?php endif; ?>

        </div>

    </div>
</section>
<style>
    .live-darshan-section {
    padding: 40px 0;
}

.darshan-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
}

.darshan-thumb {
    position: relative;
}

.darshan-thumb img {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.status-badge2 {
    position: absolute;
    top: 10px;
    left: 10px;
    background: red;
    color: #fff;
    font-size: 12px;
    padding: 4px 8px;
    border-radius: 100px;
}

.darshan-content {
    padding: 15px;
}

.temple-name {
    font-weight: 600;
    margin-bottom: 5px;
}

.temple-location {
    font-size: 14px;
    color: #666;
    margin-bottom: 10px;
}


.status-badge{
    position:absolute;
    top:12px;
    left:12px;
    padding:5px 12px;
    font-size:12px;
    font-weight:600;
    border-radius:20px;
    color:#fff;
}

.status-live{
    background:#dc3545;
    animation:pulse 1.5s infinite;
}

.status-offline{
    background:#6c757d;
}

@keyframes pulse{
    0%{box-shadow:0 0 0 0 rgba(220,53,69,.7);}
    70%{box-shadow:0 0 0 12px rgba(220,53,69,0);}
    100%{box-shadow:0 0 0 0 rgba(220,53,69,0);}
}
</style>
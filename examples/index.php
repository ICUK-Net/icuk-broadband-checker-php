<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Broadband Availability Example</title>
  <?php
    require __DIR__ . '/../vendor/autoload.php';
    $ba = new \Icuk\BroadbandAvailabilityPhp\BroadbandAvailability("/api.php");
    $ba->render_scripts();
    $ba->render_styles();
  ?>
</head>
<body>
  <?php
    $ba->render_search();
    $ba->render_address_list();
    $ba->render_results();
  ?>
</body>
</html>
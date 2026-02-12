<?php
require __DIR__ . '/../vendor/autoload.php';

// Add user authentication here!

echo json_encode(\Icuk\BroadbandAvailabilityPhp\BroadbandAvailabilityProxy::handle_api("beninnesapi", "86.CLOSE.STEP.DRIVE.61"));
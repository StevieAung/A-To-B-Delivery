<?php
$uploadDir = __DIR__ . "/uploads/drivers/";
if (is_writable($uploadDir)) {
    echo "Writable ✅";
} else {
    echo "Not writable ❌";
}
?>

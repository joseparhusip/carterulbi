<?php  
session_start(); 
session_unset();  
session_destroy();  

// Mencegah caching halaman
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
header('Location: index.php'); 
exit();
?>

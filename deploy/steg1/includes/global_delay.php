<?php
    if(!empty($_SERVER['REQUEST_METHOD']) && 
    in_array($_SERVER['REQUEST_METHOD'], ['GET', 'POST'], true)) {
        usleep(5000);
    }
?>
<?php
/*
 * SECURITY
 * Redirect to home, prevent listing names of users by query /?author=<random_id>
*/

header("HTTP/1.1 301 Moved Permanently");
header("Location: " . get_bloginfo('url'));
exit;

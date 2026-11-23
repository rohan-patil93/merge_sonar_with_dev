<?php
$lines = file('model/crm_user.class.php');
echo "Line 1780: " . $lines[1779]; // 0-indexed: 1779 is line 1780
echo "Line 2009: " . $lines[2008]; // 0-indexed: 2008 is line 2009
echo "Line 2010: " . $lines[2009];
echo "Line 2011: " . $lines[2010];
echo "Line 2012: " . $lines[2011];

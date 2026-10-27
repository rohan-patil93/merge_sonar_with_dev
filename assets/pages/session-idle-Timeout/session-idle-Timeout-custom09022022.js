'use strict';
$(document).ready(function() {
    $.sessionTimeout({
        heading: 'h5',
        title: 'Session time Out',
        message: 'Your session is about to expire. Do you want to stay connected?',
        warnAfter: 5000,   // 5 second
        redirAfter: 15000,   // 15 sec before
        keepAliveUrl: '/',
        redirUrl: 'custom_lock_screen.php',
        logoutUrl: 'index.php?option=COM_logout'
    });
    $('#sessionTimeout-dialog').modal({
    backdrop: 'static',
    keyboard: false
})
    
});
'use strict';
$(document).ready(function() {
    $.sessionTimeout({
        heading: 'h5',
        title: 'Session time Out',
        message: 'Your session is about to expire. Do you want to stay connected?',
        //warnAfter: 10000, // Warn after 10 minutes
        //redirAfter: 16000, // Redirect after 11 minutes
        warnAfter: 1000000, // Warn after 10 minutes
        redirAfter: 1006000, // Redirect after 11 minutes
        keepAliveUrl: '/',
        redirUrl: 'custom_lock_screen.php',
        logoutUrl: 'index.php?option=COM_logout'
    });
    $('#sessionTimeout-dialog').modal({
    backdrop: 'static',
    keyboard: false
})
    
});
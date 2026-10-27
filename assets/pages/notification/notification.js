
function tostarnotify(from, align, icon, type, animIn, animOut, error, title){
$.growl({
icon: icon,
title: title,
message: error,
url: ''
},{
element: 'body',
type: type,
allow_dismiss: true,
placement: {
from: from,
align: align
},
offset: {
x: 30,
y: 30
},
spacing: 10,
z_index: 999999,
delay: 2500,
timer: 1000,
url_target: '_blank',
mouse_over: false,
animate: {
enter: animIn,
exit: animOut
},
icon_type: 'class',
template: '<div data-growl="container" class="alert" role="alert"> ' +
' <span data-growl="icon" class="f-18"></span> &nbsp;' +
' <span data-growl="title"></span> ' +
' <span data-growl="message" class="f-16 p-t-3 p-b-3"></span>' +
' <a href="#" data-growl="url"></a>' +
'</div>'
});
} 


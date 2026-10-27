<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Popup Positioning</title>
    <style type="text/css">
        ul {
            padding: 0;
            list-style: none;
        }
        li {
            display: inline-block;
        }
        span {
            width: 40px;
            height: 40px;
            line-height: 40px;
            background: #cccccc;
            border-radius: 5px;
            text-align: center;
            margin: 10px;
            display: inline-block;
            cursor: pointer;
        }
        .open_popup {
            display: none;
            position: absolute;
            width: 200px;
            height: 200px;
            background: #eeeeee;
            padding: 10px;
            box-sizing: border-box;
        }
        .close {
            float: right;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <ul>
            <?php for($i=1; $i<200; $i++){ ?>
            <li>
                <span class="popup" onclick="openPopup(this,<?php echo $i; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}"><?php echo $i; ?></span>
                <div class="open_popup" id="popup_<?php echo $i; ?>">
                    PoPup
                    <b class="close" onclick="closePopup(<?php echo $i; ?>)" role="button" tabindex="0" onkeypress="if(event.key==='Enter'||event.key===' '){this.click();}">close</b>
                </div>
            </li>
            <?php } ?>
        </ul>
    </div>
    <script>
        function closeAllPopups() {
            const popups = document.querySelectorAll('.open_popup');
            popups.forEach(popup => {
                popup.style.display = 'none';
            });
        }
        function openPopup(button, val) {
            closeAllPopups();
            const popup = document.getElementById('popup_'+val);

            const buttonRect = button.getBoundingClientRect();
            const buttonTop = buttonRect.top + window.scrollY-89;
            const buttonLeft = buttonRect.left + window.scrollX;
            const buttonWidth = buttonRect.width;
            const buttonHeight = buttonRect.height;

             console.log("buttonTop:"+buttonTop+"---"+"buttonLeft:"+buttonLeft+"---"+"buttonWidth:"+buttonWidth+"---"+"buttonHeight:"+buttonHeight+"---");

            // Get popup dimensions
            const popupWidth = popup.offsetWidth;
            const popupHeight = popup.offsetHeight;

            console.log("popupWidth:"+popupWidth+"---"+"popupHeight:"+popupHeight);
            console.log("windowinnerWidth:"+window.innerWidth+"---"+"windowinnerHeight:"+window.innerHeight);
            console.log("windowscrollY:"+window.scrollY+"---"+"windowscrollX:"+window.scrollX);
            // Calculate popup position
            let popupTop, popupLeft;
            if (buttonTop + buttonHeight + popupHeight + 0 < window.innerHeight) {
                popupTop = buttonTop + buttonHeight + 0; // Below the button
                 console.log(" step 1:"+popupTop);
            } else {
                popupTop = buttonTop - popupHeight - 200; // Above the button
                console.log(" step 2:"+popupTop);
            }

            if (buttonLeft + popupWidth + 0 < (window.innerWidth-250)) {
                popupLeft = buttonLeft + buttonWidth + 0; // Right of the button
                 console.log("case 1:"+popupLeft);
            } else {
                popupLeft = buttonLeft - popupWidth - 0; // Left of the button
                 console.log("case 2:"+popupLeft);
            }

            // Adjust popup position to prevent overflow
            if (popupTop < window.scrollY) {

                popupTop = window.scrollY;
                console.log(" step 3:"+popupTop);
            }
            if (popupLeft < window.scrollX) {
                popupLeft = window.scrollX;
                 console.log("case 3:"+popupLeft);
            }
            if (popupTop + popupHeight > window.scrollY + window.innerHeight) {
                popupTop = window.scrollY + window.innerHeight - popupHeight;
                console.log(" step :"+popupTop);
            }
            if (popupLeft + popupWidth > window.scrollX + (window.innerWidth-250)) {
                popupLeft = window.scrollX + window.innerWidth-250 - popupWidth-200;
                 console.log("case 4:"+popupLeft);
            }

            // Apply position to popup
            popup.style.top = popupTop + 'px';
            popup.style.left = popupLeft + 'px';
            popup.style.display = 'block';
        }

        function closePopup(val) {
            const popup = document.getElementById('popup_' + val);
            popup.style.display = 'none';
        }
    </script>
</body>
</html>

</body>
</html>

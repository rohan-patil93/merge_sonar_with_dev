<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Display</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f0f0f0;
        }
        .container {
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background: #007BFF;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Data Display Table</h2>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Value</th>
            </tr>
            <tr>
                <td>1</td>
                <td>Item A</td>
                <td>$100</td>
            </tr>
            <tr>
                <td>2</td>
                <td>Item B</td>
                <td>$200</td>
            </tr>
            <tr>
                <td>3</td>
                <td>Item C</td>
                <td>$300</td>
            </tr>
        </table>
    </div>
</body>
</html>

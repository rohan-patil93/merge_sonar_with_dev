<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Multiple Rankings</title>
<style>
  body {
    font-family: 'Segoe UI', sans-serif;
    background: #f3f4f6;
    margin: 0;
    padding: 40px;
  }

  .survey-container {
    background: #fff;
    max-width: 800px;
    margin: 0 auto;
    padding: 30px 40px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  }

  h2 {
    text-align: center;
    color: #1f2937;
    margin-bottom: 30px;
  }

  .rating-group {
    margin-bottom: 30px;
  }

  .rating-label {
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    display: block;
  }

  input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    width: 100%;
    height: 14px;
    border-radius: 10px;
    background: linear-gradient(90deg, #22c55e 50%, #e5e7eb 50%);
    outline: none;
    transition: background 0.3s ease;
  }

  input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    background: #2563eb;
    border: 2px solid #fff; 
    box-shadow: 0 0 4px rgba(0,0,0,0.3);
    cursor: pointer;
  }

  .rating-value {
    margin-top: 6px;
    font-size: 15px;
    color: #333;
  }

  .submit-btn {
    display: block;
    width: 100%;
    background: #2563eb;
    color: white;
    border: none;
    padding: 14px;
    border-radius: 10px;
    font-size: 16px;
    cursor: pointer;
    transition: background 0.3s ease;
  }

  .submit-btn:hover {
    background: #1d4ed8;
  }

  .submitted-msg {
    text-align: center;
    margin-top: 20px;
    display: none;
    font-weight: 500;
    color: #22c55e;
  }
</style>
</head>
<body>

<div class="survey-container">
  <h2>Please rate the following aspects</h2>

  <div class="rating-group">
    <label class="rating-label">Service Quality</label>
    <input type="range" min="1" max="10" value="4" id="111" class="rating-range" style="background: linear-gradient(90deg, rgb(34, 197, 94) 33.333%, rgb(229, 231, 235) 33.333%);">
    <div class="rating-value">Rating: <span>5</span> / 10</div>
  </div>

  <div class="rating-group">
    <label class="rating-label">Response Time</label>
    <input type="range" min="1" max="10" value="7" class="rating-range" style="background: linear-gradient(90deg, rgb(34, 197, 94) 66.7778%, rgb(229, 231, 235) 66.7778%);">
    <div class="rating-value">Rating: <span>5</span> / 10</div>
  </div>

  <div class="rating-group">
    <label class="rating-label">Overall Experience</label>
    <input type="range" min="1" max="10" value="8" class="rating-range" style="background: linear-gradient(90deg, rgb(34, 197, 94) 77.7778%, rgb(229, 231, 235) 77.7778%);">
    <div class="rating-value">Rating: <span>5</span> / 10</div>
  </div>
</div>

<script>
  document.querySelectorAll('.rating-range').forEach(slider => {
    slider.addEventListener('input', function() {
      const percent = ((this.value - this.min) / (this.max - this.min)) * 100;
      this.style.background = `linear-gradient(90deg, #22c55e ${percent}%, #e5e7eb ${percent}%)`;
      this.nextElementSibling.querySelector('span').textContent = this.value;
      $('#111').val(this.value);
    });
  });

  
</script>

</body>
</html>

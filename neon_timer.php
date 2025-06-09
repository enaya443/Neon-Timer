<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['minutes'] = $_POST['minutes'] ?? 0;
    $_SESSION['seconds'] = $_POST['seconds'] ?? 0;
    exit;
}

$minutes = $_SESSION['minutes'] ?? 0;
$seconds = $_SESSION['seconds'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Neon Countdown Timer</title>
  <style>
    body {
      background: #0d0d0d;
      color: #0ff;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      height: 100vh;
      margin: 0;
      gap: 25px;
    }

    .timer {
      font-size: 5rem;
      font-weight: bold;
      border: 4px solid #0ff;
      padding: 20px 60px;
      border-radius: 20px;
      background: #111;
      box-shadow: 0 0 30px #0ff, inset 0 0 10px #0ff;
    }

    .inputs, .controls {
      display: flex;
      gap: 15px;
    }

    input[type="number"] {
      width: 90px;
      padding: 10px;
      font-size: 1.2rem;
      border-radius: 8px;
      border: 2px solid #0ff;
      background: #222;
      color: #0f0;
      text-align: center;
    }

    button {
      background: #0ff;
      color: #000;
      border: none;
      padding: 12px 20px;
      font-size: 1.1rem;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
    }

    button:hover {
      background: #0cc;
      transform: scale(1.05);
    }
  </style>
</head>
<body>

  <div class="timer" id="display">00:00</div>

  <div class="inputs">
    <input type="number" id="minutes" placeholder="Minutes" min="0" value="<?= htmlspecialchars($minutes) ?>">
    <input type="number" id="seconds" placeholder="Seconds" min="0" max="59" value="<?= htmlspecialchars($seconds) ?>">
  </div>

  <div class="controls">
    <button onclick="start()">Start</button>
    <button onclick="pause()">Pause</button>
    <button onclick="reset()">Reset</button>
  </div>

  <audio id="alarm" src="https://actions.google.com/sounds/v1/alarms/alarm_clock.ogg" preload="auto"></audio>

  <script>
    let time = 0;
    let interval = null;

    const display = document.getElementById('display');
    const alarm = document.getElementById('alarm');

    function updateDisplay() {
      const mins = Math.floor(time / 60);
      const secs = time % 60;
      display.textContent = `${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
    }

    function start() {
      if (interval) return;

      const mins = parseInt(document.getElementById('minutes').value) || 0;
      const secs = parseInt(document.getElementById('seconds').value) || 0;

      if (time === 0) {
        time = mins * 60 + secs;
      }

      if (time <= 0) return;

      fetch('neon_timer.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `minutes=${mins}&seconds=${secs}`
      });

      interval = setInterval(() => {
        if (time > 0) {
          time--;
          updateDisplay();
        } else {
          clearInterval(interval);
          interval = null;
          alarm.play();
          alert("⏰ Time's up!");
        }
      }, 1000);

      updateDisplay();
    }

    function pause() {
      clearInterval(interval);
      interval = null;
    }

    function reset() {
      pause();
      time = 0;
      document.getElementById('minutes').value = '';
      document.getElementById('seconds').value = '';
      updateDisplay();
    }

    window.onload = () => {
      const m = parseInt(document.getElementById('minutes').value) || 0;
      const s = parseInt(document.getElementById('seconds').value) || 0;
      time = m * 60 + s;
      updateDisplay();
    };
  </script>
</body>
</html>


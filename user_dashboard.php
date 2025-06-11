<?php
session_start();
if (!isset($_SESSION['user_name']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}
include 'includes/header.php';
$user = $_SESSION['user_name'];
?>

<style>
  body {
    background: linear-gradient(135deg, #e0f2f1, #a5d6a7);
    font-family: 'Segoe UI', sans-serif;
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
    color: #1b4332;
  }

  .stars-container {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    pointer-events: none;
    overflow: hidden;
    z-index: 0;
  }

  .star {
    position: absolute;
    width: 12px;
    height: 12px;
    background: radial-gradient(circle, #c5e1a5 60%, transparent 80%);
    border-radius: 50%;
    opacity: 0.85;
    animation: floatStar linear infinite;
    filter: drop-shadow(0 0 4px #a5d6a7);
  }

  @keyframes floatStar {
    0% {
      transform: translateY(0) scale(1);
      opacity: 0.85;
    }
    100% {
      transform: translateY(-200px) scale(0.5);
      opacity: 0;
    }
  }

  .greeting-box {
    background: rgba(255, 255, 255, 0.25);
    border-radius: 20px;
    padding: 50px 40px;
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.3);
    backdrop-filter: blur(14px);
    max-width: 800px;
    width: 100%;
    text-align: center;
    position: relative;
    z-index: 1;
margin-left:230px;
  }

  .bismillah {
    font-size: 36px;
    color: #4a6c43;
    font-style: italic;
    font-family: 'Scheherazade New', serif;
    margin-bottom: 10px;
  }

  .arabic-greeting {
    font-size: 44px;
    font-weight: 700;
    color: #235b4e;
    font-family: 'Amiri', serif;
    margin-bottom: 20px;
  }

  .typing-text {
    font-size: 24px;
    font-weight: 600;
    color: #2e7d32;
    margin-bottom: 25px;
    white-space: nowrap;
    overflow: hidden;
    border-right: 2px solid #2e7d32;
    width: 0;
    animation: typing 4s steps(40, end) forwards;
  }

  @keyframes typing {
    from { width: 0; }
    to { width: 100%; }
  }

  .greeting-buttons {
    margin-top: 25px;
    display: flex;
    justify-content: center;
    gap: 20px;
    flex-wrap: wrap;
  }

  .voice-btn {
    background-color: #198754;
    color: white;
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 10px;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s ease, transform 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }
  .voice-btn:hover {
    background-color: #145c32;
    transform: scale(1.05);
  }

  .btn-outline-success {
    background-color: transparent;
    color: #198754;
    border: 2px solid #198754;
    padding: 12px 24px;
    font-size: 16px;
    border-radius: 10px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
  }
  .btn-outline-success:hover {
    background-color: #198754;
    color: white;
    transform: scale(1.05);
  }

  .quote-section {
    font-size: 20px;
    font-style: italic;
    color: #3e606f;
    font-family: 'Georgia', serif;
    background: rgba(255, 255, 255, 0.35);
    padding: 15px 25px;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    margin-top: 40px;
    user-select: none;
  }

  .duaa-card {
    margin-top: 30px;
    font-size: 18px;
    color: #1c4532;
    background: rgba(255,255,255,0.4);
    border-left: 5px solid #388e3c;
    padding: 15px 20px;
    border-radius: 10px;
    font-family: 'Georgia', serif;
  }

  @media (max-width: 600px) {
    .greeting-box { padding: 30px 20px; }
    .arabic-greeting { font-size: 32px; }
    .bismillah { font-size: 28px; }
    .typing-text { font-size: 18px; }
    .quote-section { font-size: 16px; padding: 12px 18px; }
  }
</style>

<div class="stars-container" aria-hidden="true"></div>

<div class="container d-flex align-items-center justify-content-center" style="min-height: 100vh;">
  <div class="greeting-box">
    <div class="bismillah">﷽</div>
    <div class="arabic-greeting">السلام عليكم ورحمة الله وبركاته</div>

    <div class="typing-text" id="welcomeText">
      <!-- Dynamic welcome will appear here -->
    </div>

    <div class="greeting-buttons">
      <button class="btn voice-btn" onclick="playGreeting()">
        <i class="bi bi-volume-up-fill"></i> Play Greeting
      </button>
      <a href="hadees_list.php" class="btn btn-outline-success">
        <i class="bi bi-book-half"></i> View Ahadees
      </a>
    </div>

    <div class="quote-section mt-5" id="quoteSection"></div>

    <div class="duaa-card">
      May Allah (SWT) bless you with barakah in knowledge, actions and time. Ameen 🤲
    </div>
  </div>
</div>

<script>
  const quotes = [
    "Indeed, in the remembrance of Allah do hearts find rest. – Surah Ar-Ra'd (13:28)",
    "And seek help through patience and prayer. – Surah Al-Baqarah (2:45)",
    "So remember Me; I will remember you. – Surah Al-Baqarah (2:152)",
    "Allah is with those who patiently persevere. – Surah Al-Anfal (8:46)"
  ];
  let quoteIndex = 0;
  const quoteElement = document.getElementById('quoteSection');

  function showNextQuote() {
    quoteElement.textContent = quotes[quoteIndex];
    quoteIndex = (quoteIndex + 1) % quotes.length;
  }

  showNextQuote();
  setInterval(showNextQuote, 7000);

  function playGreeting() {
    if ('speechSynthesis' in window) {
      const msg = new SpeechSynthesisUtterance();
      msg.text = "As-salamu alaykum waarahmatullahi waabaarakatuh";
      msg.lang = 'ar-SA';
      window.speechSynthesis.speak(msg);
    } else {
      alert("Sorry, your browser does not support voice playback.");
    }
  }

  // Floating stars
  const starsContainer = document.querySelector('.stars-container');
  const starCount = 40;
  for (let i = 0; i < starCount; i++) {
    const star = document.createElement('div');
    star.classList.add('star');
    star.style.left = `${Math.random() * 100}%`;
    star.style.top = `${Math.random() * 100}%`;
    star.style.animationDuration = `${6 + Math.random() * 10}s`;
    star.style.animationDelay = `${Math.random() * 5}s`;
    starsContainer.appendChild(star);
  }

  // Welcome typing effect
  const user = "<?php echo $user; ?>";
  const welcome = `Welcome, ${user}! May peace be upon you.`;
  const welcomeElement = document.getElementById("welcomeText");

  // Typing animation delay
  setTimeout(() => {
    welcomeElement.textContent = welcome;
  }, 200);
</script>

<?php include 'includes/footer.php'; ?>

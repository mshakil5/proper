<section id="cookie" class="services-2 section cookie-consent-banner">
  <div class="container">
    <div class="row justify-content-center align-items-center" data-aos="fade-up">
      <div class="col-md-12 col-lg-12">
        <span class="content-subtitle">Cookies Policy</span>
        <p class="lead">
          This website uses cookies to assist with navigation, improve services,
          analyse usage, and support our marketing efforts.
        </p>
        <div class="cookie-buttons mt-3">
          <button class="btn btn-primary" onclick="acceptCookies()">Accept</button>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  function setCookie(name, value, days) {
      let expires = "";
      if (days) {
          const date = new Date();
          date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
          expires = "; expires=" + date.toUTCString();
      }
      document.cookie = name + "=" + (value || "") + expires + "; path=/";
  }

  function getCookie(name) {
      const nameEQ = name + "=";
      const ca = document.cookie.split(';');
      for (let i = 0; i < ca.length; i++) {
          let c = ca[i].trim();
          if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length);
      }
      return null;
  }

  function acceptCookies() {
      setCookie('cookie_consent', 'accepted', 365);
      hideCookieBanner();
  }

  function showCookieBanner() {
      document.getElementById('cookie').classList.add('active');
  }

  function hideCookieBanner() {
      document.getElementById('cookie').classList.remove('active');
  }

  document.addEventListener('DOMContentLoaded', function () {
      if (!getCookie('cookie_consent')) {
          setTimeout(showCookieBanner, 1000);
      }
  });
</script>

<style>
  .cookie-buttons .btn {
    background-color: #c5a572;
    border-color: #c5a572;
    border-radius: 50px;
  }
  .cookie-buttons .btn:hover {
    background-color: #b1915d;
    border-color: #b1915d;
  }
.cookie-buttons {
    text-align: left;
}

.cookie-buttons .btn {
    display: inline-block;
    width: auto;
}
</style>
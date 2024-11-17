<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Politique de confidentialité</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
  <link href="{{asset('logo.png')}}" rel="icon">
  <link href="{{asset('logo.png')}}" rel="apple-touch-icon">

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@200;300;400;500;700;800;900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{asset('pages/assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{asset('pages/assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{asset('pages/assets/vendor/aos/aos.css')}}" rel="stylesheet">
  <link href="{{asset('pages/assets/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
  <link href="{{asset('pages/assets/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{asset('pages/assets/css/main.css')}}" rel="stylesheet">
</head>

<body class="index-page">
<main class="main">
    <div class="content-wrapper" style="padding: 20px; text-align:center">
        {!! $data !!}
    </div>
</main>

<footer id="footer" class="footer dark-background">
  <div class="container">
      <img src="{{asset('agropole-no-bg.png')}}" alt="logo" class="footerLogo">

      <div class="social-links d-flex justify-content-center">
          <a href=""><i class="bi bi-twitter-x"></i></a>
          <a href=""><i class="bi bi-facebook"></i></a>
          <a href=""><i class="bi bi-instagram"></i></a>
          <a href=""><i class="bi bi-linkedin"></i></a>
      </div>
      <div class="container">
          <div class="copyright">
              <span>Copyright</span> <strong>Agropole</strong> <span>Tous droits réservés</span>
          </div>
      </div>
  </div>
</footer>
</body>
</html>

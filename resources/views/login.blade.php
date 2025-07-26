<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>An-Nur II</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="css/style.css"/>
  </head>
  <body>
    <div class="row d-flex justify-content-center align-items-center container">
      @if(Session::has('error'))
      <div class="alert danger-alert">
        <p>{{ Session::get('error') }}</p>
        <a class="close">&times;</a>
      </div>
      @endif
      <h1 class="text-center mb-4 fw-bold">An-Nur II</h1>
      <div class="card">
        <div class="tab-content" id="pills-tabContent">
          <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
            <form class="form px-4 text-center" action="/login" method="POST">
              @csrf
              <input type="text" name="username" class="form-control" placeholder="Username"/>
              <input type="text" name="password" class="form-control" placeholder="Password"/>
              <button class="btn btn-dark btn-block" type="submit">Masuk</button>
            </form>
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center align-items-center mt-5">
        <i class="fa-solid fa-sun me-2"></i>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="darkModeSwitch" data-bs-toggle="tooltip">
        </div>
        <i class="fa-solid fa-moon ms-2"></i>
      </div>    
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="/js/script.js"></script>
  </body>
</html>
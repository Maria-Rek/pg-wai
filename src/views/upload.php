<!DOCTYPE html>
<html lang="pl-PL">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Galeria Kotków</title>
    <link rel="stylesheet" type="text/css" href="../static/style/style.css" />
  </head>

  <body>
    <div class="layout">
      <header id="top">
        <a href="/">Strona główna</a>
      </header>

      <nav>
        <ul>
          <li>
              <a href="/cats/galeria"><b>Galeria kotów</b></a>
          </li>
		      <li>
			      <a href="/upload"><b>Upload</b></a>
		      </li>
          <li>
            <?php if (!empty($_SESSION['user'])): ?>
            <p><b>Użytkownik: <?= htmlspecialchars($_SESSION['user']); ?></b></p>
            <a href="/logout">Wyloguj się</a>
            <?php else: ?>
              <a href="/login">Zaloguj się</a>
              <br/>
              <a href="/register">Zarejestruj się</a>
            <?php endif; ?>
          </li>
        </ul>
      </nav>

      <footer>
        <div id="footer">
              <p>Maria Rek | Wytwarzanie aplikacji internetowych | 2024/2025</p> 
        </div>
      </footer>

      <main>
        <h5>Wysyłanie zdjęć</h5>
      </main>

      <div class="content">
        <form method="post" enctype="multipart/form-data">
          <input type="file" name="file" />
          <br />
          <input type="text" name="autor" placeholder="Autor" />
          <br />
          <input type="text" name="tytul" placeholder="Tytuł" />
          <br />
          <input type="text" name="watermark" placeholder="Znak wodny" />
          <input type="submit" value="Wyślij plik" />
        </form>
        <?php
        if(isset($_GET['error'])){
          if($_GET['error'] == 1){
          echo '<p style="color: red;">Nieprawidłowy format pliku!</p>';
          }
          if($_GET['error'] == 2){
            echo '<p style="color: red;">Za duży plik!</p>';
          }
          if($_GET['error'] == 3){
            echo '<p style="color: red;">Błąd bazy danych!</p>';
          }
          if($_GET['error'] == 4){
            echo '<p style="color: red;">Brak obowiązkowego pola!</p>';
          }
          if($_GET['error'] == 5){
            echo '<p style="color: red;">Katalog nie istnieje lub nie ma uprawnień!</p>';
          }
        }
        ?>
      </div>
    </div>
  </body>
</html>
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
        <h5>Przesyłanie zdjęć</h5>
      </main>

      <div class="content">
        <?php
          $picture = get_picture_by_id($id);
          $tytul = $picture['tytul'];
          $autor = $picture['autor'];
          $extension = pathinfo($picture['path'], PATHINFO_EXTENSION);
          $path = '../images/watermark/' . $id . '.' . $extension;
          echo '<img src="' . $path . '" alt="' . $tytul . '">';
          echo '<p> Tytuł: ' . $tytul . '</p>';
          echo '<p> Autor: ' . $autor . '</p>';
        ?>
      </div>
    </div>
  </body>
</html>

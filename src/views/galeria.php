<!DOCTYPE html>
<html lang="pl-PL">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Galeria Kotków</title>
    <link rel="stylesheet" type="text/css" href="../static/style/galeria.css" />
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
        <h5>Galeria zdjęć kotków</h5>
      </main>
      
      <div class="content">
        <?php
          $items_per_page = 2;
          $pictures = $cats;
          $totalItems = count($pictures);
          $currentPage = isset($_GET['page']) ? $_GET['page'] : 1;
          $urlPattern = '/cats/galeria?page=(:num)';
          $offset = ($currentPage - 1) * $items_per_page;
          $visiblePictures = array_slice($pictures, $offset, $items_per_page);
          $totalPages = ceil($totalItems / $items_per_page);

          $imageGap = 10;


          echo '<div class="gallery">';
            foreach ($visiblePictures as $picture) {
              $extension = pathinfo($picture['path'], PATHINFO_EXTENSION);
              echo '<a href="?action=viewimage&id=' . $picture['id'] . '">';
              echo '<img src="../images/miniaturki/' . $picture['id'] . '.' . $extension . '" alt="' . $picture['tytul'] . '"/>';
              echo '</a>';
              echo '<span> Tytuł: ' . $picture['tytul'];
              echo '<br />';
              echo 'Autor: ' . $picture['autor'] . '</span>';
              }
          echo '</div>';

          echo '<div class="paging">';
          for ($i = 1; $i <= $totalPages; $i++) {
              $isActive = ($i == $currentPage) ? 'active' : '';
              echo '<a href="?page=' . $i . '" class="' . $isActive . '">' . $i . '  </a>';
          }
          echo '</div>';
        ?>
      </div>
    </div>
  </body>
</html>

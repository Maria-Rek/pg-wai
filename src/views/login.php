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
        </ul>
      </nav>
      
      <footer>
        <div id="footer">
              <p>Maria Rek | Wytwarzanie aplikacji internetowych | 2024/2025</p> 
        </div>
      </footer>

      <main>
        <h5>Logowanie</h5>
      </main>

      <div class="content">
        <form method="POST" action="/login">
          <label for="identifier">Nazwa użytkownika:</label>
          <input type="text" id="identifier" name="identifier" required>
          <br/>
          <label for="password">Hasło:</label>
          <input type="password" id="password" name="password" required>
          <br/>
          <button type="submit">Zaloguj się</button>
          </form>
        <br/>
        <?php if (!empty($model['error'])): ?>
          <div style="color: red; font-weight: bold; margin-bottom: 10px;">
        <?= htmlspecialchars($model['error']) ?>
          </div>
        <?php endif; ?>
        <p>Nie masz konta? <br/><a href="/register">Zarejestruj się tutaj</a></p>
      </div>
    </div>
  </body>
</html>
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
        <h5>Rejestracja</h5>
      </main>

      <div class="content">
        <form method="POST" action="/register">
          <label for="username">Nazwa użytkownika:</label>
          <input type="text" id="username" name="username" required>
          <br/>
          <label for="email">E-mail:</label>
          <input type="email" id="email" name="email" required>
          <br/>
          <label for="password">Hasło:</label>
          <input type="password" id="password" name="password" required>
          <br/>
          <button type="submit">Zarejestruj się</button>
        </form>
        <br/>
        <?php if (!empty($model['user']['error'])): ?>
          <div style="color: red; font-weight: bold; margin-bottom: 10px;">
              <?= htmlspecialchars($model['user']['error']) ?>
          </div>
        <?php endif; ?>
        <p>Masz już konto? <br/><a href="/login">Zaloguj się tutaj</a></p>
      </div>
    </div>
  </body>
</html>
<!DOCTYPE html>
<html lang="pl-PL">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Galeria Kotków</title>
    <link rel="stylesheet" type="text/css" href="./static/style/style.css" />
    <?php
    include "./static/partial/top.phtml";
    ?>
  </head>
  <body>
      <main>
        <h5>Galeria przesłodkich kotków</h5>
      </main>
      
      <div class="content">
        <p>
        Witaj w galerii przesłodkich kotków! 🐾
        <br/>
        Prześlij zdjęcie słodkiego kotka lub oglądaj w galerii zdjęcia innych użytkowników.
        <br/>
        Miłego oglądania! 🤍
        </p>
        <br/>
        <?php if (!empty($_SESSION['user'])): ?>
          <p>Witaj, <?= htmlspecialchars($_SESSION['user']); ?>!</p>
          <a href="/logout">Wyloguj się</a>
        <?php else: ?>
          <a href="/login">Zaloguj się</a>
          <a href="/register">Zarejestruj się</a>
        <?php endif; ?>
        <?php if (!empty($_SESSION['success_message'])): ?>
        <div style="color: green; font-weight: bold; margin-bottom: 10px;">
            <?= htmlspecialchars($_SESSION['success_message']) ?>
        </div>
        <?php unset($_SESSION['success_message']);?>
        <?php endif; ?>
      </div>
  </body>
</html>
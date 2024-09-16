<?php
// Start the session
session_start();

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    header("Location: dashboard.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blackmarket</title>
    <link rel="shortcut icon" href="./assets/favicon.png" type="image/x-icon">

    <!-- Connect CSS -->
     <link rel="stylesheet" href="./css/global-style.css">
     <link rel="stylesheet" href="./css/style.css">

    <!-- Connect Tailwind -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/fa69f7130e.js" crossorigin="anonymous"></script>
    <script>
        tailwind.config = {
          theme: {
            extend: {
              colors: {
                "bg-color": '#212529',
              }
            }
          }
        }
      </script>
</head>
<body class="index">
    <!-- Header -->
    <header class="column sidebar bg-bg-color w-72 h-screen justify-between fixed">
        <div class="column">
            <p class="text-white opacity-25">Menu</p>
            <nav>
                <ul class="nav-menu space-y-3">
                    <li><a href="./login.php"><i class="fa-solid fa-right-to-bracket"></i> Login</a></li>
                    <li><a href="./register.php"><i class="fa-solid fa-right-to-bracket"></i> Registration</a></li>
                </ul>
            </nav>
        </div>
    </header>

        

    <main class="content ml-72 h-screen">
        <section class="section px-20 py-11">
            <div class="column">
                <h1 class="text-white text-7xl font-bold">Blackmarket</h1>
                <p class="text-white text-2xl font-medium mt-6">A bustling market known for its secretive nature, various weapons are bought and sold, catering to collectors, enthusiasts, and sometimes more dangerous clientele. Stalls are lined with an array of weaponry, from intricately designed swords and daggers to modern firearms and military-grade equipment. The atmosphere is tense yet professional, as vendors showcase their wares with pride, offering everything from rare historical artifacts to state-of-the-art technology. Conversations are often hushed, and transactions are discreet, reflecting the sensitive nature of the trade. Security is tight, and only those in the know find their way here.</p>
            </div>
        </section>
    </main>
</body>
</html>
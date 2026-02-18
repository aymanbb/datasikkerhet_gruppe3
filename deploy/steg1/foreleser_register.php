<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Foreleser Registration</title>
    <style>
        body {
            margin: 3rem;
            h1{
                display: flex;
                justify-content: center;
            }
            nav ul {
                padding: 0;
                list-style: none;
                margin: auto;
                display: flex;
                justify-content: center;
                li {
                    margin: 0.5rem;
                    a{
                        text-decoration: none;
                    }
                }
            }
            article {
                    border: 3px solid black;
                    padding: 2rem;
                    width: 50dvw;
                    margin: 1rem auto 1rem auto;
                    form{
                            display: flex;
                            flex-direction: column;
                            label{
                                    padding-top: 1rem;
                            }
                            button{
                                margin-top: 1rem;
                            }
                    }
                }
            section {
                display: flex;
                flex-direction: column;
                padding: 2rem 0rem 2rem 0rem;
            }
        }
    </style>
</head>
<body>
    <h1>Registrer foreleser</h1>
    <nav>
        <ul>
            <li><a href="index.php">Gå til forsiden</a></li>
            <li><a href="guest_login.php">Fortsett som gjest</a></li>
            <li><a href="#">Glemt passord?</a></li>
            <li><a href="subject_messages.php">Meldinger - HUSK Å FJERNE</a></li>
            <li><a href="emneoversikt.php">Emneoversikt ditto</a></li>
        </ul>
    </nav>
    <article>
        <h2>Registrer deg som foreleser</h2>
        <form action="foreleser_register_process.php" method="post" enctype="multipart/form-data">
            <label for="register-username">Navn:</label>
            <input type="text" id="registerUsername" name="register_username" required>

            <label for="register-email">E-post:</label>
            <input type="email" id="registerEmail" name="register_email" required>

            <label for="register-password">Passord:</label>
            <input type="password" id="registerPassword" name="register_password" required>

            <label for="subject-name">Emnenavn:</label>
            <input type="text" id="subjectName" name="register_subject" required>

            <label for="subject-pin">PIN-kode for emne:</label>
            <input type="text" id="subjectPin" name="register_pin" pattern="[0-9]*" maxlength="4" required>

            <label for="foreleser-image">Last opp bilde av deg selv:</label>
            <input type="file" id="foreleserImage" name="register_image" accept="image/*" required>

            <button type="submit" name="foreleser_register_submit">Send inn</button>

            <?php if ($message): ?>
                <p class="message"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

        </form>
    </article>
</body>
</html>
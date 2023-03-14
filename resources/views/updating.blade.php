<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Qualix - Update / mise à jour</title>

    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Source Sans Pro', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            border-right: 1px solid #a7abad;
            font-size: 26px;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 18px;
            text-align: center;
            padding: 10px;
            color: #2c3135;
        }
    </style>
</head>
<body>
<div class="flex-center position-ref full-height">
    <div class="code">503</div>
    <div class="message">
        <p>
        Qualix wird gerade aktualisiert. Bitte einen Moment Geduld.
        @if($_ENV['APP_CONTACT_LINK'] !== null && $_ENV['APP_CONTACT_LINK'] !== "")
            Falls diese Nachricht unerwartet erscheint, <a href="{{$_ENV['APP_CONTACT_LINK']}}">kontaktiere uns</a>.
        @endif
        </p>
        <p>
        Qualix est en cours de mise à jour. Veuille patienter un instant.
        @if($_ENV['APP_CONTACT_LINK'] !== null && $_ENV['APP_CONTACT_LINK'] !== "")
                Si ce message apparaît de manière inattendue, <a href="{{$_ENV['APP_CONTACT_LINK']}}">contacte-nous</a>.
        @endif
        </p>
    </div>
</div>
</body>
</html>

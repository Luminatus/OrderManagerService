<html>

<head>
    <title>{{ config('app.name') }} | Order Manager Service API</title>
    <link href="/swagger/style.css" rel="stylesheet">
</head>

<body>
    <div id="swagger-ui"></div>
    <script src="/swagger/jquery-2.1.4.min.js"></script>
    <script src="/swagger/swagger-bundle.js"></script>
    <script type="application/javascript">
        const ui = SwaggerUIBundle({
            url: "/swagger/swagger.yaml",
            dom_id: '#swagger-ui',
        });
    </script>
</body>

</html>
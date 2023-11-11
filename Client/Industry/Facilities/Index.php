<head>
    <script type="text/javascript"src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
    <?php
        echo <<<FORM1
            <form id='add' action='/Industry/Facilities/Index.php' method='post'>
                <input type='hidden' value='1' />
                <input form='add' type='submit' value='submit add form'>
            </form>
        FORM1;
    ?>
    <form id='del' action='/Industry/Facilities/Index.php' method='post'>
                <input type='hidden' value='2' />
                <input form='add' type='submit' value='submit add form'>
    </form>
</body>
<script>

</script>

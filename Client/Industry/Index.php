<?php
    echo "<h1 align='center'>Home</h1>";
    $connection  = mysqli_connect("localhost", "root", "");
    if (!$connection ) {
        //catch connection error
        echo "<h2>failed to connect to database</h2>";
        die('Could not connect: ' . mysqli_error());
    }
    else{
        mysqli_select_db($connection,"industry");
        $facilityNameQuery = "SELECT Name FROM FACILITY WHERE Agency_interest_number = 40313";
        $facilityNameResult = mysqli_query($connection, $facilityNameQuery);
        $facilityName = $facilityNameResult -> fetch_assoc();
        echo "<h2 align='center'>{$facilityName["Name"]}</h2>";
    }
?>
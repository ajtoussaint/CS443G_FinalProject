<head>
    <script type="text/javascript"src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
    <div style='display:flex; justify-content:center;'>
        <h1>Facilities</h1>
    </div>
    <div style='display:flex; justify-content:center;'>
        <a href='/Industry/Index.php'>Return to Home</a>
    </div>
    <br/>
    <div style='display:flex; justify-content:center;'>
        <button type='button' id='addFacility'>Add New Facility</button>
    </div>
    <br/>
    <div style='display:flex; justify-content:center;'>
        <form id='addFacilityForm' action='/Industry/Facilities.php' method='post' style='display:none; width:20%; min-width:20ch; border:solid; padding:1em; position:relative;'>
            <h3 style='margin:0;'>AI#:</h3>
            <input form='addFacilityForm' type='number' name='Agency_interest_number' min='1' max='9999999' />
            <h3 style='margin:0;'>Name:</h3>
            <input form='addFacilityForm' type='text' name='Name' maxlength='65' />
            <h3 style='margin:0;'>Permit Number:</h3>
            <input form='addFacilityForm' type='text' name='Permit_number' maxlength='11' />
            <h3 style='margin:0;'>Address:</h3>
            <input form='addFacilityForm' type='text' name='Address' maxlength='100' />
            <br></br>
            <input form='addFacilityForm' type='submit' value='Add Facility' />
            <button id='closeAddFacilityForm' type='button' style='position:absolute; right:0.5em; top:0.5em;'>X</button>
        </form>
    </div>

    <br/>
    <div id='orderingControlls' style='display:flex; justify-content:center;'>
        <form id='orderForm' action='/Industry/Facilities.php' method='get'>
            <h3 style='margin:0;'>Order By:</h3>
            <div style='display:flex;'>
                <div style='display:flex; flex-direction:column;'>
                    <label for='orderAI#'>
                        <input form='orderForm' id='orderAI#' type='radio' name='order_by' value='Agency_interest_number' checked/>
                    AI#</label>
                    <label for='orderName'>
                        <input form='orderForm' id='orderName' type='radio' name='order_by' value='Name'/>
                    Name</label>
                </div>
                <div style='display:flex; flex-direction:column;'>
                    <label for='ASC'>
                        <input form='orderForm' id='ASC' type='radio' name='order' value='ASC' checked/>
                    Ascending</label>
                    <lable for='DESC'>
                        <input form='orderForm' id='DESC' type='radio' name='order' value='DESC'/>
                    Descending</label>
                </div>
            </div>
            <input form='orderForm' type='submit' value='Filter' />
        </form>
    </div>
    <?php
        $configs = include('config.php');
        $connection  = mysqli_connect($configs['host'], $configs['username'], $configs['password']);
        if (!$connection ) {
            //catch connection error
            echo "<h2>failed to connect to database</h2>";
            die('Could not connect: ' . mysqli_error());
        }else{
            //connection established

            //if posting to add a new facility...
            if(isset($_POST["Agency_interest_number"])){
                $san = array();
                //sanitize
                foreach($_POST as $key => $value){
                    $san($key) = str_replace("'","\'",$value);
                }
                //add facility to DB
            }

            //load in data for all facilities and display
            mysqli_select_db($connection,$configs["database_name"]);
            $order = $_GET["order"] ?? "ASC";
            $order_by = $_GET["order_by"] ?? "Agency_interest_number";
            $facilityQuery = "SELECT Agency_interest_number, Name FROM facility ORDER BY {$order_by} {$order}";
            $facilityResult = mysqli_query($connection, $facilityQuery);
            echo "<div style='display:flex; justify-content:center;'>";
            echo "<table width='50%' border='1'>";
            echo "<tr><th>AI#</th><th>Facility Name</th></tr>";
            while($row = $facilityResult -> fetch_assoc()){
                echo <<<TABLE_ROW
                <tr>
                    <td style='text-align:center;'>
                        <a href='/Industry/Facilities.php?AI={$row["Agency_interest_number"]}' style='text-decoration:none; color:black; width:100%;'>
                            {$row["Agency_interest_number"]}
                        </a>
                    </td>
                    <td style='display:flex; justify-content:center;'>
                        <a href='/Industry/Facilities.php?AI={$row["Agency_interest_number"]}' style='text-decoration:none; color:black; width:100%; text-align:center;'>
                            {$row["Name"]}
                        </a>
                    </td>
                </tr>
                TABLE_ROW;
            }

            echo "</table>";
            echo "</div>";
        }
    ?>
</body>
<script>
    $("#addFacility").on('click', function() {
        $("#addFacilityForm").css("display", "block");
    })
    $("#closeAddFacilityForm").on('click', function() {
        $("#addFacilityForm").css("display", "none");
    })
</script>

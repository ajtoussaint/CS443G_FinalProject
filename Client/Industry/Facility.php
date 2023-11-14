<?php
    $configs = include('config.php');
    $connection  = mysqli_connect($configs['host'], $configs['username'], $configs['password']);
    if (!$connection ) {
        //catch connection error
        echo "<h2>failed to connect to database</h2>";
        die('Could not connect: ' . mysqli_error());
    }else{
        //connection established
        mysqli_select_db($connection,$configs["database_name"]);
        //if posting to add a new facility...
        if(isset($_POST["Action"])){
            //Switch on the various types of posting that may occur
            switch($_POST["Action"]){
                case "updateFacility":
                    $sanSQL = array();
                    //sanitize
                    foreach($_POST as $key => $value){
                        $sanSQL[$key] = str_replace("'","\'",$value);
                    }
                    $updateQuery = "UPDATE facility SET Name='{$sanSQL["Name"]}', Permit_number='{$sanSQL["Permit_number"]}', `Address`='{$sanSQL["Address"]}' WHERE Agency_interest_number = {$sanSQL["Agency_interest_number"]};";
                    $updateResult = mysqli_query($connection, $updateQuery); 
                break;

                //TODO: add case for adding a unit
            }
            header("Location: {$_SERVER['REQUEST_URI']}", true, 301);
            exit();
        }

        //if this is not a post request get the facility information
        $facilityQuery = "SELECT * FROM facility WHERE Agency_interest_number={$_GET["AI"]};";
        $facilityQueryResult = mysqli_query($connection, $facilityQuery);
        $facilityInfo = $facilityQueryResult -> fetch_assoc();
        $sanHTML = array();

        foreach($facilityInfo as $key => $value){
            $sanHTML[$key] = str_replace("'","&#39;",$value);
        }
    }
?>
<head>
    <script type="text/javascript"src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
    <div style='display:flex; align-items:center; flex-direction:column;'>
    <?php
        echo "<h1>{$facilityInfo["Agency_interest_number"]}: {$facilityInfo["Name"]}</h1>";
    ?>
        <div style='width:50%'>
            <?php
                echo "<h3>Permit#: {$facilityInfo["Permit_number"]}</h3>";
                echo "<h3>Address: {$facilityInfo["Address"]}</h3>";
            ?>
        </div>
        <button id='showUpdateForm' type='button'>Update Facility Info</button>
        <br/>
        
            <?php
                echo <<<UPDATE_INFO_FORM
                <form id='updateInfo' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style='display:none; border:solid; min-width:20ch; padding: 1em; position: relative;'>
                    <h3 style='margin:0;'>AI#:{$sanHTML["Agency_interest_number"]}</h3>
                    <input type='hidden' name='Action' value='updateFacility'/>
                    <input type='hidden' name='Agency_interest_number' value='{$facilityInfo["Agency_interest_number"]}'/>
                    <h3 style='margin:0;'>Name:</h3>
                    <input form='updateInfo' type='text' name='Name' maxlength='65' value='{$sanHTML["Name"]}'/>
                    <h3 style='margin:0;'>Permit Number:</h3>
                    <input form='updateInfo' type='text' name='Permit_number' maxlength='11' value='{$sanHTML["Permit_number"]}'/>
                    <h3 style='margin:0;'>Address:</h3>
                    <input form='updateInfo' type='text' name='Address' maxlength='100' value='{$sanHTML["Address"]}'/>
                    <br></br>
                    <input form='updateInfo' type='submit' value='Update' />
                UPDATE_INFO_FORM;
            ?>
            <button id='closeUpdateForm' type='button' style='position:absolute; right:0.5em; top:0.5em;'>X</button>
        </form>
        <div style='width:50%; min-width:20ch;'>
            <h2>Emission Units:</h2>
        </div>
        <button id='showAddUnit' type='button'>Add Unit</button>
        <br/>
        <?php
            echo <<<ADD_UNIT_FORM
                <form id='addUnit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style='display:none; border:solid; min-width:20ch; padding: 1em; position: relative;'>
                    <input type='hidden' name='Action' value='addUnit'/>
                    <input type='hidden' name='Facility_AI_number' value='{$facilityInfo["Agency_interest_number"]}' />
                    <h3 style='margin:0;'>Unit ID:</h3>
                    <input form='addUnit' type='text' name='Unit_id' maxlength='10' />
                    <h3 style='margin:0;'>Unit Name:</h3>
                    <input form='addUnit' type='text' name='Name' maxlength='65' />
                    <h3 style='margin:0;'>Unit Capacity:</h3>
                    <input form='addUnit' type='number' name='Capacity' step='0.1' placeholder='123456.7' /> units <input form='addUnit' name='Capacity_units' type='text' maxlength='15'  placeholder='tons/hr' />
                    <h3 style='margin:0;'>Unit burns fuel? </h3>
                    <label for='yes'>
                        <input id='yesFuel' type='radio' name='fuel' value='Yes' />
                    Yes</label>
                    <label for='no'>
                        <input id='noFuel' type='radio' name='fuel' value='No' checked/>
                    No</label>
                    <div style='display:none;' id='fuelInput'>
                        <h3 style='margin:0;'>Fuel Consumption</h3>
                        <input form='addUnit' type='number' name='Fuel_consumption' step='0.1' placeholder='12345.67'/> MMBtu/hr
                    </div>
                    <button id='closeAddUnitForm' type='button' style='position:absolute; right:0.5em; top:0.5em;'>X</button>
                </form>
            ADD_UNIT_FORM;
        ?>
        <div id='unitContainter' style='display:flex; width:80%; justify-content:space-around; border:solid;'>
            <?php
                //display all emission units
                $unitsQuery = "SELECT * FROM emission_unit WHERE Facility_AI_Number = {$facilityInfo["Agency_interest_number"]};";
                $unitsResult = mysqli_query($connection, $unitsQuery);

                $fuelQuery = "SELECT E.Unit_id, Fuel_consumption FROM fueled_units AS F JOIN emission_unit AS E WHERE Facility_AI_Number = {$facilityInfo["Agency_interest_number"]};";
                $fuelResult = mysqli_query($connection, $fuelQuery);
                
                $fueled = array();
                while($tuple = $fuelResult -> fetch_assoc()){
                    $fueled[$tuple["Unit_id"]] = $tuple["Fuel_consumption"];
                }

                //TODO: edit unit form
                
                //TODO: add limit form

                //TODO: delete limit form

                //TODO: limit details page or show details on this page
                
                //display info
                while($unit = $unitsResult -> fetch_assoc()){
                    $fuel = "";
                    if(isset($fueled[$unit["Unit_id"]]))
                        $fuel = $fueled[$unit["Unit_id"]] . " MMBtu/hr";
                    $limitQuery = "SELECT * FROM Emission_limit JOIN Unit_limits WHERE Unit_id = '{$unit["Unit_id"]}'";
                    $limitResult = mysqli_query($connection, $limitQuery);
                    $limitsHTML ='';
                    while($limit = $limitResult -> fetch_assoc()){
                        $limitsHTML += <<<LIMITS
                            <tr>
                                <td>{$limit["Parameter"]}:</td>
                                <td>{$limit["Limit"]} {$limit["Limit_units"]}</td>
                                <td>X</td>
                            </tr>
                        LIMITS;
                    }
                    
                    echo <<<EMISSION_UNIT
                        <div class='emissionUnit' style='max-width:40%; min-width:20ch; padding:0.5em; border:solid; display:flex; flex-direction:column;'>
                            <h3 style='align-self:center;'>{$unit["Unit_id"]}</h3>
                            <div id='{$unit["Unit_id"]}' style='display:block'>
                                <div>{$unit["Name"]}</div>
                                <div>{$unit["Capacity"]} {$unit["Capacity_units"]}</div>
                                <div>{$fuel}</div>
                            </div>
                            <form id='editUnit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style='display:block;'>
                                <div> Name: <input form ='editUnit' type='text' name='Name' value='{$unit["Name"]}' maxlength='65' /></div>
                                <br/>
                                <div>Capacity: <input form ='editUnit' type='number' name='Capacity' value='{$unit["Capacity"]}' step='0.1' />
                                Units: <input form ='editUnit' type='text' name='Capacity_units' value='{$unit["Capacity_units"]}' maxlength='15' /></div>
                                <br/>
                                Unit burns fuel?
                                <label for='yes'>
                                    <input id='yesFuelEdit' type='radio' name='fuel' value='Yes' checked />
                                Yes</label>
                                <label for='no'>
                                    <input id='noFuelEdit' type='radio' name='fuel' value='No' />
                                No</label>
                                <div style='display:block;' id='fuelInputEdit'>
                                    Fuel Consumption:
                                    <input form='editUnit' type='number' name='Fuel_consumption' step='0.1' placeholder='12345.67' value='{$fuel}'/> MMBtu/hr
                                </div>
                                <input type='submit' value='Update' form='editUnit' />                                
                            </form>
                            <br/>
                            <h4 style='margin:0;'>Emission Limits:</h4>
                            <br/>
                            <table>
                                {$limitsHTML}
                            </table>            
                            <button id='showAddLimit' type='button'>Add Limit</button>
                            <br/>
                            <div style='display:flex; justify-content:space-around;'>
                                <button id='showEditUnit' type='button'>Edit</button>
                                <button id='deleteUnit' type='button'>Delete</button>
                            </div>
                        </div>
                        
                    EMISSION_UNIT;
                }
            ?>
        </div>
    </div>
    
</body>

<script>
    $("#showUpdateForm").on('click', function() {
        $("#updateInfo").css("display", "block");
    })
    $("#closeUpdateForm").on('click', function() {
        $("#updateInfo").css("display", "none");
    })

    $("#yesFuel").on('change', function(){
        if($("#yesFuel").attr("checked", "checked")){
            $("#fuelInput").css("display", "block");
        }
    })
    $("#noFuel").on('change', function(){
        if($("#noFuel").prop("checked", true)){
            $("#fuelInput").css("display", "none");
        }
    })

    $("#yesFuelEdit").on('change', function(){
        alert("change");
        if($("#yesFuelEdit").prop("checked", true)){
            $("#fuelInputEdit").css("display", "block");
        }else{
            $("#fuelInputEdit").css("display", "none");
        }
    })

    $("#showAddUnit").on('click', function() {
        $("#addUnit").css("display", "block");
    })
    $("#closeAddUnitForm").on('click', function() {
        $("#addUnit").css("display", "none");
    })

</script>
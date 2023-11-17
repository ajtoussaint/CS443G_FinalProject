<?php
    $configs = include('config.php');
    function sanHTML($str){
        return str_replace("'","&#39;",$str);
    };
    function sanSQL($str){
        return str_replace("'","\'",$str);
    };

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
            $sanSQL = array();
            foreach($_POST as $key => $value){
                $sanSQL[$key] = sanSQL($value);
            }
            switch($_POST["Action"]){
                case "updateFacility":
                    $updateQuery = "UPDATE facility SET Name='{$sanSQL["Name"]}', Permit_number='{$sanSQL["Permit_number"]}', `Address`='{$sanSQL["Address"]}' WHERE Agency_interest_number = {$sanSQL["Agency_interest_number"]};";
                    $updateResult = mysqli_query($connection, $updateQuery); 
                break;

                case "addUnit":
                    //round capacity to 1dp
                    $sanSQL["Capacity"] = round($sanSQL["Capacity"], 1);
                    $addUnitQuery = "INSERT INTO Emission_unit VALUES('{$sanSQL["Unit_id"]}', '{$sanSQL["Name"]}', '{$sanSQL["Capacity"]}', '{$sanSQL["Capacity_units"]}', '{$sanSQL["Facility_AI_number"]}');";
                    $addUnitResult = mysqli_query($connection, $addUnitQuery);            
                    //check for fuel consumption
                    if($sanSQL["Fuel_consumption"] > 0){
                        $sanSQL["Fuel_consumption"] = round($sanSQL["Fuel_consumption"], 2);
                        $addFuelQuery = "INSERT INTO Fueled_units VALUES('{$sanSQL["Unit_id"]}', '{$sanSQL["Fuel_consumption"]}');";
                        $addFuelResult = mysqli_query($connection, $addFuelQuery);
                    }
                break;

                case "editUnit":
                    $sanSQL["Capacity"] = round($sanSQL["Capacity"], 1);
                    $updateUnitQuery = "UPDATE emission_unit SET `Name`='{$sanSQL["Name"]}', `Capacity`='{$sanSQL["Capacity"]}', `Capacity_units`='{$sanSQL["Capacity_units"]}' WHERE `Unit_id`='{$sanSQL["Unit_id"]}';";
                    $updateUnitResult = mysqli_query($connection, $updateUnitQuery);
                    $removeFuelQuery = "DELETE FROM Fueled_units WHERE `Unit_id` = '{$sanSQL["Unit_id"]}';";
                    $removeFuelResult = mysqli_query($connection, $removeFuelQuery);
                    if($sanSQL["Fuel_consumption"] > 0){
                        $sanSQL["Fuel_consumption"] = round($sanSQL["Fuel_consumption"], 2);    
                        $addFuelQuery = "INSERT INTO Fueled_units VALUES('{$sanSQL["Unit_id"]}', '{$sanSQL["Fuel_consumption"]}');";
                        $addFuelResult = mysqli_query($connection, $addFuelQuery);
                    }
                break;

                case "deleteUnit":
                    $deleteUnitQuery = "DELETE FROM fueled_units WHERE `Unit_id` = '{$sanSQL["Unit_id"]}'; 
                    DELETE FROM unit_limits WHERE `Unit_id` = '{$sanSQL["Unit_id"]}'; 
                    DELETE FROM emission_unit WHERE `Unit_id` = '{$sanSQL["Unit_id"]}';";
                    $deleteUnitResult = mysqli_multi_query($connection, $deleteUnitQuery);
                break;

                case "addLimit":
                    if($sanSQL["Citation"] != "NULL"){
                        $sanSQL["Citation"] = "'". $sanSQL["Citation"] . "'";
                    }
                    $addLimitQuery = "INSERT INTO emission_limit (`Limit_id`, `Parameter`, `Limit`, `Limit_units`, `Compliance_demonstration_method`,`Citation`) SELECT 
                    MAX(Limit_id)+1,
                    '{$sanSQL["Parameter"]}',
                    '{$sanSQL["Limit"]}',
                    '{$sanSQL["Limit_units"]}',
                    '{$sanSQL["Compliance_demonstration_method"]}'
                    , {$sanSQL["Citation"]} FROM emission_limit;
                    INSERT INTO unit_limits (`Unit_id` ,`Limit_id`) SELECT '{$sanSQL["Unit_id"]}', MAX(Limit_id) FROM emission_limit;";
                    $addLimitResult=mysqli_multi_query($connection, $addLimitQuery);
                break;

                case "addExistingLimit":
                    $addELimQuery = "INSERT INTO `unit_limits` VALUES('{$sanSQL["Unit_id"]}', '{$sanSQL["Limit_id"]}');";
                    $addELimResult = mysqli_query($connection, $addELimQuery);
                break;

                case "deleteLimit":
                    $deleteLimitQuery = "DELETE FROM `unit_limits` WHERE `Limit_id` = '{$sanSQL["Limit_id"]}' AND `Unit_id` = '{$sanSQL["Unit_id"]}';
                    DELETE FROM `emission_limit` WHERE `Limit_id` NOT IN (SELECT Limit_id FROM unit_limits);";
                    $deleteLimitResult = mysqli_multi_query($connection, $deleteLimitQuery);
                break;
                    
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
        <div style='display:flex; justify-content:center;'>
            <a href='/Industry/Facilities.php'>Return to facilities list</a>
        </div>
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
                    <input form='addUnit' type='text' name='Unit_id' maxlength='10' id='Unit_id_input' />
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
                        <input form='addUnit' type='number' name='Fuel_consumption' step='0.01' min='0' value='0' placeholder='12345.67'/> MMBtu/hr
                    </div>
                    <input form='addUnit' type='submit' value='Add Unit'/>
                    <button id='closeAddUnitForm' type='button' style='position:absolute; right:0.5em; top:0.5em;'>X</button>
                </form>
            ADD_UNIT_FORM;
        ?>
        <div id='unitContainter' style='display:flex; flex-wrap: wrap; width:80%; justify-content:space-around;'>
            <?php
                //display all emission units
                $unitsQuery = "SELECT * FROM emission_unit WHERE Facility_AI_Number = {$facilityInfo["Agency_interest_number"]};";
                $unitsResult = mysqli_query($connection, $unitsQuery);

                $fuelQuery = "SELECT E.Unit_id, Fuel_consumption FROM fueled_units AS F JOIN emission_unit AS E ON F.Unit_id=E.Unit_id WHERE Facility_AI_Number = {$facilityInfo["Agency_interest_number"]};";
                $fuelResult = mysqli_query($connection, $fuelQuery);
                
                $fueled = array();
                while($tuple = $fuelResult -> fetch_assoc()){
                    $fueled[$tuple["Unit_id"]] = $tuple["Fuel_consumption"];
                }
                //display info
                while($unit = $unitsResult -> fetch_assoc()){
                    $fuel = "";
                    $fuelHTML = <<<FUEL_HTML
                        Unit burns fuel?
                        <label for='yes'>
                            <input class='yesFuelEdit' type='radio' name='fuel' value='Yes' />
                        Yes</label>
                        <label for='no'>
                            <input class='noFuelEdit' type='radio' name='fuel' value='No' checked/>
                        No</label>
                        <div style='display:none;' class='fuelInputEdit'>
                            Fuel Consumption:
                            <input type='number' name='Fuel_consumption' step='0.1' placeholder='12345.67' value='{$fuel}'/> MMBtu/hr
                        </div>
                    FUEL_HTML;
                    if(isset($fueled[$unit["Unit_id"]])){
                        $fuel = $fueled[$unit["Unit_id"]] . " MMBtu/hr";
                        $fuelHTML = <<<FUEL_HTML
                            Unit burns fuel?
                            <label for='yes'>
                                <input class='yesFuelEdit' type='radio' name='fuel' value='Yes' checked/>
                            Yes</label>
                            <label for='no'>
                                <input class='noFuelEdit' type='radio' name='fuel' value='No' />
                            No</label>
                            <div class='fuelInputEdit'>
                                Fuel Consumption:
                                <input type='number' name='Fuel_consumption' step='0.1' placeholder='12345.67' value='{$fueled[$unit["Unit_id"]]}'/> MMBtu/hr
                            </div>
                        FUEL_HTML;
                    }
                    $limitQuery = "SELECT * FROM Emission_limit JOIN Unit_limits ON emission_limit.Limit_id=unit_limits.Limit_id WHERE Unit_id = '{$unit["Unit_id"]}';";
                    $limitResult = mysqli_query($connection, $limitQuery);
                    $limitsHTML ='';
                    while($limit = $limitResult -> fetch_assoc()){
                        $citation = "";
                        if($limit["Citation"] != "")
                            $citation .= "Citation: " . $limit["Citation"] . ",";
                        $limitsHTML .= <<<LIMITS
                            <tr>
                                <td>{$limit["Parameter"]}:</td>
                                <td>{$limit["Limit"]} {$limit["Limit_units"]}</td>
                                
                                
                                <td style='display:flex; align-items:center; justify-content:center;'><img src='/Industry/infoIcon.png' style='margin:auto; height:20px; width:20px;' title='{$citation} Compliance Demonstration Method: {$limit["Compliance_demonstration_method"]}'></td>
                                <td>
                                    <form class='delteLimit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post'>
                                        <input type='hidden' name='Action' value='deleteLimit'/>
                                        <input type='hidden' name='Limit_id' value='{$limit["Limit_id"]}'/>
                                        <input type='hidden' name='Unit_id' value='{$unit["Unit_id"]}'/>
                                        <input type='submit' value='X'/>
                                    </form>
                                </td>
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
                            <form id='edit{$unit["Unit_id"]}' class='editUnit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style='display:none;'>
                                <input type='hidden' name='Unit_id' value='{$unit["Unit_id"]}' />
                                <input type='hidden' name='Action' value='editUnit' />
                                <div> Name: <input form ='edit{$unit["Unit_id"]}' type='text' name='Name' value='{$unit["Name"]}' maxlength='65' /></div>
                                <br/>
                                <div>Capacity: <input form ='edit{$unit["Unit_id"]}' type='number' name='Capacity' value='{$unit["Capacity"]}' step='0.1' />
                                Units: <input form ='edit{$unit["Unit_id"]}' type='text' name='Capacity_units' value='{$unit["Capacity_units"]}' maxlength='15' /></div>
                                <br/>
                                {$fuelHTML}
                                <br/>
                                <input type='submit' value='Update' form='edit{$unit["Unit_id"]}' />
                                <button type='button' class='cancelEdit' value='{$unit["Unit_id"]}'>Cancel</button>                             
                            </form>
                            <br/>
                            <button class='showEditUnit' value='{$unit["Unit_id"]}' type='button'>Edit</button>
                            <br/>
                            <h4 style='margin:0;'>Emission Limits:</h4>
                            <br/>
                            <table>
                                {$limitsHTML}
                            </table>            
                            <button class='showAddLimit' value='{$unit["Unit_id"]}' type='button'>Add Limit</button>
                            <br/>
                            <div style='display:flex; justify-content:space-around;'>
                                
                                <button class='deleteUnit' value='{$unit["Unit_id"]}' type='button'>Delete</button>
                            </div>
                        </div>
                        
                    EMISSION_UNIT;
                }
            ?>
            
            
        </div>
    </div>
    
    <div id='viewWrapper' style='width:100vw; height:100vh; background:rgba(100,100,100,0.5);position:absolute; top:0; left:0; display:none; align-items:center; flex-direction:column;'>
        
        <div style=' display:flex; flex-direction:column; align-items:center; width:50%; border:solid; margin-top:5%; position:relative; background:white; padding:2ch;'>
            <div>
                <label for='useExistingLimit'>
                    <input id='useExistingLimit' type='radio' name='limitType' value='existing' checked/>
                Add Existing Limit</label>
                <label for='useNewLimit'>
                    <input id='useNewLimit' type='radio' name='limitType' value='new' />
                Add New Limit</label>
            </div>
            <?php
                echo "<form id='addExistingLimit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style=' display:flex; flex-direction:column; width:50%; border:solid; margin-top:5%; position:relative; background:white; padding:2ch;'>";
                echo "<h2 style='align-self:center;'>Select Limit to Add</h2>";
                echo "<input type='hidden' name='Action' value='addExistingLimit' />";
                echo "<input type='hidden' id='addExistingLimitUnit' name='Unit_id' value='' />";
                echo "<select form='addExistingLimit' name='Limit_id'>";
                $allLimitQuery = "SELECT * FROM emission_limit;";
                $allLimitResult = mysqli_query($connection, $allLimitQuery);
                while($limit = $allLimitResult -> fetch_assoc()){
                    $citation = "Special Limit";
                    if(isset($limit["Citation"])){
                        $citation = $limit["Citation"];
                    }
                    echo "<option value='{$limit["Limit_id"]}'>$citation: {$limit["Limit"]}{$limit["Limit_units"]} {$limit["Parameter"]}</option>";
                }
                echo "</select>";
                echo "<input type='submit' form='addExistingLimit' value='Add Limit' />";
                echo "</form>";
            ?>

            
            <?php
                $citationInput = "<select form='addLimit' name='Citation'> <option value='NULL'>No applicable Citation</option>";
                $citations = mysqli_query($connection, "SELECT `Citation` FROM regulation;");
                while($row = $citations ->fetch_assoc()){
                    $citationInput .= "<option value='{$row["Citation"]}'>{$row["Citation"]}</option>";
                }
                $citationInput.="</select>";

                echo <<<ADD_NEW_LIMIT_FORM
                <form id='addLimit' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' method='post' style=' display:none; flex-direction:column; width:50%; border:solid; margin-top:5%; position:relative; background:white; padding:2ch;'>
                    <h2 style='align-self:center;'>Add Limit</h2> 
                    <input type='hidden' name='Action' value='addLimit' />
                    <input type='hidden' id='addLimitUnit' name='Unit_id' value='' />
                    Parameter: <input form='addLimit' tyep='text' maxlength='35' name='Parameter' style='max-width:40ch;' />
                    <br/>
                    Limit: <input form='addLimit' type='number' step='0.001' max='9999' name='Limit' style='max-width:10ch;' /> units <input form='addLimit' type='text' name='Limit_units' maxlength='15' style='max-width:15ch;'/>
                    <br/>
                    Compliance Demonstration Method:
                    <br/>
                    <textarea name='Compliance_demonstration_method' form='addLimit' style='height:10ch; width:80%; resize:vertical;' maxlength='2500' ></textarea>
                    <br/>
                    {$citationInput}
                    <br/>
                    <input form='addLimit' type='submit' value='Add Limit to Unit' style='align-self:center;'/> 
                </form>
                ADD_NEW_LIMIT_FORM;
            ?>
            <button type='button' style='position:absolute; top:0; right:0; margin:1ch;' id='closeAddLimit'>X</button>
        </div>
    </div>
    <?php
        echo <<<DELETE_UNIT_FORM
        <form id='deleteUnit'  method='post' action='/Industry/Facility.php?AI={$facilityInfo["Agency_interest_number"]}' style='display:none;'>
                <input type='hidden' form='deleteUnit' name='Unit_id' value='' id='unitToDelete'/>
                <input type='hidden' form='deleteUnit' name='Action' value='deleteUnit'/>
        </form>
        
        DELETE_UNIT_FORM;
    ?>
</body>

<script>
    let AllUnits = [<?php
            $unit_ids = mysqli_query($connection, "SELECT `Unit_id` FROM emission_unit;");
            while($id = $unit_ids -> fetch_assoc()){
                echo "'{$id["Unit_id"]}',";
            }
        ?>]
    $("#showUpdateForm").on('click', function() {
        $("#updateInfo").css("display", "block");
    })
    $("#closeUpdateForm").on('click', function() {
        $("#updateInfo").css("display", "none");
    })

    $("#yesFuel").on('change', function(){
        if($("#yesFuel").prop("checked", true)){
            $("#fuelInput").css("display", "block");
        }
    })
    $("#noFuel").on('change', function(){
        if($("#noFuel").prop("checked", true)){
            $("#fuelInput").css("display", "none");
        }
    })

    $(".yesFuelEdit").on('change', function(){
        if($(this).prop("checked", true)){
            $(this).parent().parent().find(".fuelInputEdit").css("display", "block");
        }
    })

    $(".noFuelEdit").on('change', function(){
        
        if($(this).prop("checked", true)){
            
            var fuelEdit = $(this).parent().parent().find(".fuelInputEdit");
            fuelEdit.css("display", "none");
            //set the value to 0
            fuelEdit.find("input").val(0);
        }
    })

    $("#showAddUnit").on('click', function() {
        $("#addUnit").css("display", "block");
    })
    $("#closeAddUnitForm").on('click', function() {
        $("#addUnit").css("display", "none");
    })

    
    $(".showEditUnit").on('click', function(){
        var unitID = $(this).val();
        $("#edit" + unitID).css('display', 'block');
        $("#" + unitID).css('display', 'none');
    })

    $(".cancelEdit").on('click', function(){
        var unitID = $(this).val();
        $("#edit" + unitID).css('display', 'none');
        $("#" + unitID).css('display', 'block');
    })

    $("#closeAddLimit").on('click', function(){
        $("#viewWrapper").css("display", "none");
    })

    $(".showAddLimit"). on('click', function(){
        $("#addLimitUnit").val($(this).val());
        $("#addExistingLimitUnit").val($(this).val());
        $("#viewWrapper").css("display", "flex");
    })

    $(".deleteUnit").on('click', function(){
        var res = confirm("Are you sure you want to delete this Unit?");
        if(res){
            //change the hidden input of the delete form to the given unit
            $("#unitToDelete").val($(this).val());
            //run submit            
            $("#deleteUnit").trigger("submit");
        }
    })

    $(".delteLimit").on('submit', function(){
        return confirm("Are you sure you want to remove this limit from the unit?");
    })

    $("#useExistingLimit").on('change', function(){
        if($("#useExistingLimit").prop("checked", true)){
            $("#addLimit").css("display", "none");
            $("#addExistingLimit").css("display", "flex");
        }
    })

    $("#useNewLimit").on('change', function(){
        if($("#useNewLimit").prop("checked", true)){
            $("#addLimit").css("display", "flex");
            $("#addExistingLimit").css("display", "none");
        }
    })

    $("#addUnit").on('submit', function(){
        if(AllUnits.indexOf($("#Unit_id_input").val()) >= 0){
            alert("A unit with this ID already exists, units must have unique IDs. Please choose a different ID and try again.");
            return false;
        }
    })

</script>
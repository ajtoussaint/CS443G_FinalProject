<head>
    <script type="text/javascript"src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
</head>
<body>
    <?php
        function sanHTML($str){
            return str_replace("'","&#39;",$str);
        };
        function sanSQL($str){
            return str_replace("'","\'",$str);
        };

        $connection  = mysqli_connect("localhost", "root", "");
        if (!$connection ) {
            //catch connection error
            echo "<h2>failed to connect to database</h2>";
            die('Could not connect: ' . mysqli_error());
        }
        else{
            //Database connection established
            mysqli_select_db($connection,"industry");
            if(isset($_POST["Action"])){
                $sanCitation = str_replace("'", "\'",$_POST["Citation"]);
                if(isset($_POST["text"]))
                    $sanText = str_replace("'", "\'",$_POST["text"]);
                switch($_POST["Action"]){
                    case "POST":
                        //TODO: ensure the citation doesnt already exist to prevent an error;
                        $addRegulationQuery = "INSERT INTO `regulation` VALUES('{$sanCitation}', '{$sanText}');";
                        $addRegulationResult = mysqli_query($connection, $addRegulationQuery);
                    break;
                    case "PUT":
                        $updateRegulationQuery = "UPDATE `regulation` SET `text`='{$sanText}' WHERE Citation='{$sanCitation}'";
                        $updateRegulationResult = mysqli_query($connection, $updateRegulationQuery);
                    break;
                    case "REMOVE":
                        $deleteRegulationQuery = "DELETE FROM `regulation` WHERE Citation='{$sanCitation}'";
                        $deleteRegulationResult = mysqli_query($connection, $deleteRegulationQuery);
                    break;
                }
                header("Location: {$_SERVER['REQUEST_URI']}", true, 301);
                exit();
            }
            echo "<h1 align='center'>Regulations</h1>";
            echo "<div align='center'>";
            echo "<a href='/Industry/Index.php'>Return to Home</a>";
            echo "</div>";
            echo "<br></br>";
            echo "<div align='center'>";
            echo "<button type='button' id='addRegulation'>Add New Regulation</button>";
            echo "</div>";
            echo <<<NEW_REG_FORM
            <br></br>
            <div id='newRegForm' align='center' style='display: none !important;'>
                <form id='addReg' action="/Industry/Regulations.php" method="post" style='width:50%; min-width:20ch; border:solid; padding:1em; position:relative;' align='left'>
                    <input form='addReg' type='hidden' name='Action' value='POST' />
                    <h3 style='margin:0'>Citation</h3>
                    <input form='addReg' type='text' id='citationInput' name='Citation' maxlength='20'/>
                    <h3 style='margin:0'>Regulation Text:</h3>
                    <textarea form='addReg' id='textInput' name='text' maxlength='2500' style='resize:vertical; width:100%;'></textarea>
                    <div id='textCharsRemaining'>Remaining Characters:2500</div>
                    <br></br>
                    <input form='addReg' type='submit' value="Add Regulation" />
                    <button id='closeAddRegForm' type='button' style='position:absolute; right:0.5em; top:0.5em;'>X</button>
                <form>
            </div>
            NEW_REG_FORM;

            echo "<br></br>";
            
            echo "<input id='editing' type='hidden' value=0 />";

            echo "<table width='80%' align='center' border='1'>";
            echo "<tr><th>Citation</th><th>Regulation Text</th><th></th></tr>";
            $regulationsQuery = "SELECT * FROM Regulation";
            $regulationsResult = mysqli_query($connection, $regulationsQuery);
            $i = 0;
            while($row = $regulationsResult -> fetch_assoc()){
                echo <<<TABLE_ROW
                <tr id={$i}>
                <td align='center' id='Citation_{$i}' style='width:20ch'>{$row["Citation"]}</td>
                <td id='text_{$i}'>{$row["text"]}</td>
                <td align='center' style='width:20ch'>
                <button type='button' class='editReg' value='{$i}'>Edit</button>
                   |   
                <button type='button' class='deleteReg' value='{$i}'>Delete</button>
                </tr>
                TABLE_ROW;
                $i++;
            }
            echo "</table>";
        }
    ?>
    <div id='confirmDelete' style='display:none; text-align:center; align-items:center; width:100vw; height:100vh; background:rgba(100,100,100,0.5);position:absolute; top:0; left:0;'>
        <div style='background:white; border:solid; width:35%; margin:10% auto; height: auto;'>
            <button id="cancelDelete" type='button' style='margin-bottom:2ch;'>No, go back</button>
        </div>
    </div>
</body>

<script>
    let allRegulationCitations = [];
    <?php
        $citations = mysqli_query($connection, "SELECT `Citation` FROM Regulation");
        while($citation = $citations -> fetch_assoc()){
            $cite = sanSQL($citation["Citation"]);
            echo "allRegulationCitations.push('{$cite}');";
        }
    ?>

    $("#addRegulation").click( function (){
        $("#newRegForm").attr("style", "");
    })
    $("#closeAddRegForm").click( function (){
        $("#newRegForm").attr("style", "display: none !important;");
    })

    //used to update remaining character count
    $("#textInput").keyup(updateCount)
    $("#textInput").keydown(updateCount)
    function updateCount(){
        var text = $("#textInput").val();
        var remaining = 2500-text.length;
        $("#textCharsRemaining").text("Remaining Characters:" + remaining);
    }

    $(".editReg").on("click", function(){
        if($("#editing").val() == 0){
            $("#editing").val(1);
            var number = $(this).val();
            var citationElement = $("#Citation_" + number);
            var textElement = $("#text_" + number);
            var citation = citationElement.text();
            var text = textElement.text();
            html = '';
            html += "<td align='center' style='width:20ch;'>";
            html += '<form id="editForm" action="/Industry/Regulations.php" method="post">';
            html += "<input type='hidden' name='Action' value='PUT' />";
            html += "<input type='hidden' name='Citation' value='"+ citation +"'/>";
            html += citation;
            html += "</form></td>";
            html += "<td style='height:" + textElement.height() + ";'>";
            html += "<h3 style='margin:0'>Regulation Text:</h3><textarea form='editForm' id='textEditInput' name='text' maxlength='2500' style='resize:vertical; width:100%; height:80%;'>" + text + "</textarea>";
            html += "</td>";
            html += "<td align='center' style='width:8ch;'>";
            html += "<input type='submit' form='editForm' value='Update' />";
            html += "<br></br>";
            html += "<a href='/industry/Regulations.php'>cancel</a>";
            html += "</td>";
            $("#"+number).empty();
            $("#"+number).append(html);
        }else{
            alert("Edit one regulation at a time");
        }
    })

    //confirm deletes
    $(".deleteReg").on("click", function(){
        //change the confirmation form to delete the correct thing
        var number = $(this).val();
        var reg = $("#Citation_" + number).text()
        $("#confirmDelete").append("")
        $("#regToDelete").val(reg);
        $("#confirmDelete").css("display", "block");
    })

    $("#cancelDelete").on("click", function(){
        $("#confirmDelete").css("display", "none");
    })

    $(document).ready(function() {
        var html = "<form id='deleteReg' action='/Industry/Regulations.php' method='post'>";
        html += "<input form='deleteReg' type='hidden' name='Action' value='REMOVE'/>";
        html += "<input form='deleteReg' id='regToDelete' type='hidden' name='Citation' value=''/>";
        html += "<br></br>";
        html += "<div>Are you sure you want to delete this regulation? This action cannot be undone.</div>";
        html += "<br></br>";
        html += "<input form='deleteReg' type='submit' value='Delete Regualtion' style='color:red; margin:auto;'/>";
        html += "</form>";
        $("#confirmDelete").find("div").prepend(html);
    })

    $("#addReg").on('submit', function(){
        if(allRegulationCitations.indexOf($("#citationInput").val()) >= 0){
            alert("This citation already exists. Each regulation must have a unique citation. Please use a new citation and try again.");
            return false;
        }
    })

</script>
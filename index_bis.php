<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->

<html>
    
    <head>
        <meta charset="UTF-8">
        <title>Test pagina</title>
        <link rel="stylesheet" type="text/css" href="eline.css">
        <script type="text/javascript" src="js/jquery/jquery.js"></script>

        <script type="text/javascript">

            function Browse(location)
            {
                document.getElementById('navigation').src = String(location);
            }

        </script>
    </head>

<!--    <body>-->
    <body onload="Browse('http://172.16.252.1//zabbix/index.php')">
        
        <table>
            <tr>
                <td style="width: 10%">
                    <div id="MenuBar">

                        <?php

                            $buttons = array
                            (
                                array
                                (
                                    "id"    =>  "btn_NewUser",
                                    "name"  =>  "Nieuwe gebruiker",
                                    "src"   =>  "http://localhost/projects/WebInterface/mysites/NewCustomerPage.php",
                                    "ajax"  =>  false
                                ),
                                array
                                (
                                    "id"    =>  "btn_NewCustomer",
                                    "name"  =>  "Nieuwe klant",
                                    "src"   =>  "http://172.16.252.1//zabbix/index.php",
                                    "ajax"  =>  true
                                ),
                                array
                                (
                                    "id"    =>  "btn_Test",
                                    "name"  =>  "Test",
                                    "src"   =>  "http://172.16.252.1//zabbix/index.php",
                                    "ajax"  =>  false
                                )
                            );

                            foreach ($buttons as $item)
                            {
                                echo '<div class="button" id=' . $item["id"] . ' onclick="Browse(\'' . $item["src"] . '\')">' . $item["name"]  . '</div>' . "\n";
                            }
                        ?>

                    </div>      
                </td>
                <td style="width: 90%">
                    <div class="ZabbixInterface">
                        <iframe id="navigation" class="ZabbixInterface"></iframe>
                    </div>
                </td>
            </tr>
        </table>
     
        <script type="text/javascript">

            <?php

                foreach ($buttons as $item)
                {
                    if($item["ajax"])
                    {
                        $s =
                            "$('#" . $item["id"] . "').click(function()
                            {
                                $.ajax(
                                {
                                    method:         'POST',
                                    url:            'eline_scripts.php',
                                    data:           {action: '" . $item["id"] . "'},
                                    success:        function(output)
                                    {
                                       alert(output);
                                    }
                                });
                            });";

                        echo $s;
                        echo "\r\n\r\n\t";
                    }
                }
            ?>

            $(document).ready(function()
            {
                $.ajax(
                {
                    method:         'POST',
                    url:            'eline_scripts.php',
                    data:           {action: 'Authenticate'},
                    success:        function(output)
                    {
//                        alert(output);
                    }
                });
            });

//            $("#btn_Test").click(function()
//            {
//                var frame = document.getElementById("navigation");
//                alert();
//                alert($("#name").val());
//
//                $("#request").val("");
//                $("#name").val("Developer");
//                $("#password").val("BeDe159-+Line");
//                $("#autologin").attr("checked", true);
//
//                alert("ok");
//                $("#enter").click();
//            });

        </script>
        
    </body>    
</html>

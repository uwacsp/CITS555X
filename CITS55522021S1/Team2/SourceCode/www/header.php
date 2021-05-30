<?php require('../private_html/checkuserredirect.php');?>
        <div class="header" id="header">
            <div class="header_resize">
                <div class="logo" style="padding-left:60px; width:600px;">
                    <h1 style="width:500px;">
                        <a href="#"><span>REV</span>iew</a> <small><b>The University
                            of Western Australia</b></small>
                    </h1>
                </div>
                <div class="clr"></div>
                <div class="menu_nav">
                    <div class="searchform">
                    <?php echo $_SESSION['username']; ?>
                        | <a href="../login/logout.php">Logout</a>
                    </div>
                    <ul>
                            <li><a href="index.php">Index</a></li>
                            <li><a href="device_operation_index.php">Device</a></li>
                            <li><a href="device_mapping_index.php">Device mapping</a></li>
                            <li><a href="attribute_operation_index.php">Attribute</a></li>
                            <li><a href="attribute_mapping_index.php">Attribute mapping</a></li>
                            <li><a href="combination_index.php">Combination</a></li>
                    </ul>
                    <div class="clr"></div>
                </div>
            </div>
        </div>
        
<script type="text/javascript">
idleTime = 0;
$(document).ready(function () {
    //Increment the idle time counter every minute.
    var idleInterval = setInterval("timerIncrement()", 60000); // 1 minute

    //Zero the idle timer on mouse movement.
    $(this).mousemove(function (e) {
        idleTime = 0;
    });
    $(this).keypress(function (e) {
        idleTime = 0;
    });
})
function timerIncrement() {
    idleTime = idleTime + 1;
    if (idleTime > 20) { // 20 minutes
        window.location.reload();
    }
}
</script>
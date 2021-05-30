<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>The REV Project - EMS</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link href="../login/style.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../login/js/cufon-yui.js"></script>
<script type="text/javascript" src="../login/js/arial.js"></script>
<script type="text/javascript" src="../login/js/cuf_run.js"></script>
<script type="text/javascript" src="../login/js/serialize_array_php.js"></script>
<script type="text/javascript" src="j../login/s/jquery.js"></script>
<link href="../login/jqModal.css" rel="stylesheet" type="text/css" />
</head>
<body>
	<div class="main">
        <?php require('header.php');?>
		<div class="content">
			<div class="content_resize">
				<div class="article" style="padding:16px 0 0 0;">
				<div id="datatable" style="text-align: center; width:900px; margin-left:auto; margin-right:auto;">
				  <?php $sql = "select * from ems_transaction_view order by timestamp desc limit 100;";  $result = $db->query($sql); ?>
				  <table>
						<thead>
							<tr>
								<th>device_name</th>
								<th>attribute_name</th>
								<th>value</th>
								<th>unit</th>
								<th>timestamp</th>		
							</tr>
						</thead>
						<?php while ($row = $result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
							<tr>
								<td><?php echo $row['device_name']; ?></td>
								<td><?php echo $row['attribute_name']; ?></td>
								<td><?php echo $row['value']; ?></td>
								<td><?php echo $row['unit']; ?></td>
								<td><?php echo $row['timestamp']; ?></td>
							</tr>
						<?php } ?>	
					</table>
				</div>
				</div>
			</div>
		</div>
	</div>
</body>
</html>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Device Mapping Configuration</title>
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
			<?php  include('php_code_device_mapping.php'); ?>

			<?php 
				if (isset($_GET['edit'])) {
					$id = $_GET['edit'];
					$update = true;
					
					$sql = "SELECT * FROM ems_device_mapping_table WHERE device_id=$id;"; 
					$record = $db->query($sql);

					if (count($record) == 1 ) {
						$n = $record->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT);
						$deviceName = $n['device_name_ext'];
						$deviceId = $n['device_id'];
					
					}
				}
			?>		
			<?php if (isset($_SESSION['message'])): ?>
			<div class="msg">
				<?php 
					echo $_SESSION['message']; 
					unset($_SESSION['message']);
				?>
			</div>
			<?php endif ?>
			<?php 
		$sql = "SELECT * FROM ems_device_mapping_table;"; 
			$result = $db->query($sql);
		?>
			<div class="content">
				<div class="content_resize">
					<div class="article" style="padding:16px 0 0 0;">
						<div id="datatable" style="text-align: center; width:900px; margin-left:auto; margin-right:auto;">

							<table>
								<thead>
									<tr>
										<th>External Device Name</th>
										<th>Device ID</th>
										<th colspan="2">Action</th>
									</tr>
								</thead>

								<?php while ($row = $result->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT)) { ?>
								<tr>
									<td><?php echo $row['device_name_ext']; ?></td>
									<td><?php echo $row['device_id']; ?></td>

									<td>
										<a href="device_mapping_index.php?edit=<?php echo $row['device_id']; ?>" class="edit_btn" >Edit</a>
									</td>
									<td>
										<a href="php_code_device_mapping.php?del=<?php echo $row['device_id']; ?>" class="del_btn">Delete</a>
									</td>
								</tr>
								<?php } ?>
														
							<form method="post" action="php_code_device_mapping.php" >
								<tr>
								<div class="input-group">
									<td><label>External device Name:</label></td>
									
									<td><input type="text" name="deviceName" value="<?php echo $deviceName; ?>"></td>
									</div>
								</tr>
								<tr>
									<div class="input-group">
										<input type="hidden" name="id" value="<?php echo $id; ?>">
											
												<td><label>Device Id:</label></td>
												<td>
												<select name="deviceId">
												<option disabled selected>-- Select relevant device ID name --</option>
												<?php
												// $records = mysqli_query($db, "SELECT city_name From tblcity");  // Use select query here 
												$sql3 = "SELECT * FROM ems_device_table;"; 
												$result3 = $db->query($sql3);
												while($data = $result3->fetch(PDO::FETCH_ASSOC, PDO::FETCH_ORI_NEXT))
												{
													echo "<option value='". $data['device_id'] ."'>" .$data['device_name'] ."</option>";  // displaying data in option menu
												}	
											?>  
											</select>
											</td>
										</div>
										</tr>
										<tr>
										<td>
										<div class="input-group">
											<?php if ($update == true): ?>
											<button class="btn" type="submit" name="update" style="background: #556B2F;" >update</button>
											<?php else: ?>
											<button class="btn" type="submit" name="save" >Save</button>
											<?php endif ?>
										</div>
										</td>
										</tr>
									</form>
								</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</body>
		</html>
		
<?php include('ajax_includes.php'); ?>
<?php 
 $category = $_POST['category_id'];
 $query = "select * from job_subcategory where category_id = '".$category."' and status = '1' ";
 $sql = mysql_query($query);
?>
<select name="subcategory_id" id="subcategory_id">
<option value="">Select Job Role
</option>
<?php
while($rs = mysql_fetch_array($sql))
{
 ?>			
	<option value="<?php echo $rs['id']; ?>"><?php echo $rs['title']; ?></option>
<?php
}
?>
</select>

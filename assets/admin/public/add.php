<?php $this->load->view('header'); ?>

<script type="text/javascript">
$(document).ready(function() {
	$( "#start_date" ).datepicker({ 
		showOn: "button",
		buttonImage: "<?php echo $this->config->item('css_images_js_base_url'); ?>img/icon_calendar.jpg",
		buttonImageOnly: true,
	});

	$( "#end_date" ).datepicker({
		showOn: "button",
		buttonImage: "<?php echo $this->config->item('css_images_js_base_url'); ?>img/icon_calendar.jpg",
		buttonImageOnly: true,
	});
});
function getsubcategory(id) {
	$.post("<?php echo $this->config->item('css_images_js_base_url'); ?>ajax/get_subcategory.php",{category_id: id},function(data){		
		$("#subcategory_id").html(data);
		});
}
  </script>

<div class="row-fluid">
	<div class="span12">
		<ul class="breadcrumb-beauty">
			<li>
				<a href="<?php echo base_url();?>job"><span class="fs1" aria-hidden="true" data-icon="&#xe003;"></span> Job</a>
			</li>
			<li>
				<a href="#">Add Job</a>
			</li>
		</ul>
	</div>
</div>
<br>

<div class="row-fluid">
	<div class="span12">
		<div class="widget">
			<div class="widget-header">
				<div class="title">
					<span class="fs1" aria-hidden="true" data-icon="&#xe023;"></span> Add Job
				</div>
			</div>
			<div class="widget-body">
				<form class="form-horizontal no-margin" id="frmValidation" name="frm" action="<?php echo base_url();?>job/do_add" method="POST" enctype="multipart/form-data">
				<div class="span12"><span style="padding-left:30px; font-weight:200px; font-size:medium;">Job Placement</span></div>
				<div class="span12">
					<div class="control-group span6">
						<label class="control-label" style="padding-top:45px;">
							* Title
						</label>
						<div class="controls" style="padding-top:45px;">
							<input type="text" name="title" value="<?php if($this->session->userdata('title')!='') { echo  $this->session->userdata('title');  }   ?>" class="validate[required]">
						</div>
					</div>
					<div class="control-group span6">
						<label class="control-label" style="padding-top:29px;">
							* Qualifications
						</label>
						<div class="controls">
							<select id="qualification_id[]" name="qualification_id[]" multiple class="validate[required];">
								<?php
									for($q=0; $q<count($qualification); $q++)
									{
									?>
										<option value="<?php echo $qualification[$q]['id']; ?>" multiple="true" >
											<?php echo $qualification[$q]['title']; ?>
										</option>
									<?php
									}
								?>	
							</select>
						</div>
					</div>
				</div>
				<div class="span12">
					<div class="control-group span6" >
						<label class="control-label">
							* Start Date
						</label>
						<div class="controls">
							<input type="text" style="width:185px;" id="start_date" name="start_date" value="<?php if($this->session->userdata('start_date')!='') { echo  $this->session->userdata('start_date');  }   ?>" readonly="true" class="validate[required]">
							</div>		
					</div>
					<div class="control-group span6">
						<label class="control-label">
							* Job Type
						</label>
						<div class="controls">
							<select name="category_id"  id="category_id" class="validate[required];"  onchange="javascript: getsubcategory(this.value);">
								<option value="">Select Job Type</option>
								<?php
									for($u=0; $u<count($category); $u++)
									{
									?>
										<option value="<?php echo $category[$u]['id']; ?>" <?php if($this->session->userdata('category_id')!='') { echo  $this->session->userdata('category_id'); ?> selected="selected" <?php }   ?> >
											<?php echo $category[$u]['title']; ?>
										</option>
									<?php
									}
								?>
							</select>
						</div>
					</div>
				</div>
				<div class="span12">
					<div class="control-group span6">
						<label class="control-label">
								* End Date
								</label>
								<div class="controls">
								<input type="text"  style="width:185px;" id="end_date" name="end_date" value="<?php if($this->session->userdata('end_date')!='') { echo  $this->session->userdata('end_date');  }   ?>" readonly="true" class="validate[required]">
								</div>
					</div>
					<div class="control-group span6" style="float-right:267px;">
							<label class="control-label">
								* Job Role 
							</label>
							<div class="controls">
								<select id="subcategory_id" name="subcategory_id" class="validate[required];">
									<option value="">Select Job Role </option>
								</select>
							</div>
					</div>
				</div>
				<div class="span12">
					<div class="control-group span6">
							<label class="control-label">
								* Job Skills
							</label>
							<div class="controls">
								<input type="text" name="job_skills" value="<?php if($this->session->userdata('job_skills')!='') { echo  $this->session->userdata('job_skills');  }   ?>" class="validate[required]">
							</div>
					</div>
					<div class="control-group span6">
							<label class="control-label">
							* Pay 
							</label>
							<div class="controls">
								<select id="pay" name="pay" class="validate[required];">
									<option value="">Select Pay</option>
										<option value="5000-6000">5000-6000</option><option value="6000-7000">6000-7000</option><option value="7000-8000">7000-8000</option>
								</select>
							</div>
					</div>
				</div>
				<div class="span12">
					<div class="control-group span6">
						<label class="control-label">
							Job Skills Description
						</label>
						<div class="controls">
							<textarea name="skill_description" style="width:300px;"  rows="3"><?php if($this->session->userdata('skill_description')!='') { echo  $this->session->userdata('skill_description');  }   ?></textarea>
						</div>
					</div>
					<div class="control-group span6">
						<label class="control-label">
							Job Details
						</label>
						<div class="controls">
							<textarea name="description"  style="width:300px;" rows="5" cols="5"><?php if($this->session->userdata('description')!='') { echo  $this->session->userdata('description');  }   ?></textarea>
						</div>
					</div>
				</div>

				<div class="span12" style="padding-right:80px;"><hr></div>	

				<div class="span12"><span style="font-weight:200px; font-size:medium;">Contact Information</span></div>

					<div class="span12">
						<div class="control-group span6">
								<label class="control-label">
									* Email
								</label>
								<div class="controls">
									<input type="text" name="email" value="<?php if($this->session->userdata('email')!='') { echo  $this->session->userdata('email');  }   ?>" class="validate[required,custom[email]]">
								</div>
						</div>
						<div class="control-group span6">
								<label class="control-label">
									 Twitter
								</label>
								<div class="controls">
									<input type="text" name="twitter" value="<?php if($this->session->userdata('twitter')!='') { echo  $this->session->userdata('twitter');  }   ?>">
								</div>
						</div>
					</div>
					<div class="span12">
						<div class="control-group span6">
								<label class="control-label">
									* Phone
								</label>
								<div class="controls">
									<input type="text" name="phone" value="<?php if($this->session->userdata('phone')!='') { echo  $this->session->userdata('phone');  }   ?>" class="validate[required,custom[phone]]">
								</div>
						</div>
						<div class="control-group span6">
								<label class="control-label">
									 Facebook
								</label>
								<div class="controls">
									<input type="text" name="facebook" value="<?php if($this->session->userdata('facebook')!='') { echo  $this->session->userdata('facebook');  }   ?>">
								</div>
						</div>
					</div>
					<div class="span12">
						<div class="control-group span6">
								<label class="control-label">
									 * Postcode

								</label>
								<div class="controls">
									<input type="text" name="postcode" value="<?php if($this->session->userdata('postcode')!='') { echo  $this->session->userdata('postcode');  }   ?>" class="validate[required]">
								</div>
						</div>
						<div class="control-group span6">
							<label class="control-label">
								Featured
							</label>
							<div class="controls">
								<label class="radio inline">
									<input type="radio" name="featured" id="featured" value="1">Yes
								</label>
								<label class="radio inline">
									<input type="radio" name="featured" id="featured" value="0"  checked="true">No
								</label>
							</div>	
						</div>
					</div>
					<div class="span12">
						<div class="control-group span6">
								<label class="control-label">
									 * Address

								</label>
								<div class="controls">
									<input type="text" name="address" value="<?php if($this->session->userdata('address')!='') { echo  $this->session->userdata('address');  }   ?>" class="validate[required]">
								</div>
						</div>
						<div class="control-group span6">
							<label class="control-label">
								Status
							</label>
							<div class="controls">
								<label class="radio inline">
									<input type="radio" name="status" id="status" value="1" checked="true">Yes
								</label>
								<label class="radio inline">
									<input type="radio" name="status" id="status" value="0">No
								</label>
							</div>
						</div>
					</div>
					<div class="span12">
						<div class="control-group span6">
								<label class="control-label">
									Website
								</label>
								<div class="controls">
									<input type="text" name="website" value="<?php if($this->session->userdata('website')!='') { echo  $this->session->userdata('website');  }   ?>" class="validate[custom[url]]">
								</div>
						</div>
						<div class="control-group span6"></div>
					</div>
					<div class="span12">
						<div class="form-actions no-margin span6">
							<input type="submit" name="submit" value="Save" class="btn btn-info">
							<button class="btn" type="button" onClick="javascript:window.location='<?php echo base_url();?>job';" >
								Cancel
							</button>
						</div>
					</div>
					<div class="clearfix"></div>
				</form>
			</div>


		</div>
	</div>
</div>

<?php $this->load->view('footer'); ?>
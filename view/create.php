<?php
include 'header.php';
?>
	<div class="row">
		<div class="col-md-6">
			<form action="/" method="POST" name="form">
				<div class="mb-3">
					<label for="name" class="form-label">Name</label>
					<input type="text" name="name" class="form-control" id="name" aria-describedby="name">
					<div id="name" class="form-text">Please fill form</div>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>

<?php
include 'footer.php';
?>
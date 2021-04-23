<?php
include 'header.php';
?>
	<div class="row">
		<div class="col-md-6">
			<form action="/" method="POST" name="form">
				<div class="mb-3">
					<label for="name" class="form-label">Name</label>
					<input type="hidden" class="form-control" id="id" name="id" value="<?= $product->getId() ?>" aria-describedby="name">
					<input type="text" class="form-control" id="name" name="name" value="<?= $product->getName() ?>" aria-describedby="name">
					<div id="name" class="form-text">Please fill form</div>
				</div>
				<button type="submit" class="btn btn-primary">Submit</button>
			</form>
		</div>
	</div>

<?php
include 'footer.php';
?>
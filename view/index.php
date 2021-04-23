<?php
include 'header.php';
?>
	<a href="/?<?= http_build_query( [ 'sort-name' => 'asc', 'page' => $page, 'search-name' => $searchName ] ) ?>">
		<button class=" <?= $page == 'asc' ? 'active' : '' ?> btn btn-success">Name asc</button>
	</a>
	<a href="/?<?= http_build_query( [ 'sort-name' => 'desc', 'page' => $page, 'search-name' => $searchName ] ) ?>">
		<button class=" <?= $page == 'asc' ? 'active' : '' ?> btn btn-success">Name desc</button>
	</a>
	<form action="/" method="get">
		<input type="text" name="search-name" value="<?= $searchName ?>">
		<button type="submit" class="btn btn-primary">Search</button>
	</form>
	<table class="table">
		<thead>
		<tr>
			<th scope="col">#</th>
			<th scope="col">Name</th>
			<th scope="col">Action</th>
		</tr>
		</thead>
		<tbody>
        <?php foreach ( $products as $product ): ?>
			<tr>
				<td><?= $product->getId(); ?></td>
				<td><?= $product->getName(); ?></td>
				<td>
					<a href="/?route=delete&id=<?= $product->getId(); ?>">
						<button type="submit" class="btn btn-danger">Delete</button>
					</a>
					<a href="/?route=update&id=<?= $product->getId(); ?>">
						<button type="submit" class="btn btn-primary">Update</button>
					</a>
				</td>
			</tr>
        <?php endforeach; ?>
		</tbody>
	</table>
	<a href="/?route=create">
		<button type="submit" class="btn btn-success">Create</button>
	</a>
	<nav aria-label="Page navigation example">
		<ul class="pagination">
            <?php for ( $i = 1; $i <= ceil($count/5); $i += 1 ): ?>
				<li class="page-item"><a class="page-link" href="/?<?= http_build_query( [
                        'sort-name'   => $sortName,
                        'page'        => $i ,
                        'search-name' => $searchName
                    ] ) ?>"><?= $i  ?></a></li>
            <?php endfor; ?>
		</ul>
	</nav>
<?php
include 'footer.php';
?>
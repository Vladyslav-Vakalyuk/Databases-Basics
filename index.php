<?php

require_once 'bootstrap.php';

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = new Request(
	$_GET,
	$_POST,
	[],
	$_COOKIE,
	$_FILES,
	$_SERVER
);

$time = $request->cookies->get( 'time' );

if ( $time ) {
	$timeSecondDiff = time() - $time;
	echo "<H1>You are have been visited the site {$timeSecondDiff} seconds</H1>";
}

$response = new Response(
	'Content',
	Response::HTTP_OK,
	[ 'content-type' => 'text/html' ]
);
if ( ! $time ) {
	$response->headers->setCookie( Cookie::create( 'time', time() ) );
	$response->send();
}

$route      = $request->query->get( 'route' );
$id         = $request->query->get( 'id' );
$page       = $request->query->get( 'page', 1 );
$sortName   = $request->query->get( 'sort-name' );
$searchName = $request->query->get( 'search-name' );

if ( $route === 'delete' ) {
	deleteItem( $id, $entityManager );
	renderIndex( $entityManager, $page, $sortName, $searchName );
} elseif ( $route === 'create' ) {
	include 'view/create.php';
} elseif ( $route === 'update' ) {
	$product = findById( $_GET['id'], $entityManager );

	include 'view/update.php';
}

if ( $route == '' ) {
	$name = $request->get( 'name' );
	$id   = $request->get( 'id' );

	if ( $name && null === $id ) {
		createItem( $name, $entityManager );
	}
	if ( $name && $id ) {
		updateItem( $name, $id, $entityManager );
	}
	renderIndex( $entityManager, $page, $sortName, $searchName );
}

/**
 * @param $entityManager
 * @param $page
 * @param $sortName
 * @param $searchName
 */
function renderIndex( $entityManager, $page, $sortName, $searchName ) {
	$qb = new QueryBuilder( $entityManager );

	$qb->add( 'select', 'u' )
	   ->add( 'from', 'Product u' )
	   ->setFirstResult( ( $page - 1 ) * 5 )
	   ->setMaxResults( 5 );

	if ( ! empty( $sortName ) ) {
		sortByName( $qb, $sortName );
	}
	if ( ! empty( $searchName ) ) {
		findByName( $qb, $searchName );
	}

	$query    = $qb->getQuery();
	$products = $query->getResult();

	$count = getCountProduct( $searchName, $entityManager );
	include 'view/index.php';
}

/**
 * @param $id
 * @param $entityManager
 */
function deleteItem( $id, $entityManager ) {
	if ( ! empty( $id ) ) {
		$product = findById( $id, $entityManager );
		if ( ! empty( $product ) ) {
			$entityManager->remove( $product );
			$entityManager->flush();
		}
	}
}

/**
 * @param $name
 * @param $entityManager
 */
function createItem( $name, $entityManager ) {
	$product = new Product();
	$product->setName( $name );
	$entityManager->persist( $product );
	$entityManager->flush();
}

/**
 * @param $name
 * @param $id
 * @param $entityManager
 */
function updateItem( $name, $id, $entityManager ) {
	$product = findById( $id, $entityManager );
	if ( ! empty( $product ) ) {
		$product->setName( $name );
		$entityManager->flush();
	}
}

/**
 * @param $query
 * @param $searchName
 *
 * @return mixed
 */
function findByName( $query, $searchName ) {
	return $query->andWhere( 'u.name LIKE :name' )
	             ->setParameter( 'name', $searchName );
}

/**
 * @param $id
 * @param $entityManager
 *
 * @return mixed
 */
function findById( $id, $entityManager ) {
	$productRepository = $entityManager->getRepository( 'Product' );

	return $productRepository->find( $id );
}

/**
 * @param $searchName
 * @param $entityManager
 *
 * @return mixed
 */
function getCountProduct( $searchName, $entityManager ) {
	$repository = $entityManager->getRepository( 'Product' );

	$countQuery = $repository->createQueryBuilder( 'u' )
	                         ->select( 'count(u.id)' );
	if ( ! empty( $searchName ) ) {
		findByName( $countQuery, $searchName );
	}

	return $countQuery->getQuery()->getSingleScalarResult();
}

/**
 * @param $query
 * @param $sortName
 *
 * @return mixed
 */
function sortByName( $query, $sortName ) {
	if ( $sortName === 'asc' ) {
		$query = $query->orderBy( 'u.name', 'ASC' );
	} elseif ( $sortName === 'desc' ) {
		$query = $query->orderBy( 'u.name', 'DESC' );
	}

	return $query;
}
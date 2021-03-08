<?php

require_once 'bootstrap.php';

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\Tools\Pagination\Paginator;

$route      = $_GET['route'] ?? '';
$id         = $_GET['id'] ?? '';
$page       = $_GET['page'] ?? 0;
$sortName   = $_GET['sort-name'] ?? '';
$searchName = $_GET['search-name'] ?? '';

if ( $route === 'delete' ) {
    deleteItem( $id, $entityManager );
    renderIndex( $entityManager, $page, $sortName, $searchName );
} elseif ( $route === 'create' ) {
    include 'view/create.php';
} elseif ( $route === 'update' ) {
    $productRepository = $entityManager->getRepository( 'Product' );
    $product           = $productRepository->find( $_GET['id'] );

    include 'view/update.php';
}

if ( $route == '' ) {
    if ( isset( $_POST['name'] ) && ! isset( $_POST['id'] ) ) {
        createItem( $_POST['name'], $entityManager );
    }
    if ( isset( $_POST['name'] ) && isset( $_POST['id'] ) ) {
        updateItem( $_POST['name'], $_POST['id'], $entityManager );
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
       ->setFirstResult( $page * 5 )
       ->setMaxResults( 5 );

    if ( ! empty( $sortName ) ) {
        if ( $sortName === 'asc' ) {
            $qb->orderBy( 'u.name', 'ASC' );
        } elseif ( $sortName === 'desc' ) {
            $qb->orderBy( 'u.name', 'DESC' );
        }

    }
    if ( ! empty( $searchName ) ) {
        $qb->andWhere( 'u.name LIKE :name' )
           ->setParameter( 'name', $searchName );
    }

    $query      = $qb->getQuery();
    $products   = $query->getResult();
    $repository = $entityManager->getRepository( 'Product' );

    $count      = $repository->createQueryBuilder( 'u' )
                             ->select( 'count(u.id)' );

    if ( ! empty( $searchName ) ) {
        $count->andWhere( 'u.name LIKE :name' )
              ->setParameter( 'name', $searchName );

    }
    $count = $count->getQuery()->getSingleScalarResult();

    include 'view/index.php';
}

/**
 * @param $id
 * @param $entityManager
 */
function deleteItem( $id, $entityManager ) {
    if ( ! empty( $id ) ) {
        $productRepository = $entityManager->getRepository( 'Product' );
        $product           = $productRepository->find( $id );
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
    $productRepository = $entityManager->getRepository( 'Product' );
    $product           = $productRepository->find( $id );
    if ( ! empty( $product ) ) {
        $product->setName( $name );
        $entityManager->flush();
    }
}
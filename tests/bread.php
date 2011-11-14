<pre>

<?php

require 'PHPUnit/Autoload.php';

require_once(__DIR__.'/../vendor/doctrine/lib/vendor/doctrine-common/lib/Doctrine/Common/ClassLoader.php');

$loader = new Doctrine\Common\ClassLoader("Doctrine\\Common", __DIR__.'/../vendor/doctrine/lib/vendor/doctrine-common/lib');
$loader->register();

$loader = new Doctrine\Common\ClassLoader("Doctrine\\DBAL", __DIR__.'/../vendor/doctrine/lib/vendor/doctrine-dbal/lib');
$loader->register();

$loader = new Doctrine\Common\ClassLoader("Doctrine\\ORM", __DIR__.'/../vendor/doctrine/lib');
$loader->register();

$loader = new Doctrine\Common\ClassLoader("DoctrineExtensions\\NestedSet\\Tests", __DIR__);
$loader->register();

$loader = new Doctrine\Common\ClassLoader("DoctrineExtensions\\NestedSet\\Tests\\Mocks", __DIR__);
$loader->register();

$loader = new Doctrine\Common\ClassLoader("DoctrineExtensions\\NestedSet", __DIR__."/../lib");
$loader->register();


$conn = \Doctrine\DBAL\DriverManager::getConnection(array(
            'driver' => 'pdo_sqlite',
            'memory' => true
));

function printTree($nsm){
	$w = $GLOBALS['wrappers'];
	echo "[\n";
	foreach($w as $k=>$node){
		echo " $k => ". $node .' ['.$node->getLeftValue().'|'.$node->getRightValue().'|'.$node->getDeepValue().']'. "\n";	
	}
	echo "]\n";
	$tree = $nsm->fetchTreeAsArray(1);
	foreach ($tree as $node) {
		echo str_repeat('  ', $node->getLevel()) . $node .' ['.$node->getLeftValue().'|'.$node->getRightValue().'|'.$node->getDeepValue().']'. "\n";
	}
}

$config = new \Doctrine\ORM\Configuration();
$config->setProxyDir(__DIR__ . '/../Proxies');
$config->setProxyNamespace('DoctrineExtensions\NestedSet\Tests\Proxies');
//$config->setMetadataDriverImpl(\Doctrine\ORM\Mapping\Driver\AnnotationDriver::create());

$driverImpl = $config->newDefaultAnnotationDriver(__DIR__ . '/Mocks');
$config->setMetadataDriverImpl($driverImpl);

$config->setAutoGenerateProxyClasses(true);

$em = \Doctrine\ORM\EntityManager::create($conn, $config);

$schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
$schemaTool->createSchema(array($em->getClassMetadata('DoctrineExtensions\NestedSet\Tests\Mocks\NodeMock')));

use DoctrineExtensions\NestedSet\Tests\Mocks\NodeMock;
use DoctrineExtensions\NestedSet\Tests\Mocks\ManagerMock;
use DoctrineExtensions\NestedSet\NodeWrapper;

$nodes = array(
new NodeMock(1, '1', 1, 10, 1, 0),               # 0
new NodeMock(2, '1.1', 2, 7, 1, 1),          # 1
new NodeMock(3, '1.1.1', 3, 4, 1, 2),    # 2
new NodeMock(4, '1.1.2', 5, 6, 1, 2),    # 3
new NodeMock(5, '1.2', 8, 9, 1, 1),          # 4
);

$nodes2 = array(
new NodeMock(11, '1', 1, 12, 2, 0),           # 0
new NodeMock(12, '1.1', 2, 7, 2, 1),       # 1
new NodeMock(13, '1.1.1', 3, 4, 2, 2), # 2
new NodeMock(14, '1.1.2', 5, 6, 2, 2), # 3
new NodeMock(15, '1.2', 8, 9, 2, 1),       # 4
new NodeMock(16, '1.3', 10, 11, 2, 1),     # 5
);

$em->flush();

$nsm = new ManagerMock($em, 'DoctrineExtensions\NestedSet\Tests\Mocks\NodeMock');
$wrappers = array();
foreach($nodes as $node)
{
	$em->persist($node);
	$wrappers[] = $nsm->wrapNode($node);
}

$wrappers2 = array();
foreach($nodes2 as $node)
{
	$em->persist($node);
	$wrappers2[] = $nsm->wrapNode($node);
}

$em->flush();

printTree($nsm);

echo "\n\n ->insertAsParentOf \n";
// echo $qb->getQuery()->getSql();
// print_r($qb->getQuery()->getParameters());
$newWrapper = $nsm->wrapNode(new NodeMock(6, '1.3', 0, 0, 0, 1));
$newWrapper->insertAsParentOf($wrappers[4]);
printTree($nsm);

echo "\n\n ->insertAsPrevSiblingOf \n";
$newNode = new NodeWrapper(new NodeMock(21, '1.1.1(.5)', null, null, 1, 0), $nsm);
$newNode->insertAsPrevSiblingOf($wrappers[3]);
printTree($nsm);

echo "\n\n ->insertAsPrevSiblingOf \n";
$newNode = new NodeWrapper(new NodeMock(21, '1.1.1(-.5)', null, null, 1, 0), $nsm);
$newNode->insertAsNextSiblingOf($wrappers[3]);
printTree($nsm);

echo "\n\n ->insertAsFirstChildOf \n";
$newNode = new NodeWrapper(new NodeMock(21, '1.1.0', null, null, 1, 0), $nsm);
$newNode->insertAsFirstChildOf($wrappers[1]);
printTree($nsm);

echo "\n\n ->insertAsLastChildOf \n";
$newNode = new NodeWrapper(new NodeMock(21, '1.1.3', null, null, 1, 0), $nsm);
$newNode->insertAsLastChildOf($wrappers[1]);
printTree($nsm);

echo "\n\n ->moveAsFirstChildOf \n";
$wrappers[1]->moveAsFirstChildOf($wrappers[4]);
printTree($nsm);

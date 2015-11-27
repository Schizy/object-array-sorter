# object-array-sorter
A PHP class that can sort/filter an object array (like the result of Doctrine query) by method result (can be chained)

TODO: The final wanted behavior for the v1

$sorted = OAS::sort($data)->ByFoo(); // on getFoo() result
$sorted = OAS::sort($data)->ByFooByName(); // on getFoo()->getName() result
$sorted = OAS::sortById($data); // shortcut to sort($data)->ById();

$grouped = OAS::group($data)->ByFoo() // group en getFoo() result
$grouped = OAS::group($data)->ByFooByName() // group en getFoo()->getName() result

$truncated = OAS::truncate($data)->limit(10) //Only the 10 first results
$truncated = OAS::first($data) //Only the first result => shortcut to truncate()->limit(1)

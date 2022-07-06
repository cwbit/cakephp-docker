<h1 align="center">Scenarios</h1>

You can create scenarios that will persist a multitude of test fixtures. This can be useful to seed your
test database with a reusable set of data.

Use the `CakephpFixtureFactories\Scenario\ScenarioAwareTrait`
in your test and load your scenario with the `loadFixtureScenario()` method. You can either provide the
fully qualified name of the scenario class, or place your scenarios under the `App\Test\Scenario` namespace.


Example:
```php
$authors = $this->loadFixtureScenario('NAustralianAuthors', 3);
```
will persist 3 authors associated to the country Australia, as defined here:

```php

use CakephpFixtureFactories\Scenario\FixtureScenarioInterface;
use CakephpFixtureFactories\Test\Factory\AuthorFactory;
use TestApp\Model\Entity\Author;

class NAustralianAuthorsScenario implements FixtureScenarioInterface
{
    const COUNTRY_NAME = 'Australia';

    /**
     * @param int $n the number of authors
     * @return Author|Author[]
     */
    public function load($n = 1, ...$args)
    {
        return AuthorFactory::make($n)->fromCountry(self::COUNTRY_NAME)->persist();
    }
}

```

Scenarios should implement the `CakephpFixtureFactories\Scenario\FixtureScenarioInterface` class.
This test provides an example on how to use scenarios:

```php

namespace CakephpFixtureFactories\Test\TestCase\Scenario;

use Cake\ORM\Query;
use Cake\TestSuite\TestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use CakephpFixtureFactories\Test\Factory\AuthorFactory;
use CakephpFixtureFactories\Test\Scenario\NAustralianAuthorsScenario;
use TestApp\Model\Entity\Author;

class FixtureScenarioTest extends TestCase
{
    use ScenarioAwareTrait;

    public function testLoadScenario()
    {
        /** @var Author[] $authors */
        $authors = $this->loadFixtureScenario(NAustralianAuthorsScenario::class, 3) ?? [];
        
        $this->assertSame(3, $this->countAustralianAuthors());
        
        foreach ($authors as $author) {
            $this->assertInstanceOf(Author::class, $author);
            $this->assertSame(
                NAustralianAuthorsScenario::COUNTRY_NAME,
                $author->address->city->country->name
            );
        }
    }

    private function countAustralianAuthors(): int
    {
        return AuthorFactory::find()
            ->innerJoinWith('Address.City.Country', function (Query $q) {
                return $q->where(['Country.name' => NAustralianAuthorsScenario::COUNTRY_NAME]);
            })
            ->count();
    }
}

```

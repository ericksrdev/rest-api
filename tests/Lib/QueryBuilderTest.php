<?php

use App\Lib\Database\QueryBuilder;
use PHPUnit\Framework\TestCase;

class QueryBuilderTest extends TestCase
{
    public array $attributes;

    public array $expectedParsedAttributes;

    public string $expectedInsertString;

    public string $expectedUpdateString;

    protected function setUp(): void
    {
        parent::setUp();

        $this->attributes = [
            'id'        => 1,
            'name'      => 'Erick',
            'last_name' => 'Sandoval',
            'email'     => 'erick.sr@yahoo.com',
        ];

        $this->expectedParsedAttributes = [
            ':ID'        => 1,
            ':NAME'      => 'Erick',
            ':LAST_NAME' => 'Sandoval',
            ':EMAIL'     => 'erick.sr@yahoo.com',
        ];

        $this->expectedInsertString = "INSERT INTO :TABLE_NAME (name,last_name,email) VALUES (:NAME,:LAST_NAME,:EMAIL)";

        $this->expectedUpdateString = "UPDATE :TABLE_NAME SET name = :NAME, last_name = :LAST_NAME, email = :EMAIL WHERE id = :PRIMARY_KEY";

    }

    /**
     * @test
     */
    public function it_can_create_insert_queries_for_models()
    {
        $generatedQuery = QueryBuilder::buildModelQuery($this->attributes, QueryBuilder::INSERT_QUERY_TYPE);

        $this->assertEquals($this->expectedInsertString, $generatedQuery);
    }

    /**
     * @test
     */
    public function it_can_create_update_queries_for_models()
    {
        $generatedQuery = QueryBuilder::buildModelQuery($this->attributes, QueryBuilder::UPDATE_QUERY_TYPE);

        $this->assertEquals($this->expectedUpdateString, $generatedQuery);
    }

    public function it_can_parse_model_attributes_to_pdo_statement_spec()
    {
        $parsedAttributes = QueryBuilder::parseModelAttributes($this->attributes);

        foreach ($this->attributes as $key => $val)
        {
            $this->assertArrayHasKey(":$key", $parsedAttributes);
            $this->assertEquals($val, $parsedAttributes[":$key"]);
        }
    }
}
<?php

namespace Otto;

class Challenge
{
    protected $pdoBuilder;

    public function __construct()
    {
        $config = require __DIR__ . '/../config/database.config.php';
        $this->setPdoBuilder(new PdoBuilder($config));
    }

    /**
     * Use the PDOBuilder to retrieve all the records
     *
     * @return array
     */
    public function getRecords()
    {
        $sql = 'SELECT
        businesses.id,
        directors.first_name,
        directors.last_name,
        businesses.name,
        businesses.registered_address,
        businesses.registration_number
    FROM
        businesses
    INNER JOIN
        director_businesses ON businesses.id = director_businesses.business_id
    INNER JOIN
        directors ON director_businesses.director_id = directors.id
    ORDER BY
        businesses.id;';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Use the PDOBuilder to retrieve all the director records
     *
     * @return array
     */
    public function getDirectorRecords()
    {
        $sql = 'SELECT * FROM directors';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Use the PDOBuilder to retrieve a single director record with a given id
     *
     * @param int $id
     * @return array
     */
    public function getSingleDirectorRecord($id)
    {
        $sql = 'SELECT * FROM directors WHERE id = :id';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * Use the PDOBuilder to retrieve all the business records
     *
     * @return array
     */
    public function getBusinessRecords()
    {
        $sql = 'SELECT * FROM businesses';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Use the PDOBuilder to retrieve a single business record with a given id
     *
     * @param int $id
     * @return array
     */
    public function getSingleBusinessRecord($id)
    {
        $sql = 'SELECT * FROM businesses WHERE id = :id';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':id', $id);
        $statement->execute();
        return $statement->fetch();
    }

    /**
     * Use the PDOBuilder to retrieve a list of all businesses registered on a particular year
     *
     * @param int $year
     * @return array
     */
    public function getBusinessesRegisteredInYear($year)
    {
        $sql = 'SELECT * FROM businesses WHERE YEAR(registration_date) = :year';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->bindParam(':year', $year);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Use the PDOBuilder to retrieve the last 100 records in the directors table
     *
     * @return array
     */
    public function getLast100Records()
    {
        $sql = 'SELECT * FROM directors ORDER BY id DESC LIMIT 100';
        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * Use the PDOBuilder to retrieve a list of all business names with the director's name in a separate column.
     * The links between directors and businesses are located inside the director_businesses table.
     *
     * Your result schema should look like this;
     *
     * | business_name | director_name |
     * ---------------------------------
     * | some_company  | some_director |
     *
     * @return array
     */
    public function getBusinessNameWithDirectorFullName()
    {
        $sql = "
            SELECT
                businesses.name,
                    CONCAT(directors.first_name, ' ', directors.last_name) AS director_name
                FROM
                    businesses
                INNER JOIN
                    director_businesses ON businesses.id = director_businesses.business_id
                INNER JOIN
                    directors ON director_businesses.director_id = directors.id
                ORDER BY
                    businesses.name;
    ";

        $pdo = $this->getPdoBuilder()->getPdo();
        $statement = $pdo->prepare($sql);
        $statement->execute();
        $results = $statement->fetchAll();

        $businessNamesWithDirectorFullNames = [];
        foreach ($results as $result) {
            $businessNamesWithDirectorFullNames[] = [
                'business_name' => $result['business_name'],
                'director_name' => $result['director_name'],
            ];
        }

        return $businessNamesWithDirectorFullNames;
    }

    /**
     * @param PdoBuilder $pdoBuilder
     * @return $this
     */
    public function setPdoBuilder(PdoBuilder $pdoBuilder)
    {
        $this->pdoBuilder = $pdoBuilder;
        return $this;
    }

    /**
     * @return PdoBuilder
     */
    public function getPdoBuilder()
    {
        return $this->pdoBuilder;
    }
}

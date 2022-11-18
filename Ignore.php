No hagas caso a este documento, solo está para cuando quiero editar algo fuera del archivo

public function existsZipcode($zipcode)
    {
        $sql = 'SELECT * FROM addresses WHERE zipcode=:zipcode';
        $query = $this->db->prepare($sql);
        $query->bindParam(':zipcode', $zipcode, PDO::PARAM_STR);
        $query->execute();

        return $query->rowCount();
    }

public function createAddress($data)
    {
        $response = false;

        if ( ! $this->existsZipcode($data['zipcode'])) {
            // Crear el address

            $password = hash_hmac('sha512', $data['password'], ENCRIPTKEY);

            $sql = 'INSERT INTO addresses(
                  address, city, state, zipcode, country) 
                  VALUES(
                  :address, :city, :state, :zipcode, :country)';

            $params = [
                ':address' => $data['address'],
                ':city' => $data['city'],
                ':state' => $data['state'],
                ':zipcode' => $data['postcode'],
                ':country' => $data['country'],
            ];

            $query = $this->db->prepare($sql);
            $response = $query->execute($params);

        }

        return $response;
    }

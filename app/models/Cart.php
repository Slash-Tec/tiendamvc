<?php

class Cart
{
    private $db;

    public function __construct()
    {
        $this->db = Mysqldb::getInstance()->getDatabase();
    }

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

    public function verifyProduct($product_id, $user_id)
    {
        $sql = 'SELECT * FROM carts WHERE product_id=:product_id AND user_id=:user_id';
        $query = $this->db->prepare($sql);
        $params = [
            ':product_id' => $product_id,
            ':user_id' => $user_id,
        ];
        $query->execute($params);

        return $query->rowCount();
    }

    public function addProduct($product_id, $user_id)
    {
        $sql = 'SELECT * FROM products WHERE id=:id';
        $query = $this->db->prepare($sql);
        $query->execute([':id' => $product_id]);
        $product = $query->fetch(PDO::FETCH_OBJ);

        $sql2 = 'INSERT INTO carts(state, user_id, product_id, quantity, discount, send, date)
                 VALUES (:state, :user_id, :product_id, :quantity, :discount, :send, :date)';
        $query2 = $this->db->prepare($sql2);
        $params2 = [
            ':state' => 0,
            ':user_id' => $user_id,
            ':product_id' => $product_id,
            ':quantity' => 1,
            ':discount' => $product->discount,
            ':send' => $product->send,
            ':date' => date('Y-m-d H:i:s'),
        ];
        $query2->execute($params2);
        return $query2->rowCount();
    }

    public function getCart($user_id)
    {
        $sql = 'SELECT c.user_id as user, c.product_id as product, c.quantity as quantity, 
                c.send as send, c.discount as discount, p.price as price, p.image as image,
                p.description as description, p.name as name
                FROM carts as c, products as p
                WHERE c.user_id=:user_id AND state=0 AND c.product_id=p.id';

        $query = $this->db->prepare($sql);
        $query->execute([':user_id' => $user_id]);

        return $query->fetchAll(PDO::FETCH_OBJ);
    }

    public function update($user, $product, $quantity)
    {
        $sql = 'UPDATE carts SET quantity=:quantity WHERE user_id=:user_id AND product_id=:product_id';
        $query = $this->db->prepare($sql);
        $params = [
            ':user_id' => $user,
            ':product_id' => $product,
            ':quantity' => $quantity,
        ];

        return $query->execute($params);
    }

    public function delete($product, $user)
    {
        $sql = 'DELETE FROM carts WHERE user_id=:user_id AND product_id=:product_id';
        $query = $this->db->prepare($sql);
        $params = [
            ':user_id' => $user,
            ':product_id' => $product,
        ];
        return $query->execute($params);
    }

    public function closeCart($id, $state)
    {
        $sql = 'UPDATE carts SET state=:state WHERE user_id=:user_id AND state=0';
        $query = $this->db->prepare($sql);
        $params = [
            ':user_id' => $id,
            ':state' => $state,
        ];
        return $query->execute($params);
    }
}
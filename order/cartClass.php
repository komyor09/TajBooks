<?php
require '../config/db.php';


class Cart
{
    private $pdo;
    private $user_id;

    public function __construct($pdo, $user_id)
    {
        $this->pdo = $pdo;
        $this->user_id = $user_id;
    }

    public function add_to_cart($book_id, $quantity)
    {
        $sql = "INSERT INTO cart (user_id, book_id, quantity) VALUES (?, ?, ?) 
                ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$this->user_id, $book_id, $quantity]);
    }
    

    public function get_cart_items()
    {
        $sql = "SELECT book.title, book.price, cart.quantity, book.id, book.image_path
                FROM cart 
                JOIN book ON cart.book_id = book.id 
                WHERE cart.user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_quantity($book_id, $quantity)
    {
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND book_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$quantity, $this->user_id, $book_id]);
    }

    public function remove_from_cart($book_id)
    {
        $sql = "DELETE FROM cart WHERE user_id = ? AND book_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id, $book_id]);
    }

    public function clear_cart()
    {
        $sql = "DELETE FROM cart WHERE user_id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id]);
    }

    public function checkout()
    {
        $items = $this->get_cart_items();
        $total = 0;

        $this->pdo->beginTransaction();

        $sql = "INSERT INTO Orders (user_id, total_price) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$this->user_id, $total]);
        $order_id = $this->pdo->lastInsertid();

        foreach ($items as $item) {
            $total += $item['price'] * $item['quantity'];
            $sql = "INSERT INTO OrderItems (order_id, book_id, quantity, price) VALUES (?, ?, ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$order_id, $item['book_id'], $item['quantity'], $item['price']]);
        }

        $sql = "UPDATE Orders SET total_price = ? WHERE id = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$total, $order_id]);

        $this->clear_cart();
        $this->pdo->commit();

        return $order_id;
    }
    public function decrease_quantity($book_id) {
        $stmt = $this->pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$this->user_id, $book_id]);
        $item = $stmt->fetch();
    
        if ($item) {
            if ($item['quantity'] > 1) {
                $stmt = $this->pdo->prepare("UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND book_id = ?");
                $stmt->execute([$this->user_id, $book_id]);
            } else {
                $this->remove_from_cart($book_id);
            }
        }
    }
    public function increase_quantity($book_id) {
        $stmt = $this->pdo->prepare("SELECT quantity FROM cart WHERE user_id = ? AND book_id = ?");
        $stmt->execute([$this->user_id, $book_id]);
        $item = $stmt->fetch();
    
        if ($item) {
            $stmt = $this->pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND book_id = ?");
            $stmt->execute([$this->user_id, $book_id]);
        }
    }
    
    
}
?>

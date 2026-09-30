<?php
class Order {
    private $conn;
    private $table_name = "orders";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getOrdersByCustomer($pelanggan_id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE pelanggan_id = :pelanggan_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':pelanggan_id', $pelanggan_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteOrder($order_id, $pelanggan_id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id AND pelanggan_id = :pelanggan_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $order_id);
        $stmt->bindParam(':pelanggan_id', $pelanggan_id);
        return $stmt->execute();
    }

    public function getPickupOrders() {
        // Menggunakan nama_pelanggan langsung dari tabel orders, dengan fallback ke users jika kosong
        $query = "SELECT o.*, COALESCE(o.nama_pelanggan, u.nama) as nama_pelanggan, u.no_hp FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.pelanggan_id = u.id 
                  WHERE o.status_pakaian IN ('Menunggu Penjemputan', 'Proses Penjemputan', 'Siap Diantar') 
                  ORDER BY o.created_at ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllOrders() {
        $query = "SELECT o.*, COALESCE(o.nama_pelanggan, u.nama) as nama_pelanggan FROM " . $this->table_name . " o 
                  LEFT JOIN users u ON o.pelanggan_id = u.id 
                  ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($order_id, $status, $driver_id = null) {
        if ($driver_id) {
            $query = "UPDATE " . $this->table_name . " SET status_pakaian = :status, driver_id = :driver_id WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':driver_id', $driver_id);
        } else {
            $query = "UPDATE " . $this->table_name . " SET status_pakaian = :status WHERE id = :id";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $order_id);
        return $stmt->execute();
    }

    public function getDashboardStats() {
        $stats = [];
        $q1 = "SELECT SUM(total_harga) as total_pemasukan FROM " . $this->table_name . " WHERE status_pakaian = 'Selesai'";
        $stmt1 = $this->conn->prepare($q1);
        $stmt1->execute();
        $stats['total_pemasukan'] = $stmt1->fetch(PDO::FETCH_ASSOC)['total_pemasukan'] ?? 0;

        $q2 = "SELECT COUNT(*) as order_aktif FROM " . $this->table_name . " WHERE status_pakaian != 'Selesai'";
        $stmt2 = $this->conn->prepare($q2);
        $stmt2->execute();
        $stats['order_aktif'] = $stmt2->fetch(PDO::FETCH_ASSOC)['order_aktif'] ?? 0;

        return $stats;
    }
}
?>
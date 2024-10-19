- Kode ini akan membantu kamu untuk mengabaikan case sensitive ketika mencoba akses array.
- Kode ini otomatis mengconvert array menjadi class (class disini bisa berperilaku seperti array, jadi kamu bisa akses seperti array pada umumnya)

  Berikut adalah dokumentasi untuk penggunaan class `ObjectIterable` yang mengonversi array ke objek dan mengabaikan case sensitivity saat mengakses array, serta mendeteksi jika suatu properti adalah array dan mengonversinya secara otomatis ke `RowData`.

### 1. **Pembuatan ObjectIterable**

Untuk membuat instance dari `ObjectIterable`, cukup masukkan array yang ingin dikonversi:

```php
$array = [
    'NAME' => 'John Doe',
    'email' => 'johndoe@example.com',
    'ADDRESS' => [
        'CITY' => 'New York',
        'ZIP' => '10001',
    ],
];

$objectIterable = new \App\Utils\ObjectIterable($array);
```

### 2. **Akses Properti dengan Case Insensitive**

Setelah array dikonversi, Anda dapat mengakses nilai-nilai di dalamnya secara case-insensitive. Misalnya:

```php
echo $objectIterable->name; // Output: John Doe
echo $objectIterable->EMAIL; // Output: johndoe@example.com
```

### 3. **Akses Properti yang Merupakan Array**

Jika properti berisi array, properti tersebut secara otomatis akan dikonversi menjadi objek `RowData`:

```php
echo $objectIterable->address->city; // Output: New York
echo $objectIterable->ADDRESS->ZIP;  // Output: 10001
```

### 4. **Penggunaan dengan `foreach` (Iterator)**

Anda dapat melakukan iterasi melalui semua item dalam `ObjectIterable` atau `RowData` menggunakan `foreach`:

```php
foreach ($objectIterable as $item) {
    echo $item->name;
    echo $item->email;
}
```

Untuk array di dalam properti, iterasi akan bekerja sama:

```php
foreach ($objectIterable->address as $key => $value) {
    echo "$key: $value";
}
```

### 5. **Mengonversi Kembali ke Array**

Jika Anda ingin mengonversi objek kembali menjadi array, gunakan metode `toArray()`:

```php
$array = $objectIterable->toArray();
```

### 6. **Mengonversi ke Laravel Collection**

Jika Anda menggunakan Laravel dan ingin memanfaatkan Collection, gunakan metode `toCollect()`:

```php
$collection = $objectIterable->toCollect();
```

### 7. **Menggunakan JSON Serialize**

Anda dapat mengubah instance `ObjectIterable` ke JSON menggunakan `json_encode()`:

```php
$json = json_encode($objectIterable); // Menghasilkan JSON dari objek
```

### 8. **ArrayAccess (Akses seperti Array)**

`ObjectIterable` dan `RowData` juga mendukung akses seperti array:

```php
echo $objectIterable['name']; // Output: John Doe
```

Jika properti merupakan array, maka akan tetap bisa diakses seperti ini:

```php
echo $objectIterable['ADDRESS']['CITY']; // Output: New York
```

### 9. **Penghitungan Jumlah Item**

Anda dapat menggunakan `count()` untuk mengetahui jumlah elemen dalam `ObjectIterable`:

```php
echo count($objectIterable); // Output: jumlah elemen dalam array
```

### Contoh Kasus

```php
$data = [
    'Name' => 'Alice',
    'age' => 30,
    'Address' => [
        'Street' => '123 Main St',
        'City' => 'Wonderland',
    ]
];

$object = new \App\Utils\ObjectIterable($data);

echo $object->name; // Alice
echo $object->address->city; // Wonderland

$arrayBack = $object->toArray();
``` 

Ini adalah cara pemakaian dasar dari class `ObjectIterable` yang Anda buat untuk mengonversi array ke objek dan memberikan akses yang lebih fleksibel dengan fitur case-insensitive serta otomatisasi dalam penanganan properti berjenis array.

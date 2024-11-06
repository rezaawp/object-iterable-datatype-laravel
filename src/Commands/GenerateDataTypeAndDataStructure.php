<?php

namespace RKWP\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GenerateDataTypeAndDataStructure extends Command
{
    /**
     * Author: Reza Khoirul Wijaya Putra
     */

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:data-type {name : untuk membuat data type nya} {properties : untuk membuat property} {isObjectOfArray=null : jika array}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Perintah ini untuk membuat tipe data baru di project medicare';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $name = $this->argument('name');
        $properties = $this->argument('properties');
        $isObjectOfArray = $this->argument('isObjectOfArray');

        if (!str_contains($name, "/")) {
            $this->error("Untuk sementara, kamu harus membuat tipe data di dalam folder. Contoh benar: php artisan make:data-type Mahasiswa/Mahasiswa");
            return Command::INVALID;
        }

        if (str_contains($name, '/')) {
            $nmType = explode('/', $name);
            $nmType[count($nmType) - 1] = 'T' . $nmType[count($nmType) - 1];
            $nmType = implode('/', $nmType);
        } else {
            $nmType = "T" . $name;
        }

        $structPath = base_path('app/DataStructure/' . $name . 'Struct' . '.php');
        $typePath = base_path('app/DataType/' . $nmType . '.php');

        // if (File::exists($structPath) || File::exists($typePath)) {
        //     $this->error("Type \"$name\" sudah ada");
        //     return Command::INVALID;
        // }

        $structFile = File::get(__DIR__ . '/structdatatype.stub');
        $typeFile = File::get(__DIR__ . '/datatype.stub');

        $structDir = dirname($structPath);
        $typeDir = dirname($typePath);
        $structName = pathinfo($structPath, PATHINFO_FILENAME);
        $typeName = pathinfo($typePath, PATHINFO_FILENAME);
        if (str_contains($name, '/')) {
            $nameSpace = str_replace('/', '\\', dirname($name));
        } else {
            $nameSpace = $name;
        }

        $structFile = str_replace('{namespace}', $nameSpace, $structFile);
        if (!str_contains($name, '/')) {
            $structFile = str_replace("\\" . $name, '', $structFile);
        }
        $structFile = str_replace('{name}', $structName, $structFile);

        $properties = explode(',', $properties);
        if (!count($properties)) {
            return Command::INVALID;
        }

        $resultProperties = [];
        foreach ($properties as $p) {
            array_push($resultProperties, "\tpublic \$$p;\n");
        }

        $resultProperties[count($resultProperties) - 1] = str_replace("\n", '', $resultProperties[count($resultProperties) - 1]);

        $resultProperties = implode('', $resultProperties);

        $structFile = str_replace('{properties}', $resultProperties, $structFile);

        File::ensureDirectoryExists($structDir, 0755, true);
        File::ensureDirectoryExists($typeDir, 0755, true);

        $typeFile = str_replace('{namespace}', $nameSpace, $typeFile);
        $typeFile = str_replace('{usenamespace}', "\\" . $nameSpace . '\\' . $structName, $typeFile);
        $typeFile = str_replace('{name}', $typeName, $typeFile);
        $typeFile = str_replace('{use}', $structName, $typeFile);
        $typeFile = str_replace('{isObjectOfArray}', $isObjectOfArray == 'null' ? '' : '[]', $typeFile);

        File::put($structPath, $structFile);
        File::put($typePath, $typeFile);
        $this->info("DataStructure \"$structName\" berhasil dibuat di $structDir");
        $this->info("DataType \"$typeName\" berhasil dibuat di $typeDir");

        return Command::SUCCESS;
    }
}

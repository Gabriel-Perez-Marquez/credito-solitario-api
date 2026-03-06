<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Producto;

class CatalogoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $catAlimentos = Categoria::create(['nombre' => 'Alimentación Básica', 'activa' => true]);
        $catHigiene = Categoria::create(['nombre' => 'Higiene Personal', 'activa' => true]);
        $catInfantil = Categoria::create(['nombre' => 'Infantil', 'activa' => true]);

        // 2. Crear Productos
        $productos = [
            [
                'nombre' => 'Aceite de Oliva Virgen Extra 1L',
                'descripcion' => 'Botella de 1 litro de AOVE.',
                'precio' => 450,
                'stock' => 50,
                'urlImagen' => 'https://www.olibaza.com/tienda/193-thickbox_default/aceite-de-oliva-virgen-extra-1l.jpg',
                'activo' => true,
                'categoria_id' => $catAlimentos->id,
            ],
            [
                'nombre' => 'Arroz Blanco de Grano Redondo 1kg',
                'descripcion' => 'Paquete de 1kg de arroz.',
                'precio' => 120,
                'stock' => 100,
                'urlImagen' => 'https://images.piensavirtual.com/demogreen/core/images/8426904170267.JPG',
                'activo' => true,
                'categoria_id' => $catAlimentos->id,
            ],
            [
                'nombre' => 'Leche Entera 1L',
                'descripcion' => 'Brick de leche entera UHT.',
                'precio' => 90,
                'stock' => 200,
                'urlImagen' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR2nYhnCOvdwf1KWGZ9IiD6Y_8uVlSDaXllMw&s',
                'activo' => true,
                'categoria_id' => $catAlimentos->id,
            ],
            [
                'nombre' => 'Gel de Ducha Dermoprotector 750ml',
                'descripcion' => 'Gel de baño neutro para toda la familia.',
                'precio' => 150,
                'stock' => 40,
                'urlImagen' => 'https://sgfm.elcorteingles.es/SGFM/dctm/MEDIA03/202304/13/00155754104127____2__600x600.jpg',
                'activo' => true,
                'categoria_id' => $catHigiene->id,
            ],
        ];

        foreach ($productos as $prod) {
            $prod['descuento'] = 0; 
            Producto::create($prod);
        }
    }
}

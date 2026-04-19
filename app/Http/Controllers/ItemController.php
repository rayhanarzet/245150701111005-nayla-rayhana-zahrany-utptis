<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; 
use OpenApi\Attributes as OA;   

#[OA\Info(
    title: "API E-Commerce Sederhana",
    version: "1.0.0",
    description: "Dokumentasi API untuk UTP Teknologi Integrasi Sistem"
)]

class ItemController extends Controller
{
    private function getData()
    {
        return json_decode(file_get_contents(storage_path('app/items.json')), true);
    }

    private function saveData($data)
    {
        file_put_contents(storage_path('app/items.json'), json_encode($data, JSON_PRETTY_PRINT));
    }

    // fitur get all menampilkan item
   #[OA\Get(
        path: "/api/items",
        summary: "Get all items",
    responses: [
        new OA\Response(response: 200, description: "Success")
    ]
)]
    public function index()
    {
        return response()->json($this->getData());
    }

    // fitur get by id menampilkan barang berdasarkan parameter id  
    #[OA\Get(
        path: "/api/items/{id}",
        summary: "Get item by ID",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function show($id)
    {
        $data = $this->getData();

        foreach ($data as $item) {
            if ($item['id'] == $id) {
                return response()->json($item);
            }
        }
    // validation dan error handling
        return response()->json([
            "message" => "Item dengan ID $id tidak ditemukan"
        ], 404);
    }

    // fitur post membuat nama item dan/atau harga barang
   #[OA\Post(
        path: "/api/items",
        summary: "Create item",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama", "harga"],
                properties: [
                    new OA\Property(property: "nama", type: "string", example: "Sepatu"),
                    new OA\Property(property: "harga", type: "integer", example: 200000)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Created"),
            new OA\Response(response: 400, description: "Bad Request")
        ]
    )]
    public function store(Request $request)
    {
        $data = $this->getData();

    // validation dan error handling
        if (!isset($request->nama) || !isset($request->harga)) {
            return response()->json([
                "message" => "Nama dan harga wajib diisi"
            ], 400);
        }

        $newItem = [
            "id" => count($data) + 1,
            "nama" => $request->nama,
            "harga" => $request->harga
        ];

        $data[] = $newItem;
        $this->saveData($data);

        return response()->json($newItem, 201);
    }

    // fitur put mengedit seluruh data barang
    #[OA\Put(
        path: "/api/items/{id}",
        summary: "Update full item",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["nama", "harga"],
                properties: [
                    new OA\Property(property: "nama", type: "string"),
                    new OA\Property(property: "harga", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function update(Request $request, $id)
    {
        $data = $this->getData();

        foreach ($data as &$item) {
            if ($item['id'] == $id) {

    // validation dan error handling
                if (!isset($request->nama) || !isset($request->harga)) {
                    return response()->json([
                        "message" => "Nama dan harga wajib diisi"
                    ], 400);
                }

                $item['nama'] = $request->nama;
                $item['harga'] = $request->harga;

                $this->saveData($data);

                return response()->json($item);
            }
        }
    // validation dan error handling
        return response()->json([
            "message" => "Item dengan ID $id tidak ditemukan"
        ], 404);
    }

    // fitur patch mengedit salah satu data dari barang
  #[OA\Patch(
        path: "/api/items/{id}",
        summary: "Update partial item",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: "nama", type: "string"),
                    new OA\Property(property: "harga", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Success"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function patch(Request $request, $id)
    {
        $data = $this->getData();

        foreach ($data as &$item) {
            if ($item['id'] == $id) {

                if (isset($request->nama)) {
                    $item['nama'] = $request->nama;
                }

                if (isset($request->harga)) {
                    $item['harga'] = $request->harga;
                }

                $this->saveData($data);

                return response()->json($item);
            }
        }
    // validation dan error handling
        return response()->json([
            "message" => "Item dengan ID $id tidak ditemukan"
        ], 404);
    }

    // fitur delete menghapus barang
    #[OA\Delete(
        path: "/api/items/{id}",
        summary: "Delete item",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Deleted"),
            new OA\Response(response: 404, description: "Not Found")
        ]
    )]
    public function destroy($id)
    {
        $data = $this->getData();

        foreach ($data as $index => $item) {
            if ($item['id'] == $id) {

                array_splice($data, $index, 1);
                $this->saveData($data);

                return response()->json([
                    "message" => "Item berhasil dihapus"
                ]);
            }
        }
    // validation dan error handling
        return response()->json([
            "message" => "Item dengan ID $id tidak ditemukan"
        ], 404);
    }
}


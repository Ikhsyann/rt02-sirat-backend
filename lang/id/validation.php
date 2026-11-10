<?php

return [
    'required' => ':attribute wajib diisi.',
    'unique' => ':attribute sudah digunakan.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'in' => ':attribute yang dipilih tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'date' => ':attribute bukan tanggal yang valid.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'before_or_equal' => ':attribute tidak boleh lebih dari :date.',
    'after_or_equal' => ':attribute tidak boleh kurang dari :date.',

    'max' => [
        'string' => ':attribute maksimal :max karakter.',
        'numeric' => ':attribute maksimal :max.',
    ],
    'min' => [
        'string' => ':attribute minimal :min karakter.',
        'numeric' => ':attribute minimal :min.',
    ],
    'size' => [
        'string' => ':attribute harus :size karakter.',
        'numeric' => ':attribute harus :size.',
    ],
];

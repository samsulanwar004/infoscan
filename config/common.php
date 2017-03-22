<?php

return [
    'snap_category' => [
        'rc' => 'Moderen Receipt',
        'gt' => 'General Trade',
        'hw' => 'Hand Written',
    ],
    'snap_category_mode' => [
        'in' => 'Input',
        'ta' => 'Tags',
        'au' => 'Audios'
    ],
    'snap_mode' => [
        'in' => 'input',
        'ta' => 'tags',
        'au' => 'audios',
    ],
    'snap_type' => [
    	'rc' => 'receipt',
    	'gt' => 'generaltrade',
    	'hw' => 'handwritten',
    ],
    'payment_methods' => [
        'cash' => 'Cash',
        'debit_card' => 'Debit Card',
        'credit_card' => 'Credit Card',
    ],
    'reports' => [
        'fields' => [
            'user_id' => [
                'label' => 'User ID',
                'type' => 'multiple'
            ],
            'age' => [
                'label' => 'Age',
                'type' => 'range',
                'data' => [
                    'min' => 17,
                    'max' => 100,
                ]
            ],
            'province' => [
                'label' => 'Province',
                'type' => 'multiple'
            ],
            'gender' => [
                'label' => 'Gender',
                'type' => 'multiple'
            ],
            'occupation' => [
                'label' => 'Occupation',
                'type' => 'single'
            ],
            'person_in_house' => [
                'label' => 'Person in House',
                'type' => 'range',
                'data' => [
                    'min' => 1,
                    'max' => 5,
                ]
            ],
            'last_education' => [
                'label' => 'Latest Education',
                'type' => 'multiple',
                'data' => [
                    'min' => 0,
                    'max' => 1000000,
                ]
            ],
            'users_city' => [
                'label' => 'City',
                'type' => 'multiple'
            ],
            'sec' => [
                'label' => 'SEC',
                'type' => 'single'
            ],
            'usership' => [
                'label' => 'User Ship',
                'type' => 'single'
            ],
            'receipt_number' => [
                'label' => 'Receipt Number',
                'type' => 'input'
            ],
            'outlet_type' => [
                'label' => 'Outlet Type',
                'type' => 'single'
            ],
            'outlet_name' => [
                'label' => 'Outlet Name',
                'type' => 'single'
            ],
            'outlet_province' => [
                'label' => 'Outlet Province',
                'type' => 'single'
            ],
            'outlet_city' => [
                'label' => 'Outlet City',
                'type' => 'single'
            ],
            'outlet_address' => [
                'label' => 'Outlet Address',
                'type' => 'input'
            ],
            'products' => [
                'label' => 'Products',
                'type' => 'multiple'
            ],
            'brand' => [
                'label' => 'Brands',
                'type' => 'single'
            ],
            'quantity' => [
                'label' => 'Quantity',
                'type' => 'range',
                'data' => [
                    'min' => 0,
                    'max' => 1000,
                ]
            ],
            'total_price_quantity' => [
                'label' => 'Total Price',
                'type' => 'range',
                'data' => [
                    'min' => 0,
                    'max' => 3000,
                ]
            ],
            'grand_total_price' => [
                'label' => 'Grand Total Price',
                'type' => 'range',
                'data' => [
                    'min' => 0,
                    'max' => 3000,
                ]
            ],
            'purchase_date' => [
                'label' => 'Purchase Date',
                'type' => 'dateRange',
                'data' => [
                    'min' => -0,
                    'max' => \Carbon\Carbon::today()->toDateString(),
                    'format' => 'YYYY-MM-DD',
                ]
            ],
            'sent_time' => [
                'label' => 'Sent Date',
                'type' => 'dateRange',
                'data' => [
                    'min' => -0,
                    'max' => \Carbon\Carbon::today()->toDateString(),
                    'format' => 'YYYY-MM-DD',
                ]
            ],
        ],
        'ignored_fields' => [
            'user_id',
            'receipt_number',
        ]
    ],

    // tasks code
    'tasks' => [

        'types' => [
            'a' => 'Receipt',
            'b' => 'Handwritten',
            'c' => 'General Trade',
        ],

        'select_mode' => [
            'a' => [
                '1' => [
                    'label' => 'No Mode',
                ],
            ],
            'b' => [
                '1' => [
                    'label' => 'No Mode',
                ],
                '2' => [
                    'label' => 'With Input',
                ],
                '3' => [
                    'label' => 'With Audio',
                ],
            ],
            'c' => [
                '1' => [
                    'label' => 'No Mode',
                ],
                '2' => [
                    'label' => 'With Tag',
                ],
                '3' => [
                    'label' => 'With Audio',
                ],
            ],
        ]
    ],

    'latest_educations' => [
        'SD', 'SMP', 'SMA', 'Diploma', 'Sarjana', 'Other',
    ],

    'banks' => [
        'BCA',
    ],

    'genders' => [
        ['id' => 'male', 'name' => 'Pria'],
        ['id' => 'female', 'name' => 'Wanita'],
    ],

    'marital_statuses' => [
        ['id' => 'single', 'name' => 'Lajang'],
        ['id' => 'married', 'name' => 'Menikah'],
    ],


    //transaction
    'transaction' => [
        'transaction_type' => [
            'snaps' => '1',
            'redeem' => '2',
            'survey' => '3',
            'lottery' => '4',
        ],
        'member' => [
            'cashier' => '101',
            'cashier_money' => '102',
        ],
    ],
    //chart
    'chart' => [
        'month' => [
            '1' => 'January',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        ],
        'color' => [
            '0' => [
                'background' => 'rgba(255, 99, 132, 0.2)',
                'border' => 'rgba(255,99,132,1)',
            ],
            '1' => [
                'background' => 'rgba(54, 162, 235, 0.2)',
                'border' => 'rgba(54, 162, 235, 1)',
            ],
            '2' => [
                'background' => 'rgba(255, 206, 86, 0.2)',
                'border' => 'rgba(255, 206, 86, 1)',
            ],
            '3' => [
                'background' => 'rgba(75, 192, 192, 0.2)',
                'border' => 'rgba(75, 192, 192, 1)',
            ],
            '4' => [
                'background' => 'rgba(153, 102, 255, 0.2)',
                'border' => 'rgba(153, 102, 255, 1)',
            ],
            '5' => [
                'background' => 'rgba(255, 159, 64, 0.2)',
                'border' => 'rgba(255, 159, 64, 1)',
            ],
        ],
    ],

    //queue list
    'queue_list' => [
        'crowdsource_log' => 'userBehaviourLog',
        'member_log' => 'userBehaviourLog',
        'point_process' => 'pointProcess',
        'transaction_process' => 'transactionProcess',
        'assign_process' => 'assignProcess',
        'member_action_log' => 'userBehaviourLog',
        'member_register_verification_email' => 'userBehaviourLog',
    ],

    'notification_messages' => [
        'snaps' => [
            'success' => 'Selamat, klaim sebesar %s poin telah berhasil! Kluk!',
            'pending' => 'Fotomu sudah kami terima. Kamu bisa mendapatkan bonus sebesar %s poin!',
            'failed' => 'Maaf, Transaksi kamu belum berhasil. Ayo coba lagi!',
        ],

        'lottery' => [
            'success' => 'Kamu adalah PEMENANG! - Selamat! Kamu telah memenangkan undian %s.',
            'failed' => 'Maaf, kamu belum beruntung - Ayo coba keberuntunganmu di undian lainnya!',
        ],

        'cashback' => [
            'success' => 'Cashback Berhasil - Selamat, cashback telah berhasil! Kluk!',
            'failed' => 'Cashback Gagal - Maaf, cashback kamu gagal :(',
        ],

        'register' => [
            'verification' => 'Verifikasi Email - Verifikasi akun GoJaGomu sekarang!',
            'resend' => 'Akun Belum Terverifikasi - Kami sudah mengirim ulang email verifikasi ke emailmu. Verifikasi akunmu untuk bisa cashback!'
        ],
    ],
];
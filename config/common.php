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
            'user_city' => [
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
            'purchase_time' => [
                'label' => 'Purchase Time',
                'type' => 'range',
                'data' => [
                    'min' => 0,
                    'max' => 3000,
                ]
            ],
            'sent_time' => [
                'label' => 'Time Sent',
                'type' => 'range',
                'data' => [
                    'min' => 0,
                    'max' => 3000,
                ]
            ],
            //'monthly_expense' => 'Monthly Expense',
        ]
    ]
];

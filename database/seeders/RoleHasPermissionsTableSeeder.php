<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleHasPermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('role_has_permissions')->delete();
        
        \DB::table('role_has_permissions')->insert(array (
            0 => 
            array (
                'permission_id' => '019f6bb3-5744-7148-a55e-30db1754acb7',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            1 => 
            array (
                'permission_id' => '019f6bb3-574a-7138-bc73-dfc27bee59ef',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            2 => 
            array (
                'permission_id' => '019f6bb3-574f-73f3-bd1d-184b192a8687',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            3 => 
            array (
                'permission_id' => '019f6bb3-5753-7272-874a-6f2046eb687b',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            4 => 
            array (
                'permission_id' => '019f6bb3-5756-711b-9da8-ac84b272cf16',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            5 => 
            array (
                'permission_id' => '019f6bb3-575b-7042-a72f-6ca27e4245d8',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            6 => 
            array (
                'permission_id' => '019f6bb3-575f-7079-97e3-2a8437bdcf67',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            7 => 
            array (
                'permission_id' => '019f6bb3-5763-71bb-9d49-660400e636a8',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            8 => 
            array (
                'permission_id' => '019f6bb3-5767-70cc-af1f-9adb37260a58',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            9 => 
            array (
                'permission_id' => '019f6bb3-576b-7121-b7fb-e988794c7db5',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            10 => 
            array (
                'permission_id' => '019f6bb3-5770-72b5-96ab-46546c4b03ef',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            11 => 
            array (
                'permission_id' => '019f6bb3-5777-7120-8e52-6fa32dc4d0fd',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            12 => 
            array (
                'permission_id' => '019f6bb3-5791-73c5-b9c6-309ef51e2ab7',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            13 => 
            array (
                'permission_id' => '019f6bb3-5796-7260-8f3c-bce962e45399',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            14 => 
            array (
                'permission_id' => '019f6bb3-579a-719a-927a-92b87a2c7540',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            15 => 
            array (
                'permission_id' => '019f6bb3-579e-7188-8e16-b2aa019c9422',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            16 => 
            array (
                'permission_id' => '019f6bb3-57a2-73ae-9dd3-c632cd8fd729',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            17 => 
            array (
                'permission_id' => '019f6bb3-57a6-7144-b2e2-a5b33b685a60',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            18 => 
            array (
                'permission_id' => '019f6bb3-57aa-71a4-a703-eaca0f6d66b0',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            19 => 
            array (
                'permission_id' => '019f6bb3-57ae-7067-9c59-5774be73582a',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            20 => 
            array (
                'permission_id' => '019f6bb3-57b2-72c6-bd2c-aa8fec77a53d',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            21 => 
            array (
                'permission_id' => '019f6bb3-57b6-7271-a5c5-6b8be7d0703d',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            22 => 
            array (
                'permission_id' => '019f6bb3-57ba-7275-a6ad-fa690d1ee4d0',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            23 => 
            array (
                'permission_id' => '019f6bb3-57be-7324-b3d6-ab0074c49f5e',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            24 => 
            array (
                'permission_id' => '019f6bb3-57cf-702f-9858-2a05581e8ee8',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            25 => 
            array (
                'permission_id' => '019f6bb3-57d3-715e-9311-211048ea7f87',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            26 => 
            array (
                'permission_id' => '019f6bb3-57d7-71fd-a79d-5a3c8ffd5d38',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            27 => 
            array (
                'permission_id' => '019f6bb3-57dc-7010-af7a-f72e0296f6b1',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            28 => 
            array (
                'permission_id' => '019f6bb3-57e0-70e9-afcd-6d1697ee0f01',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            29 => 
            array (
                'permission_id' => '019f6bb3-57e6-7261-a613-9ca9d9f45bc1',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            30 => 
            array (
                'permission_id' => '019f6bb3-57ea-71b8-ac72-70d62ddc05bb',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            31 => 
            array (
                'permission_id' => '019f6bb3-57ee-737c-8a80-9d8d639ae8a8',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            32 => 
            array (
                'permission_id' => '019f6bb3-57f2-73e8-b464-84819eead974',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            33 => 
            array (
                'permission_id' => '019f6bb3-57f6-700e-bf07-3f2b69f46673',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            34 => 
            array (
                'permission_id' => '019f6bb3-57fa-71ec-9a2c-ae509196686c',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            35 => 
            array (
                'permission_id' => '019f6bb3-57fe-727a-a15a-5a55d6d215e2',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            36 => 
            array (
                'permission_id' => '019f6bb3-580e-735e-adf2-ec12a7375dcb',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            37 => 
            array (
                'permission_id' => '019f6bb3-5812-72d3-886b-dacbc9066b37',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            38 => 
            array (
                'permission_id' => '019f6bb3-5816-739f-a1c0-d64d1c27bfd4',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            39 => 
            array (
                'permission_id' => '019f6bb3-581a-731b-b78f-2fde02240ec1',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            40 => 
            array (
                'permission_id' => '019f6bb3-581e-71c4-8fda-5966a456d198',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            41 => 
            array (
                'permission_id' => '019f6bb3-5823-739e-8d50-18f8ccd37e15',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            42 => 
            array (
                'permission_id' => '019f6bb3-5827-7213-9981-9666eac06277',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            43 => 
            array (
                'permission_id' => '019f6bb3-582b-7004-812e-c6f982e996cf',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            44 => 
            array (
                'permission_id' => '019f6bb3-582f-73b9-8625-f17fa0a2a44e',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            45 => 
            array (
                'permission_id' => '019f6bb3-5833-70e8-8c2a-1e9cfea31179',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            46 => 
            array (
                'permission_id' => '019f6bb3-5837-7395-adc1-19bbdbfd682b',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            47 => 
            array (
                'permission_id' => '019f6bb3-583b-7274-979e-a63f28d8bc45',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            48 => 
            array (
                'permission_id' => '019f6bb3-584c-71ec-b9fc-264b2b2541e5',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            49 => 
            array (
                'permission_id' => '019f6bb3-585d-70f7-b51a-df838c315496',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            50 => 
            array (
                'permission_id' => '019f6bb3-586d-70d3-bc2f-813ba9cc8870',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            51 => 
            array (
                'permission_id' => '019f6bb3-5881-716b-bfdd-1e5748f0c520',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            52 => 
            array (
                'permission_id' => '019f6bb3-5893-7278-9d08-c6b1bc562957',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            53 => 
            array (
                'permission_id' => '019f6bb3-58a3-7114-9c7a-0cd063fb45fb',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            54 => 
            array (
                'permission_id' => '019f6bb3-58b3-7104-947e-5535d76e3e03',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            55 => 
            array (
                'permission_id' => '019f6bb3-58c3-72d8-b8a7-420a8ba6ddba',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            56 => 
            array (
                'permission_id' => '019f6bb3-58d4-72c1-9cce-3e289b36d528',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            57 => 
            array (
                'permission_id' => '019f6bb3-58e4-715a-902a-1c47bf82c99c',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            58 => 
            array (
                'permission_id' => '019f6bb3-58f5-734e-8de5-5e8a65575bb4',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            59 => 
            array (
                'permission_id' => '019f6bbb-e108-7169-b4ff-4bf32e01a849',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            60 => 
            array (
                'permission_id' => '019f6bb3-5906-7047-ba40-c1c0f5a65fda',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            61 => 
            array (
                'permission_id' => '019f6bb3-5918-739e-b40e-1c4e6f449b76',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            62 => 
            array (
                'permission_id' => '019f6bb3-5929-70f1-9859-fd16e8396998',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            63 => 
            array (
                'permission_id' => '019f6bb3-593a-72e2-9061-d34210aa8189',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            64 => 
            array (
                'permission_id' => '019f6bc4-4c36-73e2-97c1-b64fd0493e62',
                'role_id' => '019f6bb5-5a3b-7012-b2b2-74d87fb30345',
            ),
            65 => 
            array (
                'permission_id' => '019f6bb3-5918-739e-b40e-1c4e6f449b76',
                'role_id' => '019f6bd6-f303-709d-9c5a-ac4bd54bd49c',
            ),
            66 => 
            array (
                'permission_id' => '019f6bb3-5929-70f1-9859-fd16e8396998',
                'role_id' => '019f6bd6-f303-709d-9c5a-ac4bd54bd49c',
            ),
            67 => 
            array (
                'permission_id' => '019f6bb3-593a-72e2-9061-d34210aa8189',
                'role_id' => '019f6bd6-f303-709d-9c5a-ac4bd54bd49c',
            ),
            68 => 
            array (
                'permission_id' => '019f6bb3-5744-7148-a55e-30db1754acb7',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            69 => 
            array (
                'permission_id' => '019f6bb3-574a-7138-bc73-dfc27bee59ef',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            70 => 
            array (
                'permission_id' => '019f6bb3-574f-73f3-bd1d-184b192a8687',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            71 => 
            array (
                'permission_id' => '019f6bb3-5753-7272-874a-6f2046eb687b',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            72 => 
            array (
                'permission_id' => '019f6bb3-5756-711b-9da8-ac84b272cf16',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            73 => 
            array (
                'permission_id' => '019f6bb3-575b-7042-a72f-6ca27e4245d8',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            74 => 
            array (
                'permission_id' => '019f6bb3-575f-7079-97e3-2a8437bdcf67',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            75 => 
            array (
                'permission_id' => '019f6bb3-5763-71bb-9d49-660400e636a8',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            76 => 
            array (
                'permission_id' => '019f6bb3-5767-70cc-af1f-9adb37260a58',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            77 => 
            array (
                'permission_id' => '019f6bb3-576b-7121-b7fb-e988794c7db5',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            78 => 
            array (
                'permission_id' => '019f6bb3-5770-72b5-96ab-46546c4b03ef',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            79 => 
            array (
                'permission_id' => '019f6bb3-5777-7120-8e52-6fa32dc4d0fd',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            80 => 
            array (
                'permission_id' => '019f6bb3-5791-73c5-b9c6-309ef51e2ab7',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            81 => 
            array (
                'permission_id' => '019f6bb3-5796-7260-8f3c-bce962e45399',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            82 => 
            array (
                'permission_id' => '019f6bb3-579a-719a-927a-92b87a2c7540',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            83 => 
            array (
                'permission_id' => '019f6bb3-579e-7188-8e16-b2aa019c9422',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            84 => 
            array (
                'permission_id' => '019f6bb3-57a2-73ae-9dd3-c632cd8fd729',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            85 => 
            array (
                'permission_id' => '019f6bb3-57a6-7144-b2e2-a5b33b685a60',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            86 => 
            array (
                'permission_id' => '019f6bb3-57aa-71a4-a703-eaca0f6d66b0',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            87 => 
            array (
                'permission_id' => '019f6bb3-57ae-7067-9c59-5774be73582a',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            88 => 
            array (
                'permission_id' => '019f6bb3-57b2-72c6-bd2c-aa8fec77a53d',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            89 => 
            array (
                'permission_id' => '019f6bb3-57b6-7271-a5c5-6b8be7d0703d',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            90 => 
            array (
                'permission_id' => '019f6bb3-57ba-7275-a6ad-fa690d1ee4d0',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            91 => 
            array (
                'permission_id' => '019f6bb3-57be-7324-b3d6-ab0074c49f5e',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            92 => 
            array (
                'permission_id' => '019f6bb3-57cf-702f-9858-2a05581e8ee8',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            93 => 
            array (
                'permission_id' => '019f6bb3-57d3-715e-9311-211048ea7f87',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            94 => 
            array (
                'permission_id' => '019f6bb3-57d7-71fd-a79d-5a3c8ffd5d38',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            95 => 
            array (
                'permission_id' => '019f6bb3-57dc-7010-af7a-f72e0296f6b1',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            96 => 
            array (
                'permission_id' => '019f6bb3-57e0-70e9-afcd-6d1697ee0f01',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            97 => 
            array (
                'permission_id' => '019f6bb3-57e6-7261-a613-9ca9d9f45bc1',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            98 => 
            array (
                'permission_id' => '019f6bb3-57ea-71b8-ac72-70d62ddc05bb',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            99 => 
            array (
                'permission_id' => '019f6bb3-57ee-737c-8a80-9d8d639ae8a8',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            100 => 
            array (
                'permission_id' => '019f6bb3-57f2-73e8-b464-84819eead974',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            101 => 
            array (
                'permission_id' => '019f6bb3-57f6-700e-bf07-3f2b69f46673',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            102 => 
            array (
                'permission_id' => '019f6bb3-57fa-71ec-9a2c-ae509196686c',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            103 => 
            array (
                'permission_id' => '019f6bb3-57fe-727a-a15a-5a55d6d215e2',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            104 => 
            array (
                'permission_id' => '019f6bb3-580e-735e-adf2-ec12a7375dcb',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            105 => 
            array (
                'permission_id' => '019f6bb3-5812-72d3-886b-dacbc9066b37',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            106 => 
            array (
                'permission_id' => '019f6bb3-5816-739f-a1c0-d64d1c27bfd4',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            107 => 
            array (
                'permission_id' => '019f6bb3-581a-731b-b78f-2fde02240ec1',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            108 => 
            array (
                'permission_id' => '019f6bb3-581e-71c4-8fda-5966a456d198',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            109 => 
            array (
                'permission_id' => '019f6bb3-5823-739e-8d50-18f8ccd37e15',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            110 => 
            array (
                'permission_id' => '019f6bb3-5827-7213-9981-9666eac06277',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            111 => 
            array (
                'permission_id' => '019f6bb3-582b-7004-812e-c6f982e996cf',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            112 => 
            array (
                'permission_id' => '019f6bb3-582f-73b9-8625-f17fa0a2a44e',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            113 => 
            array (
                'permission_id' => '019f6bb3-5833-70e8-8c2a-1e9cfea31179',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            114 => 
            array (
                'permission_id' => '019f6bb3-5837-7395-adc1-19bbdbfd682b',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            115 => 
            array (
                'permission_id' => '019f6bb3-583b-7274-979e-a63f28d8bc45',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            116 => 
            array (
                'permission_id' => '019f6bb3-584c-71ec-b9fc-264b2b2541e5',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            117 => 
            array (
                'permission_id' => '019f6bb3-585d-70f7-b51a-df838c315496',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            118 => 
            array (
                'permission_id' => '019f6bb3-586d-70d3-bc2f-813ba9cc8870',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            119 => 
            array (
                'permission_id' => '019f6bb3-5881-716b-bfdd-1e5748f0c520',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            120 => 
            array (
                'permission_id' => '019f6bb3-5893-7278-9d08-c6b1bc562957',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            121 => 
            array (
                'permission_id' => '019f6bb3-58a3-7114-9c7a-0cd063fb45fb',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            122 => 
            array (
                'permission_id' => '019f6bb3-58b3-7104-947e-5535d76e3e03',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            123 => 
            array (
                'permission_id' => '019f6bb3-58c3-72d8-b8a7-420a8ba6ddba',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            124 => 
            array (
                'permission_id' => '019f6bb3-58d4-72c1-9cce-3e289b36d528',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            125 => 
            array (
                'permission_id' => '019f6bb3-58e4-715a-902a-1c47bf82c99c',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            126 => 
            array (
                'permission_id' => '019f6bb3-58f5-734e-8de5-5e8a65575bb4',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            127 => 
            array (
                'permission_id' => '019f6bbb-e108-7169-b4ff-4bf32e01a849',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            128 => 
            array (
                'permission_id' => '019f6bb3-5906-7047-ba40-c1c0f5a65fda',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            129 => 
            array (
                'permission_id' => '019f7561-1754-72dd-92cf-d8cec4fac63e',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            130 => 
            array (
                'permission_id' => '019f7561-1759-701a-a681-83fdc70bc958',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
            131 => 
            array (
                'permission_id' => '019f6bc4-4c36-73e2-97c1-b64fd0493e62',
                'role_id' => '019f6bb3-577e-716e-9413-b73c43833c4b',
            ),
        ));
        
        
    }
}
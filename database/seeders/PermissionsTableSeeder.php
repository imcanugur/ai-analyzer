<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('permissions')->delete();
        
        \DB::table('permissions')->insert(array (
            0 => 
            array (
                'id' => '019f6bb3-5744-7148-a55e-30db1754acb7',
                'name' => 'ViewAny:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            1 => 
            array (
                'id' => '019f6bb3-574a-7138-bc73-dfc27bee59ef',
                'name' => 'View:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            2 => 
            array (
                'id' => '019f6bb3-574f-73f3-bd1d-184b192a8687',
                'name' => 'Create:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            3 => 
            array (
                'id' => '019f6bb3-5753-7272-874a-6f2046eb687b',
                'name' => 'Update:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            4 => 
            array (
                'id' => '019f6bb3-5756-711b-9da8-ac84b272cf16',
                'name' => 'Delete:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            5 => 
            array (
                'id' => '019f6bb3-575b-7042-a72f-6ca27e4245d8',
                'name' => 'DeleteAny:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            6 => 
            array (
                'id' => '019f6bb3-575f-7079-97e3-2a8437bdcf67',
                'name' => 'Restore:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            7 => 
            array (
                'id' => '019f6bb3-5763-71bb-9d49-660400e636a8',
                'name' => 'ForceDelete:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            8 => 
            array (
                'id' => '019f6bb3-5767-70cc-af1f-9adb37260a58',
                'name' => 'ForceDeleteAny:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            9 => 
            array (
                'id' => '019f6bb3-576b-7121-b7fb-e988794c7db5',
                'name' => 'RestoreAny:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            10 => 
            array (
                'id' => '019f6bb3-5770-72b5-96ab-46546c4b03ef',
                'name' => 'Replicate:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            11 => 
            array (
                'id' => '019f6bb3-5777-7120-8e52-6fa32dc4d0fd',
                'name' => 'Reorder:Node',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            12 => 
            array (
                'id' => '019f6bb3-5791-73c5-b9c6-309ef51e2ab7',
                'name' => 'ViewAny:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            13 => 
            array (
                'id' => '019f6bb3-5796-7260-8f3c-bce962e45399',
                'name' => 'View:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            14 => 
            array (
                'id' => '019f6bb3-579a-719a-927a-92b87a2c7540',
                'name' => 'Create:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            15 => 
            array (
                'id' => '019f6bb3-579e-7188-8e16-b2aa019c9422',
                'name' => 'Update:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            16 => 
            array (
                'id' => '019f6bb3-57a2-73ae-9dd3-c632cd8fd729',
                'name' => 'Delete:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            17 => 
            array (
                'id' => '019f6bb3-57a6-7144-b2e2-a5b33b685a60',
                'name' => 'DeleteAny:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            18 => 
            array (
                'id' => '019f6bb3-57aa-71a4-a703-eaca0f6d66b0',
                'name' => 'Restore:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            19 => 
            array (
                'id' => '019f6bb3-57ae-7067-9c59-5774be73582a',
                'name' => 'ForceDelete:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            20 => 
            array (
                'id' => '019f6bb3-57b2-72c6-bd2c-aa8fec77a53d',
                'name' => 'ForceDeleteAny:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            21 => 
            array (
                'id' => '019f6bb3-57b6-7271-a5c5-6b8be7d0703d',
                'name' => 'RestoreAny:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            22 => 
            array (
                'id' => '019f6bb3-57ba-7275-a6ad-fa690d1ee4d0',
                'name' => 'Replicate:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            23 => 
            array (
                'id' => '019f6bb3-57be-7324-b3d6-ab0074c49f5e',
                'name' => 'Reorder:StageRoute',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            24 => 
            array (
                'id' => '019f6bb3-57cf-702f-9858-2a05581e8ee8',
                'name' => 'ViewAny:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            25 => 
            array (
                'id' => '019f6bb3-57d3-715e-9311-211048ea7f87',
                'name' => 'View:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            26 => 
            array (
                'id' => '019f6bb3-57d7-71fd-a79d-5a3c8ffd5d38',
                'name' => 'Create:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            27 => 
            array (
                'id' => '019f6bb3-57dc-7010-af7a-f72e0296f6b1',
                'name' => 'Update:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            28 => 
            array (
                'id' => '019f6bb3-57e0-70e9-afcd-6d1697ee0f01',
                'name' => 'Delete:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            29 => 
            array (
                'id' => '019f6bb3-57e6-7261-a613-9ca9d9f45bc1',
                'name' => 'DeleteAny:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            30 => 
            array (
                'id' => '019f6bb3-57ea-71b8-ac72-70d62ddc05bb',
                'name' => 'Restore:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            31 => 
            array (
                'id' => '019f6bb3-57ee-737c-8a80-9d8d639ae8a8',
                'name' => 'ForceDelete:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            32 => 
            array (
                'id' => '019f6bb3-57f2-73e8-b464-84819eead974',
                'name' => 'ForceDeleteAny:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            33 => 
            array (
                'id' => '019f6bb3-57f6-700e-bf07-3f2b69f46673',
                'name' => 'RestoreAny:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            34 => 
            array (
                'id' => '019f6bb3-57fa-71ec-9a2c-ae509196686c',
                'name' => 'Replicate:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            35 => 
            array (
                'id' => '019f6bb3-57fe-727a-a15a-5a55d6d215e2',
                'name' => 'Reorder:Submission',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            36 => 
            array (
                'id' => '019f6bb3-580e-735e-adf2-ec12a7375dcb',
                'name' => 'ViewAny:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            37 => 
            array (
                'id' => '019f6bb3-5812-72d3-886b-dacbc9066b37',
                'name' => 'View:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            38 => 
            array (
                'id' => '019f6bb3-5816-739f-a1c0-d64d1c27bfd4',
                'name' => 'Create:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            39 => 
            array (
                'id' => '019f6bb3-581a-731b-b78f-2fde02240ec1',
                'name' => 'Update:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            40 => 
            array (
                'id' => '019f6bb3-581e-71c4-8fda-5966a456d198',
                'name' => 'Delete:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            41 => 
            array (
                'id' => '019f6bb3-5823-739e-8d50-18f8ccd37e15',
                'name' => 'DeleteAny:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            42 => 
            array (
                'id' => '019f6bb3-5827-7213-9981-9666eac06277',
                'name' => 'Restore:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            43 => 
            array (
                'id' => '019f6bb3-582b-7004-812e-c6f982e996cf',
                'name' => 'ForceDelete:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            44 => 
            array (
                'id' => '019f6bb3-582f-73b9-8625-f17fa0a2a44e',
                'name' => 'ForceDeleteAny:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            45 => 
            array (
                'id' => '019f6bb3-5833-70e8-8c2a-1e9cfea31179',
                'name' => 'RestoreAny:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            46 => 
            array (
                'id' => '019f6bb3-5837-7395-adc1-19bbdbfd682b',
                'name' => 'Replicate:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            47 => 
            array (
                'id' => '019f6bb3-583b-7274-979e-a63f28d8bc45',
                'name' => 'Reorder:Role',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            48 => 
            array (
                'id' => '019f6bb3-584c-71ec-b9fc-264b2b2541e5',
                'name' => 'View:BatchPreview',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            49 => 
            array (
                'id' => '019f6bb3-585d-70f7-b51a-df838c315496',
                'name' => 'View:Batches',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            50 => 
            array (
                'id' => '019f6bb3-586d-70d3-bc2f-813ba9cc8870',
                'name' => 'View:Dashboard',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            51 => 
            array (
                'id' => '019f6bb3-5881-716b-bfdd-1e5748f0c520',
                'name' => 'View:FailedJobPreview',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            52 => 
            array (
                'id' => '019f6bb3-5893-7278-9d08-c6b1bc562957',
                'name' => 'View:FailedJobs',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            53 => 
            array (
                'id' => '019f6bb3-58a3-7114-9c7a-0cd063fb45fb',
                'name' => 'View:JobPreview',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            54 => 
            array (
                'id' => '019f6bb3-58b3-7104-947e-5535d76e3e03',
                'name' => 'View:Metrics',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            55 => 
            array (
                'id' => '019f6bb3-58c3-72d8-b8a7-420a8ba6ddba',
                'name' => 'View:MetricsPreview',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            56 => 
            array (
                'id' => '019f6bb3-58d4-72c1-9cce-3e289b36d528',
                'name' => 'View:Monitoring',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            57 => 
            array (
                'id' => '019f6bb3-58e4-715a-902a-1c47bf82c99c',
                'name' => 'View:MonitoringTag',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            58 => 
            array (
                'id' => '019f6bb3-58f5-734e-8de5-5e8a65575bb4',
                'name' => 'View:RecentJobs',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            59 => 
            array (
                'id' => '019f6bb3-5906-7047-ba40-c1c0f5a65fda',
                'name' => 'View:SendNotification',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            60 => 
            array (
                'id' => '019f6bb3-5918-739e-b40e-1c4e6f449b76',
                'name' => 'View:StatsOverview',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            61 => 
            array (
                'id' => '019f6bb3-5929-70f1-9859-fd16e8396998',
                'name' => 'View:WorkloadWidget',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            62 => 
            array (
                'id' => '019f6bb3-593a-72e2-9061-d34210aa8189',
                'name' => 'View:WorkersWidget',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:12:23',
                'updated_at' => '2026-07-16 16:12:23',
            ),
            63 => 
            array (
                'id' => '019f6bb7-9712-71cf-8ee4-05e2d6276dae',
                'name' => 'ViewAny:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            64 => 
            array (
                'id' => '019f6bb7-9717-7254-b3f6-52cbcfab7402',
                'name' => 'View:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            65 => 
            array (
                'id' => '019f6bb7-971c-729f-a4ae-30492db5e529',
                'name' => 'Create:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            66 => 
            array (
                'id' => '019f6bb7-9721-722c-a8c0-8bd333635004',
                'name' => 'Update:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            67 => 
            array (
                'id' => '019f6bb7-9726-7330-beff-0c96076c8b87',
                'name' => 'Delete:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            68 => 
            array (
                'id' => '019f6bb7-972a-72e5-b2db-b5cc52c371de',
                'name' => 'DeleteAny:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            69 => 
            array (
                'id' => '019f6bb7-972f-71d0-92e6-8b66e73a7957',
                'name' => 'Restore:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            70 => 
            array (
                'id' => '019f6bb7-9733-70a2-930f-2f40113c8f5a',
                'name' => 'ForceDelete:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            71 => 
            array (
                'id' => '019f6bb7-9737-70fa-a0ba-314c089aff5c',
                'name' => 'ForceDeleteAny:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            72 => 
            array (
                'id' => '019f6bb7-973b-7312-979a-b48a0a9e87ba',
                'name' => 'RestoreAny:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            73 => 
            array (
                'id' => '019f6bb7-9740-72bd-98a5-718873050a22',
                'name' => 'Replicate:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            74 => 
            array (
                'id' => '019f6bb7-9744-7141-9b8f-294bf75545a1',
                'name' => 'Reorder:User',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:17:01',
                'updated_at' => '2026-07-16 16:17:01',
            ),
            75 => 
            array (
                'id' => '019f6bbb-e108-7169-b4ff-4bf32e01a849',
                'name' => 'View:RoleAssignment',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:21:42',
                'updated_at' => '2026-07-16 16:21:42',
            ),
            76 => 
            array (
                'id' => '019f6bc4-4c36-73e2-97c1-b64fd0493e62',
                'name' => 'ViewHorizon',
                'guard_name' => 'web',
                'created_at' => '2026-07-16 16:30:54',
                'updated_at' => '2026-07-16 16:30:54',
            ),
            77 => 
            array (
                'id' => '019f7561-1754-72dd-92cf-d8cec4fac63e',
                'name' => 'View:AccountWidget',
                'guard_name' => 'web',
                'created_at' => '2026-07-18 13:18:45',
                'updated_at' => '2026-07-18 13:18:45',
            ),
            78 => 
            array (
                'id' => '019f7561-1759-701a-a681-83fdc70bc958',
                'name' => 'View:InfoWidget',
                'guard_name' => 'web',
                'created_at' => '2026-07-18 13:18:45',
                'updated_at' => '2026-07-18 13:18:45',
            ),
        ));
        
        
    }
}
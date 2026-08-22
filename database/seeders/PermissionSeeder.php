<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [

            // === 1. قسم إدارة المستخدمين والملفات الشخصية (Users & Profiles) ===
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'view_profiles',
            'edit_profiles',

            // === 2. قسم المنشآت الطبية والفروع (Facilities & Branches) ===
            'view_facilities',
            'create_facilities',
            'edit_facilities',
            'delete_facilities',

            // === 3. قسم الأطباء والتخصصات (Doctors & Specializations) ===
            'view_doctors',
            'create_doctors',
            'edit_doctors',
            'delete_doctors',
            'manage_specializations',

            // === 4. قسم المواعيد والجدولة (Appointments & Scheduling) ===
            'view_appointments',
            'create_appointments', // للمريض أو موظف الاستقبال
            'edit_appointments',
            'cancel_appointments',
            'manage_doctor_schedules', // لتحديد أوقات دوام الأطباء

            // === 5. قسم السجلات الطبية والتشخيصات (Medical Records & Visits) ===
            'view_medical_records',   // للطبيب لرؤية تاريخ المريض
            'create_medical_records', // للطبيب لإنشاء تشخيص جديد
            'edit_medical_records',
            'view_patient_history',   // للمريض للاطلاع على ملفه الشخصي

            // === 6. قسم الروشتات والوصفات الطبية (Prescriptions) ===
            'view_prescriptions',
            'create_prescriptions',   // حصرياً للطبيب
            'edit_prescriptions',
            'dispense_prescriptions', // حصرياً للصيدلاني لتغيير حالة الروشتة إلى "تم الصرف"

            // === 7. قسم المختبرات والتحاليل الطبية (Laboratory & Lab Requests) ===
            'view_lab_requests',
            'create_lab_requests',   // الطبيب يطلب تحليلاً للمريض
            'upload_lab_results',    // المخبري يرفع ملف التحليل والنتيجة الرقمية
            'approve_lab_results',   // المخبري المسؤول أو الطبيب يعتمد النتيجة

            // === 8. قسم الأمراض المزمنة والحساسية (Chronic Diseases & Allergies) ===
            'manage_allergies',
            'manage_chronic_diseases',
        ];

        // 3. التغذية الذكية باستخدام firstOrCreate لمنع التكرار عند إعادة التشغيل
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }
    }
}
